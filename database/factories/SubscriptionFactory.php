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
            // Tier par défaut = 'agence' (accès complet) : la majorité des tests
            // exercent une feature et veulent y accéder. La colonne plan_niveau a été
            // introduite le 20/05/2026 ; les fixtures antérieures ne la fixaient pas,
            // d'où un fallback 'legacy'→pro qui bloquait les features tier 'agence'
            // (fiscalité, bilans, logs). Les tests de gating peuvent l'override.
            'plan_niveau'         => 'agence',
            'plan'                => null,
            'montant_paye'        => null,
            'date_debut_abonnement' => null,
            'date_fin_abonnement' => null,
            'reference_paytech'   => null,
            'rappel_7j_envoye'    => false,
            'rappel_1j_envoye'    => false,
        ];
    }

    public function actif(string $plan = 'mensuel', string $planNiveau = 'pro'): static
    {
        $durees = \App\Models\Subscription::DUREES_MOIS;
        return $this->state(fn () => [
            'statut'                => 'actif',
            'plan'                  => $plan,
            'plan_niveau'           => $planNiveau,
            'montant_paye'          => \App\Models\Subscription::TARIFS[$planNiveau][$plan],
            'date_debut_abonnement' => now(),
            'date_fin_abonnement'   => now()->addMonths($durees[$plan]),
        ]);
    }
}
