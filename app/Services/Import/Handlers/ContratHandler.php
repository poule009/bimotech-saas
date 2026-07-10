<?php

namespace App\Services\Import\Handlers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\ImportBatch;
use App\Services\Import\CodeSequencer;
use Illuminate\Support\Facades\DB;

/**
 * Import des baux DÉJÀ en cours.
 *
 * Anti-rétroactif : aucune quittance n'est générée ici, même si date_debut est dans
 * le passé. La commande rent:generate (idempotente, mois courant uniquement) prendra
 * le relais le 1er du mois suivant → pas de faux historique de retard au démarrage.
 */
class ContratHandler extends AbstractImportHandler
{
    public function type(): string { return 'contrats'; }

    public function templateHeaders(): array
    {
        return ['code_bien', 'code_locataire', 'loyer', 'date_debut'];
    }

    public function templateSample(): array
    {
        return ['B-0001', 'L-0001', '450000', '2025-01-01'];
    }

    public function previewColumns(): array
    {
        return [
            'code_bien'      => 'Code bien',
            'bien'           => 'Bien trouvé',
            'code_locataire' => 'Code locataire',
            'locataire'      => 'Locataire trouvé',
        ];
    }

    public function validate(array $row, int $line, array &$ctx): array
    {
        $codeBien = strtoupper((string) $this->val($row, 'code_bien'));
        $codeLoc  = strtoupper((string) $this->val($row, 'code_locataire'));
        $loyer    = $row['loyer'] ?? '';
        $dateDebut = $this->parseDate($row, 'date_debut');

        $display = [
            'code_bien'      => $codeBien ?: '—',
            'bien'           => '—',
            'code_locataire' => $codeLoc ?: '—',
            'locataire'      => '—',
        ];

        if ($codeBien === '') {
            return $this->erreur($line, 'Le code bien est obligatoire.', $display);
        }
        if ($codeLoc === '') {
            return $this->erreur($line, 'Le code locataire est obligatoire.', $display);
        }

        $bien = $this->biens($ctx)[$codeBien] ?? null;
        if (! $bien) {
            return $this->erreur($line, "Code bien « {$codeBien} » introuvable.", $display);
        }
        $display['bien'] = $bien['titre'];

        $loc = $this->locataires($ctx)[$codeLoc] ?? null;
        if (! $loc) {
            return $this->erreur($line, "Code locataire « {$codeLoc} » introuvable.", $display);
        }
        $display['locataire'] = $loc['name'];

        // Un bien ne peut pas être loué deux fois (base OU déjà vu dans le fichier).
        if (! empty($bien['occupe']) || isset($ctx['seen_biens'][$bien['id']])) {
            return $this->erreur($line, "Le bien « {$bien['titre']} » est déjà sous contrat actif.", $display);
        }

        if (! $dateDebut) {
            return $this->erreur($line, 'La date de début est manquante ou invalide (format attendu : AAAA-MM-JJ).', $display);
        }

        // Loyer : la valeur du fichier prime, sinon le loyer de référence du bien.
        $loyerNu = is_numeric($loyer) && (float) $loyer > 0
            ? (float) $loyer
            : (float) $bien['loyer_mensuel'];
        if ($loyerNu <= 0) {
            return $this->erreur($line, 'Le loyer doit être un nombre positif.', $display);
        }

        $ctx['seen_biens'][$bien['id']] = true;

        return $this->ok($line, $display, [
            'bien_id'      => $bien['id'],
            'locataire_id' => $loc['user_id'],
            'loyer_nu'     => $loyerNu,
            'date_debut'   => $dateDebut,
        ]);
    }

    public function create(array $data, ImportBatch $batch, CodeSequencer $seq, int &$sequence): array
    {
        $contrat = Contrat::create([
            'bien_id'         => $data['bien_id'],
            'locataire_id'    => $data['locataire_id'],
            'loyer_nu'        => $data['loyer_nu'],
            'date_debut'      => $data['date_debut'],
            'type_bail'       => 'habitation',
            'statut'          => 'actif',
            'import_batch_id' => $batch->id,
        ]);

        // Le bien passe à « loué » (aucune quittance générée — cf. en-tête de classe).
        Bien::withoutGlobalScopes()->where('id', $data['bien_id'])->update(['statut' => 'loue']);

        return ['id' => $contrat->id, 'code' => null, 'label' => 'Bail ' . $contrat->reference_bail_affichee];
    }

    /** Map code_import → ['id','titre','loyer_mensuel','occupe'] des biens de l'agence. */
    private function biens(array &$ctx): array
    {
        if (! isset($ctx['biens'])) {
            $rows = DB::table('biens')
                ->where('biens.agency_id', $this->agencyId)
                ->whereNotNull('biens.code_import')
                ->whereNull('biens.deleted_at')
                ->select('biens.id', 'biens.code_import', 'biens.titre', 'biens.loyer_mensuel')
                ->get();

            // Biens déjà occupés par un contrat actif.
            $occupes = DB::table('contrats')
                ->where('contrats.agency_id', $this->agencyId)
                ->where('contrats.statut', 'actif')
                ->whereNull('contrats.deleted_at')
                ->pluck('bien_id')
                ->flip();

            $ctx['biens'] = [];
            foreach ($rows as $r) {
                $ctx['biens'][strtoupper($r->code_import)] = [
                    'id'            => $r->id,
                    'titre'         => $r->titre,
                    'loyer_mensuel' => $r->loyer_mensuel,
                    'occupe'        => isset($occupes[$r->id]),
                ];
            }
        }
        return $ctx['biens'];
    }

    /** Map code_import → ['user_id','name'] des locataires de l'agence. */
    private function locataires(array &$ctx): array
    {
        if (! isset($ctx['locataires'])) {
            $rows = DB::table('locataires')
                ->join('users', 'users.id', '=', 'locataires.user_id')
                ->where('users.agency_id', $this->agencyId)
                ->whereNotNull('locataires.code_import')
                ->whereNull('locataires.deleted_at')
                ->select('locataires.code_import', 'locataires.user_id', 'users.name')
                ->get();

            $ctx['locataires'] = [];
            foreach ($rows as $r) {
                $ctx['locataires'][strtoupper($r->code_import)] = [
                    'user_id' => $r->user_id,
                    'name'    => $r->name,
                ];
            }
        }
        return $ctx['locataires'];
    }
}
