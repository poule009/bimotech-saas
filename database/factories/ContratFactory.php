<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContratFactory extends Factory
{
    public function definition(): array
    {
        $bien = Bien::factory()->create();
        $locataire = User::factory()->create([
            'role' => 'locataire',
            'agency_id' => $bien->agency_id,
        ]);

        return [
            'agency_id'         => $bien->agency_id ?? Agency::query()->value('id') ?? Agency::factory()->create()->id,
            'bien_id'           => $bien->id,
            'locataire_id'      => $locataire->id,
            'date_debut'        => now()->startOfMonth()->toDateString(),
            'date_fin'          => null,
            'loyer_contractuel' => 250000,
            'caution'           => 250000,
            'statut'            => 'actif',
        ];
    }
}