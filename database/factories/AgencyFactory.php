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
        ];
    }
}
