<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocataireFactory extends Factory
{
    public function definition(): array
    {
        $quartiers   = ['Médina', 'Grand Dakar', 'Pikine', 'Guédiawaye', 'Parcelles', 'HLM'];
        $professions = ['Ingénieur', 'Commerçant', 'Enseignant', 'Médecin', 'Fonctionnaire', 'Entrepreneur'];

        return [
            'user_id'                  => User::factory()->create([
                'role' => 'locataire'
            ])->id,
            'cni'                      => 'SN-' . fake()->numerify('#######'),
            'date_naissance'           => fake()->dateTimeBetween('-55y', '-20y')->format('Y-m-d'),
            'genre'                    => fake()->randomElement(['homme', 'femme']),
            'nationalite'              => 'Sénégalaise',
            'profession'               => fake()->randomElement($professions),
            'employeur'                => fake()->company(),
            'revenu_mensuel'           => fake()->randomElement([200000, 350000, 500000, 750000]),
            'contact_urgence_nom'      => fake()->name(),
            'contact_urgence_tel'      => '+221 7' . fake()->numerify('# ### ## ##'),
            'contact_urgence_lien'     => fake()->randomElement(['père', 'mère', 'conjoint', 'frère']),
            'adresse_precedente'       => fake()->streetAddress(),
            'ville'                    => 'Dakar',
            'quartier'                 => fake()->randomElement($quartiers),
            'cni_verified'             => fake()->boolean(70),
            'justif_revenus_fourni'    => fake()->boolean(60),
        ];
    }
}