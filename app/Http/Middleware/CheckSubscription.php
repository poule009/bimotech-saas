<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    protected array $except = [
        'login',
        'logout',
        'password/*',
        'register/agency',
        'subscription',
        'subscription/*',
        'admin/agency/settings',
        'admin/agency/logo',
        'profile',
        'profile/*',
        'home',
        'verify-email',
        'verify-email/*',
        'email/verification-notification',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        foreach ($this->except as $pattern) {
            if ($request->routeIs($pattern) || $request->is($pattern)) {
                return $next($request);
            }
        }

        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        if ($user->role === 'superadmin') {
            return $next($request);
        }

        if (! $user->agency) {
            return $next($request);
        }

        $subscription = $user->agency->subscription;

        // Pas de subscription → redirection
        if (! $subscription) {
            return redirect()->route('subscription.index')
                ->with('warning', "Aucun abonnement trouvé pour votre agence.");
        }

        // Accès autorisé si essai valide ou abonnement valide
        if ($subscription->aAcces()) {
            return $next($request);
        }

        // Tout autre cas → bloqué (expiré, annulé, essai terminé...)
        $message = match($subscription->statut) {
            'essai'  => "Votre période d'essai de 30 jours est terminée. Choisissez un abonnement pour continuer.",
            'annulé' => "Votre abonnement a été annulé. Contactez-nous pour le réactiver.",
            default  => "Votre abonnement a expiré. Renouvelez-le pour continuer à utiliser BIMO-Tech.",
        };

        return redirect()->route('subscription.index')
            ->with('warning', $message);
    }
}