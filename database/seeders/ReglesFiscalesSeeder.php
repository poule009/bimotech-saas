<?php

namespace Database\Seeders;

use App\Models\RegleFiscale;
use Illuminate\Database\Seeder;

/**
 * Persiste le catalogue des règles fiscales (config/fiscal_sources.php)
 * dans la table `regles_fiscales`.
 *
 * Idempotent : upsert par `cle`. Rejouable après migrate:fresh sans doublon.
 * La date de dernière vérification globale du catalogue est reprise depuis
 * la config (`fiscal_sources.date_verification`).
 */
class ReglesFiscalesSeeder extends Seeder
{
    public function run(): void
    {
        $dateVerification = config('fiscal_sources.date_verification');
        $regles           = config('fiscal_sources.regles', []);

        foreach ($regles as $cle => $regle) {
            RegleFiscale::updateOrCreate(
                ['cle' => $cle],
                [
                    'categorie'         => $regle['categorie'] ?? 'tva',
                    'titre'             => $regle['titre'],
                    'description'       => $regle['description'],
                    'statut'            => $regle['statut'],
                    'sources'           => $regle['sources'] ?? [],
                    'note'              => $regle['note'] ?? null,
                    'date_verification' => $dateVerification,
                ]
            );
        }
    }
}
