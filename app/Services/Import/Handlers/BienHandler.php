<?php

namespace App\Services\Import\Handlers;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\ImportBatch;
use App\Services\Import\CodeSequencer;
use Illuminate\Support\Facades\DB;

class BienHandler extends AbstractImportHandler
{
    public function type(): string { return 'biens'; }

    public function templateHeaders(): array
    {
        return ['nom', 'type', 'adresse', 'ville', 'quartier', 'loyer', 'code_proprietaire'];
    }

    public function templateSample(): array
    {
        return ['Villa Almadies 12', 'villa', '12 Route des Almadies', 'Dakar', 'Almadies', '450000', 'P-0001'];
    }

    public function previewColumns(): array
    {
        return [
            'nom'               => 'Nom du bien',
            'code_proprietaire' => 'Code propriétaire',
            'proprietaire'      => 'Propriétaire trouvé',
        ];
    }

    public function validate(array $row, int $line, array &$ctx): array
    {
        $nom     = $this->val($row, 'nom');
        $type    = strtolower((string) $this->val($row, 'type'));
        $loyer   = $row['loyer'] ?? '';
        $adresse = $this->val($row, 'adresse');
        $codeProp = strtoupper((string) $this->val($row, 'code_proprietaire'));

        $display = [
            'nom'               => $nom ?? '—',
            'code_proprietaire' => $codeProp ?: '—',
            'proprietaire'      => '—',
        ];

        if (! $nom) {
            return $this->erreur($line, 'Le nom du bien est obligatoire.', $display);
        }
        if (! array_key_exists($type, Bien::TYPES)) {
            return $this->erreur($line, "Type « {$type} » invalide. Valeurs : " . implode(', ', array_keys(Bien::TYPES)) . '.', $display);
        }
        if (! is_numeric($loyer) || (float) $loyer <= 0) {
            return $this->erreur($line, 'Le loyer doit être un nombre positif.', $display);
        }
        if (! $adresse) {
            return $this->erreur($line, "L'adresse est obligatoire.", $display);
        }
        if ($codeProp === '') {
            return $this->erreur($line, 'Le code propriétaire est obligatoire.', $display);
        }

        // Résolution RÉELLE en base : le code doit correspondre à un propriétaire existant.
        $prop = $this->proprietaires($ctx)[$codeProp] ?? null;
        if (! $prop) {
            return $this->erreur($line, "Code propriétaire « {$codeProp} » introuvable. Vérifiez le fichier de retour de l'étape Propriétaires.", $display);
        }
        $display['proprietaire'] = $prop['name'];

        // Limite d'unités du plan.
        $ctx['bien_ajoutes'] = ($ctx['bien_ajoutes'] ?? 0);
        $limite = $this->limiteUnites($ctx);
        if ($limite !== null && ($ctx['bien_actuelles'] + $ctx['bien_ajoutes']) >= $limite) {
            return $this->erreur($line, "Limite du plan atteinte ({$limite} unités). Bien ignoré.", $display);
        }
        $ctx['bien_ajoutes']++;

        return $this->ok($line, $display, [
            'titre'           => $nom,
            'type'            => $type,
            'adresse'         => $adresse,
            'ville'           => $this->val($row, 'ville') ?? 'Dakar',
            'quartier'        => $this->val($row, 'quartier'),
            'loyer_mensuel'   => (float) $loyer,
            'proprietaire_id' => $prop['user_id'],
        ]);
    }

    public function create(array $data, ImportBatch $batch, CodeSequencer $seq, int &$sequence): array
    {
        $code = $seq->formater('biens', $sequence);

        $bien = Bien::create([
            'agency_id'       => $this->agencyId,
            'proprietaire_id' => $data['proprietaire_id'],
            'reference'       => Bien::generateReference($this->agencyId),
            'titre'           => $data['titre'],
            'type'            => $data['type'],
            'adresse'         => $data['adresse'],
            'ville'           => $data['ville'],
            'quartier'        => $data['quartier'],
            'loyer_mensuel'   => $data['loyer_mensuel'],
            'statut'          => 'disponible',
            'code_import'     => $code,
            'import_batch_id' => $batch->id,
        ]);

        $sequence++;

        return ['id' => $bien->id, 'code' => $code, 'label' => $data['titre']];
    }

    /** Map code_import → ['user_id','name'] des propriétaires de l'agence. */
    private function proprietaires(array &$ctx): array
    {
        if (! isset($ctx['proprietaires'])) {
            $rows = DB::table('proprietaires')
                ->join('users', 'users.id', '=', 'proprietaires.user_id')
                ->where('users.agency_id', $this->agencyId)
                ->whereNotNull('proprietaires.code_import')
                ->whereNull('proprietaires.deleted_at')
                ->select('proprietaires.code_import', 'proprietaires.user_id', 'users.name')
                ->get();

            $ctx['proprietaires'] = [];
            foreach ($rows as $r) {
                $ctx['proprietaires'][strtoupper($r->code_import)] = [
                    'user_id' => $r->user_id,
                    'name'    => $r->name,
                ];
            }
        }
        return $ctx['proprietaires'];
    }

    private function limiteUnites(array &$ctx): ?int
    {
        if (! array_key_exists('bien_limite', $ctx)) {
            $agency = Agency::with('subscription')->find($this->agencyId);
            $plan   = $agency?->subscription?->plan_niveau ?? 'legacy';
            $ctx['bien_limite'] = match ($plan) {
                'starter'       => 15,
                'pro', 'legacy' => 50,
                'agence'        => null,
                default         => 50,
            };
            $ctx['bien_actuelles'] = $agency ? $agency->nbUnitesActives() : 0;
        }
        return $ctx['bien_limite'];
    }
}
