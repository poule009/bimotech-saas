<?php

namespace App\Services\Import;

use App\Imports\RawRowsImport;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\ImportBatch;
use App\Models\User;
use App\Services\Import\Handlers\AbstractImportHandler;
use App\Services\Import\Handlers\BienHandler;
use App\Services\Import\Handlers\ContratHandler;
use App\Services\Import\Handlers\LocataireHandler;
use App\Services\Import\Handlers\ProprietaireHandler;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Orchestrateur du module Import : aperçu (sans écriture), commit (depuis l'aperçu),
 * annulation en bloc, et calcul de l'état du stepper.
 */
class ImportManager
{
    /** Ordre des étapes du stepper. */
    public const ETAPES = ['proprietaires', 'biens', 'locataires', 'contrats'];

    public function __construct(private CodeSequencer $seq) {}

    public function handler(string $type, int $agencyId): AbstractImportHandler
    {
        return match ($type) {
            'proprietaires' => new ProprietaireHandler($agencyId),
            'biens'         => new BienHandler($agencyId),
            'locataires'    => new LocataireHandler($agencyId),
            'contrats'      => new ContratHandler($agencyId),
            default         => throw new \InvalidArgumentException("Type d'import inconnu : {$type}"),
        };
    }

    // ── Aperçu ───────────────────────────────────────────────────────────────

    /**
     * Parse le fichier, valide chaque ligne (SANS écrire), et crée un lot en statut
     * « preview ». Remplace tout aperçu précédent en attente pour ce type.
     */
    public function preview(string $type, UploadedFile $file, int $agencyId): ImportBatch
    {
        $handler = $this->handler($type, $agencyId);

        $raw = new RawRowsImport();
        Excel::import($raw, $file);

        $ctx     = [];
        $rows    = [];
        $valides = $erreurs = $doublons = 0;

        foreach ($raw->rows as $index => $row) {
            $assoc = is_array($row) ? $row : $row->toArray();

            if ($handler->estLigneVide($assoc)) {
                continue;
            }

            $line    = $index + 2; // +1 entête, +1 base 0
            $verdict = $handler->validate($assoc, $line, $ctx);
            $rows[]  = $verdict;

            match ($verdict['status']) {
                'valid'     => $valides++,
                'duplicate' => $doublons++,
                default     => $erreurs++,
            };
        }

        // Un seul aperçu en attente par type : on purge les précédents.
        ImportBatch::where('agency_id', $agencyId)
            ->where('type', $type)
            ->where('statut', 'preview')
            ->delete();

        return ImportBatch::create([
            'agency_id'         => $agencyId,
            'user_id'           => Auth::id(),
            'type'              => $type,
            'statut'            => 'preview',
            'original_filename' => $file->getClientOriginalName(),
            'rows'              => $rows,
            'nb_total'          => count($rows),
            'nb_valides'        => $valides,
            'nb_erreurs'        => $erreurs,
            'nb_doublons'       => $doublons,
        ]);
    }

    // ── Commit ─────────────────────────────────────────────────────────────────

    /** Insère les lignes valides du lot. Idempotent : ne committe qu'un lot « preview ». */
    public function commit(ImportBatch $batch): ImportBatch
    {
        abort_unless($batch->statut === 'preview', 400, 'Ce lot a déjà été traité.');

        $handler = $this->handler($batch->type, $batch->agency_id);

        DB::transaction(function () use ($batch, $handler) {
            $sequence = $this->seq->prochaineSequence($batch->agency_id, $batch->type);
            $codes    = [];
            $crees    = 0;

            foreach ($batch->lignesValides() as $ligne) {
                $res = $handler->create($ligne['data'], $batch, $this->seq, $sequence);
                if (! empty($res['code'])) {
                    $codes[] = ['code' => $res['code'], 'label' => $res['label']];
                }
                $crees++;
            }

            $batch->update([
                'statut'       => 'committed',
                'codes'        => $codes,
                'nb_crees'     => $crees,
                'committed_at' => now(),
            ]);
        });

        return $batch->refresh();
    }

    // ── Annulation ──────────────────────────────────────────────────────────────

    /**
     * Vérifie qu'un lot committé est annulable : aucun dépendant, rien de modifié
     * depuis l'import. Retourne null si annulable, sinon la raison du blocage.
     */
    public function raisonBlocageAnnulation(ImportBatch $batch): ?string
    {
        if ($batch->statut !== 'committed') {
            return 'Ce lot ne peut pas être annulé.';
        }

        return match ($batch->type) {
            'proprietaires' => $this->blocageProprietaires($batch),
            'locataires'    => $this->blocageLocataires($batch),
            'biens'         => $this->blocageBiens($batch),
            'contrats'      => $this->blocageContrats($batch),
            default         => 'Type inconnu.',
        };
    }

    public function annulable(ImportBatch $batch): bool
    {
        return $this->raisonBlocageAnnulation($batch) === null;
    }

    /** Supprime réellement les enregistrements du lot (libère codes/emails/téléphones). */
    public function undo(ImportBatch $batch): void
    {
        $raison = $this->raisonBlocageAnnulation($batch);
        abort_unless($raison === null, 422, $raison ?? 'Annulation impossible.');

        DB::transaction(function () use ($batch) {
            switch ($batch->type) {
                case 'proprietaires':
                case 'locataires':
                    $table   = $batch->type;
                    $userIds = DB::table($table)->where('import_batch_id', $batch->id)->pluck('user_id');
                    DB::table($table)->where('import_batch_id', $batch->id)->delete();
                    User::withoutGlobalScopes()->whereIn('id', $userIds)->forceDelete();
                    break;

                case 'biens':
                    Bien::withoutGlobalScopes()->where('import_batch_id', $batch->id)->forceDelete();
                    break;

                case 'contrats':
                    $contrats = Contrat::withoutGlobalScopes()->where('import_batch_id', $batch->id)->get();
                    foreach ($contrats as $contrat) {
                        // Rendre le bien de nouveau disponible s'il n'a pas d'autre bail actif.
                        $autreActif = Contrat::withoutGlobalScopes()
                            ->where('bien_id', $contrat->bien_id)
                            ->where('id', '!=', $contrat->id)
                            ->where('statut', 'actif')
                            ->exists();
                        if (! $autreActif) {
                            Bien::withoutGlobalScopes()->where('id', $contrat->bien_id)->update(['statut' => 'disponible']);
                        }
                        $contrat->forceDelete();
                    }
                    break;
            }

            $batch->update(['statut' => 'annule', 'annule_at' => now()]);
        });
    }

    // ── Règles de blocage d'annulation ──────────────────────────────────────────

    private function blocageProprietaires(ImportBatch $batch): ?string
    {
        $userIds = DB::table('proprietaires')->where('import_batch_id', $batch->id)->pluck('user_id');
        if ($userIds->isEmpty()) return 'Aucune donnée à annuler.';

        $nbBiens = DB::table('biens')
            ->whereIn('proprietaire_id', $userIds)
            ->whereNull('deleted_at')
            ->count();
        if ($nbBiens > 0) {
            return "Des biens sont déjà rattachés à ces propriétaires ({$nbBiens}). Annulez d'abord l'import des biens.";
        }

        if ($this->profilsModifies('proprietaires', $batch->id)) {
            return 'Des propriétaires ont été modifiés depuis l\'import — annulation bloquée.';
        }
        return null;
    }

    private function blocageLocataires(ImportBatch $batch): ?string
    {
        $userIds = DB::table('locataires')->where('import_batch_id', $batch->id)->pluck('user_id');
        if ($userIds->isEmpty()) return 'Aucune donnée à annuler.';

        $nbContrats = DB::table('contrats')
            ->whereIn('locataire_id', $userIds)
            ->whereNull('deleted_at')
            ->count();
        if ($nbContrats > 0) {
            return "Des contrats sont déjà rattachés à ces locataires ({$nbContrats}). Annulez d'abord l'import des contrats.";
        }

        if ($this->profilsModifies('locataires', $batch->id)) {
            return 'Des locataires ont été modifiés depuis l\'import — annulation bloquée.';
        }
        return null;
    }

    private function blocageBiens(ImportBatch $batch): ?string
    {
        $bienIds = DB::table('biens')->where('import_batch_id', $batch->id)->pluck('id');
        if ($bienIds->isEmpty()) return 'Aucune donnée à annuler.';

        $nbContrats = DB::table('contrats')
            ->whereIn('bien_id', $bienIds)
            ->whereNull('deleted_at')
            ->count();
        if ($nbContrats > 0) {
            return "Des contrats portent déjà sur ces biens ({$nbContrats}). Annulez d'abord l'import des contrats.";
        }

        // Pas de contrôle updated_at ici : le statut d'un bien est légitimement modifié
        // par l'import/annulation de contrats (→ loué / → disponible). La garde de
        // dépendance ci-dessus (aucun contrat rattaché) est la vraie protection.
        return null;
    }

    private function blocageContrats(ImportBatch $batch): ?string
    {
        $contratIds = DB::table('contrats')->where('import_batch_id', $batch->id)->pluck('id');
        if ($contratIds->isEmpty()) return 'Aucune donnée à annuler.';

        $nbPaiements = DB::table('paiements')->whereIn('contrat_id', $contratIds)->count();
        if ($nbPaiements > 0) {
            return "Des quittances existent déjà pour ces contrats ({$nbPaiements}). Annulation bloquée.";
        }

        if ($this->profilsModifies('contrats', $batch->id)) {
            return 'Des contrats ont été modifiés depuis l\'import — annulation bloquée.';
        }
        return null;
    }

    /** True si une ligne du lot a été modifiée après sa création (updated_at > created_at). */
    private function profilsModifies(string $table, int $batchId): bool
    {
        return DB::table($table)
            ->where('import_batch_id', $batchId)
            ->whereColumn('updated_at', '>', 'created_at')
            ->exists();
    }

    // ── État du stepper ──────────────────────────────────────────────────────────

    /**
     * Pour chaque étape : done (≥1 lot committé), unlocked (prérequis réels remplis),
     * et le dernier lot committé (pour proposer téléchargement des codes / annulation).
     */
    public function etat(int $agencyId): array
    {
        $committes = [];
        foreach (self::ETAPES as $type) {
            $committes[$type] = ImportBatch::where('agency_id', $agencyId)
                ->where('type', $type)
                ->where('statut', 'committed')
                ->latest('committed_at')
                ->first();
        }

        $done = fn (string $t) => $committes[$t] !== null;

        // Dépendances réelles (le brief insiste : locataires est indépendant).
        $unlocked = [
            'proprietaires' => true,
            'biens'         => $done('proprietaires'),
            'locataires'    => true,
            'contrats'      => $done('biens') && $done('locataires'),
        ];

        $etat = [];
        foreach (self::ETAPES as $type) {
            $etat[$type] = [
                'done'     => $done($type),
                'unlocked' => $unlocked[$type],
                'batch'    => $committes[$type],
            ];
        }
        return $etat;
    }
}
