<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BienFactory extends Factory
{
    public function definition(): array
    {
        $proprietaire = User::factory()->create(['role' => 'proprietaire']);
        $agencyId = $proprietaire->agency_id ?? Agency::query()->value('id') ?? Agency::factory()->create()->id;

        return [
            'agency_id'        => $agencyId,
            'proprietaire_id'  => $proprietaire->id,
            'reference'       => 'BIEN-TEST-' . fake()->unique()->numerify('###'),
            'type'            => fake()->randomElement(['Appartement', 'Villa', 'Studio', 'Bureau']),
            'adresse'         => fake()->streetAddress(),
            'ville'           => 'Dakar',
            'surface_m2'      => fake()->numberBetween(30, 200),
            'nombre_pieces'   => fake()->numberBetween(1, 6),
            'loyer_mensuel'   => fake()->randomElement([120000, 250000, 350000, 600000]),
            'taux_commission' => 10.00,
            'statut'          => 'disponible',
        ];
    }
}