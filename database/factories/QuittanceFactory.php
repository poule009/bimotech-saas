<?php

namespace Database\Factories;

// use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Quittance;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuittanceFactory extends Factory
{
    protected $model = Quittance::class;

    public function definition(): array
    {
        $paiement = Paiement::factory()->create(['statut' => 'valide']);

        return [
            'agency_id'     => $paiement->agency_id,
            'paiement_id'   => $paiement->id,
            'contrat_id'    => $paiement->contrat_id,
            'numero'        => 'QT-01-' . now()->year . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'date_emission' => now()->toDateString(),
            'mois_concerne' => now()->format('Y-m'),
            'generee_par'   => null,
        ];
    }
}