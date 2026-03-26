<?php

namespace Database\Factories;

use App\Models\Bien;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContratFactory extends Factory
{
    public function definition(): array
    {
        return [
            'bien_id'           => Bien::factory(),
            'locataire_id'      => User::factory()->create(['role' => 'locataire'])->id,
            'date_debut'        => now()->startOfMonth()->toDateString(),
            'date_fin'          => null,
            'loyer_contractuel' => 250000,
            'caution'           => 250000,
            'statut'            => 'actif',
        ];
    }
}