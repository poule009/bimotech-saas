<?php

namespace Database\Factories;

use App\Models\Contrat;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaiementFactory extends Factory
{
    public function definition(): array
    {
        $contrat = Contrat::factory()->create();

        return [
            'agency_id'                => $contrat->agency_id,
            'contrat_id'               => $contrat->id,
            'periode'                  => now()->startOfMonth()->toDateString(),
            'montant_encaisse'         => 250000,
            'mode_paiement'            => 'virement',
            'taux_commission_applique' => 10.00,
            'commission_agence'        => 25000,
            'tva_commission'           => 4500,
            'commission_ttc'           => 29500,
            'net_proprietaire'         => 220500,
            'caution_percue'           => 0,
            'est_premier_paiement'     => false,
            'date_paiement'            => now()->toDateString(),
            'reference_paiement'       => 'QUITT-TEST-' . fake()->unique()->numerify('####'),
            'statut'                   => 'valide',
        ];
    }
}