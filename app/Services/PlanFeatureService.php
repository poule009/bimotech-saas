<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class PlanFeatureService
{
    /**
     * Vérifie si l'utilisateur courant peut accéder à une feature.
     * Superadmin = accès total. Feature inconnue = accès accordé (pas de blocage).
     */
    public function canAccess(string $feature): bool
    {
        $user = Auth::user();

        if (! $user || $user->role === 'superadmin') {
            return true;
        }

        $subscription = $user->agency?->subscription;

        if (! $subscription) {
            return false;
        }

        $plans = config('plans');

        if (! isset($plans['features'][$feature])) {
            return true;
        }

        $niveauEffectif = $plans['niveau_effectif'][$subscription->plan_niveau ?? 'legacy'] ?? 'starter';
        $niveauRequis   = $plans['features'][$feature];
        $hierarchy      = $plans['hierarchy'];

        return array_search($niveauEffectif, $hierarchy) >= array_search($niveauRequis, $hierarchy);
    }

    /** Niveau de plan minimum requis pour une feature (ex: 'pro'). */
    public function requiredPlan(string $feature): ?string
    {
        return config("plans.features.{$feature}");
    }

    /** Label lisible du plan requis (ex: 'Pro'). */
    public function requiredPlanLabel(string $feature): string
    {
        $plan = $this->requiredPlan($feature);

        return config("plans.labels.{$plan}", ucfirst((string) $plan));
    }
}
