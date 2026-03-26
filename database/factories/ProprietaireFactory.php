<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProprietaireFactory extends Factory
{
    public function definition(): array
    {
        $quartiers = ['Almadies', 'Plateau', 'Mermoz', 'Fann', 'Point E', 'Sacré-Cœur'];

        return [
            'user_id'                => User::factory()->create([
                'role' => 'proprietaire'
            ])->id,
            'cni'                    => 'SN-' . fake()->numerify('#######'),
            'date_naissance'         => fake()->dateTimeBetween('-65y', '-30y')->format('Y-m-d'),
            'genre'                  => fake()->randomElement(['homme', 'femme']),
            'nationalite'            => 'Sénégalaise',
            'telephone_secondaire'   => '+221 7' . fake()->numerify('# ### ## ##'),
            'adresse_domicile'       => fake()->streetAddress(),
            'ville'                  => 'Dakar',
            'quartier'               => fake()->randomElement($quartiers),
            'mode_paiement_prefere'  => fake()->randomElement(['virement', 'mobile_money', 'wave']),
            'numero_wave'            => '+221 7' . fake()->numerify('# ### ## ##'),
            'assujetti_tva'          => fake()->boolean(20),
        ];
    }
}