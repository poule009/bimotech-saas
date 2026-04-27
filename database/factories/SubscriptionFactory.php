<?php

namespace Database\Factories;

use App\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'agency_id'           => Agency::factory(),
            'statut'              => 'essai',
            'date_debut_essai'    => now(),
            'date_fin_essai'      => now()->addDays(30),
            'plan'                => null,
            'montant_paye'        => null,
            'date_debut_abonnement' => null,
            'date_fin_abonnement' => null,
            'reference_paytech'   => null,
            'rappel_7j_envoye'    => false,
            'rappel_1j_envoye'    => false,
        ];
    }

    public function actif(string $plan = 'mensuel'): static
    {
        return $this->state(fn () => [
            'statut'                => 'actif',
            'plan'                  => $plan,
            'montant_paye'          => \App\Models\Subscription::TARIFS[$plan],
            'date_debut_abonnement' => now(),
            'date_fin_abonnement'   => now()->addMonth(),
        ]);
    }
}
