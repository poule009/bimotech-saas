<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = Auth::user();

        if (! $user || $user->role === 'superadmin') {
            return $next($request);
        }

        $subscription = $user->agency?->subscription;

        // Pas d'abonnement → CheckSubscription gère déjà ce cas
        if (! $subscription) {
            return $next($request);
        }

        $plans = config('plans');

        // Clé de feature inconnue → on laisse passer sans bloquer
        if (! isset($plans['features'][$feature])) {
            return $next($request);
        }

        $niveauEffectif = $plans['niveau_effectif'][$subscription->plan_niveau ?? 'legacy'] ?? 'starter';
        $niveauRequis   = $plans['features'][$feature];
        $hierarchy      = $plans['hierarchy'];

        $positionActuelle = array_search($niveauEffectif, $hierarchy);
        $positionRequise  = array_search($niveauRequis,   $hierarchy);

        if ($positionActuelle >= $positionRequise) {
            return $next($request);
        }

        // Requête AJAX → réponse JSON sans redirection
        if ($request->expectsJson()) {
            $label = $plans['labels'][$niveauRequis] ?? ucfirst($niveauRequis);
            return response()->json([
                'message' => "Fonctionnalité disponible à partir du plan {$label}.",
            ], 403);
        }

        return redirect()->route('subscription.upgrade-required')
            ->with('required_plan', $niveauRequis);
    }
}
