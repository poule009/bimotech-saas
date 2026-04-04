<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureAgencyIsActive — Middleware de garde multi-tenant.
 *
 * Vérifie à chaque requête authentifiée que :
 * 1. L'utilisateur appartient à une agence existante
 * 2. L'agence est active (non suspendue / non expirée)
 * 3. Le rôle de l'utilisateur est valide
 *
 * À enregistrer dans bootstrap/app.php (Laravel 11) ou Kernel.php (Laravel 10)
 * sur le groupe de middlewares 'web' authentifiés.
 */
class EnsureAgencyIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Pas d'utilisateur connecté → laisser passer (géré par auth middleware)
        if (! $user) {
            return $next($request);
        }

        // Le superadmin n'est rattaché à aucune agence
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // Vérification de l'existence de l'agence
        if (! $user->agency_id || ! $user->agency) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte n\'est rattaché à aucune agence.']);
        }

        $agency = $user->agency;

        // Vérification du statut de l'agence (suspendue pour impayé, par ex.)
        if ($agency->statut === 'suspendu') {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre agence a été suspendue. Contactez BimoTech.']);
        }

        // Vérification de l'expiration de l'abonnement SaaS
        if ($agency->abonnement_expire_le && $agency->abonnement_expire_le->isPast()) {
            // On redirige vers une page de renouvellement, pas de logout brutal
            if (! $request->routeIs('abonnement.*')) {
                return redirect()->route('abonnement.expire');
            }
        }

        return $next($request);
    }
}