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
            // Pays/devise explicites : `agencies.pays` est NOT NULL sans default
            // (cf. migration add_pays_and_devise_to_agencies). Les fixer ici garantit
            // que la suite de tests existante continue de décrire le comportement
            // sénégalais — et non celui d'une agence au pays indéterminé.
            'pays' => \App\Support\Pays::DEFAUT,
            'devise' => \App\Support\Pays::devise(\App\Support\Pays::DEFAUT),
            'actif' => true,
            // Les agences de test représentent une agence enregistrée à la TVA
            // (cas courant d'un prestataire immobilier). En prod, le flag est
            // opt-in (false par défaut, cf. migration make_assujetti_tva_opt_in).
            'assujetti_tva' => true,
        ];
    }
}
