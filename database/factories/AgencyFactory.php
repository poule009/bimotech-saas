<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AgencyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Agency ' . fake()->unique()->company(),
            'slug' => fake()->unique()->slug(),
            'email' => fake()->unique()->companyEmail(),
            'telephone' => '+221 7' . fake()->numerify('## ### ## ##'),
            'adresse' => fake()->streetAddress(),
            'ville' => 'Dakar',
            'actif' => true,
            // Les agences de test représentent une agence enregistrée à la TVA
            // (cas courant d'un prestataire immobilier). En prod, le flag est
            // opt-in (false par défaut, cf. migration make_assujetti_tva_opt_in).
            'assujetti_tva' => true,
        ];
    }
}
