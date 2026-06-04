<?php

namespace App\Http\Middleware;

use App\Services\PlanFeatureService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    public function __construct(private PlanFeatureService $planFeatureService) {}

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = Auth::user();

        if (! $user || $user->role === 'superadmin') {
            return $next($request);
        }

        if ($this->planFeatureService->canAccess($feature)) {
            return $next($request);
        }

        // Requête AJAX → réponse JSON sans redirection
        if ($request->expectsJson()) {
            $plans = config('plans');
            $niveauRequis = $plans['features'][$feature] ?? null;
            $label = $niveauRequis ? ($plans['labels'][$niveauRequis] ?? ucfirst($niveauRequis)) : 'supérieur';
            return response()->json([
                'message' => "Fonctionnalité disponible à partir du plan {$label}.",
            ], 403);
        }

        return redirect()->route('subscription.upgrade-required')
            ->with('required_plan', config("plans.features.{$feature}"));
    }
}
