<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    // ✅ CORRECTION M2 : noms de routes uniquement (plus de chemins URL)
    // Avant : mélange de paths ("admin/agency/settings") et de noms de routes
    //         → certaines exclusions ne marchaient pas
    // Après : uniquement des noms de routes → fiable même si les URLs changent
    protected array $except = [
        'login',
        'logout',
        'register',
        'password.*',
        'verification.*',
        'agency.register',
        'agency.register.store',
        'subscription.*',
        'admin.agency.settings',
        'admin.agency.settings.update',
        'admin.agency.logo.delete',
        'profile.edit',
        'profile.update',
        'profile.destroy',
        'redirect.home',
        'dashboard',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs(...$this->except)) {
            return $next($request);
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

        if (! $subscription) {
            return redirect()->route('subscription.index')
                ->with('warning', "Aucun abonnement trouvé pour votre agence.");
        }

        if ($subscription->aAcces()) {
            return $next($request);
        }

        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        $message = match ($subscription->statut) {
            'essai'  => "Votre période d'essai est terminée. Abonnez-vous pour réactiver les modifications.",
            'annulé' => "Votre abonnement est annulé. Réactivez-le pour modifier vos données.",
            default  => "Votre abonnement a expiré. Renouvelez pour reprendre les modifications.",
        };

        return redirect()->route('subscription.index')->with('warning', $message);
    }
}