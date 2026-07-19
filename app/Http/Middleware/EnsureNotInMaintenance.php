<?php

namespace App\Http\Middleware;

use App\Support\PlatformSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mode maintenance (réglage plateforme) — blocage TOTAL côté agences.
 *
 * Quand le mode maintenance est actif, tout compte d'agence (admin, propriétaire,
 * locataire) voit une page d'attente (HTTP 503). Le Super Admin reste pleinement
 * accessible — c'est lui qui pilote l'intervention. Une session d'impersonation en
 * cours (support d'un admin principal) reste également autorisée.
 *
 * Enregistré sur le groupe « web » (bootstrap/app.php). La lecture du réglage passe
 * par le cache de PlatformSettings : coût négligeable sur le chemin de chaque requête.
 */
class EnsureNotInMaintenance
{
    public function __construct(private PlatformSettings $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Réglage inactif → rien à faire (cas nominal).
        if (! $this->settings->maintenanceActive()) {
            return $next($request);
        }

        $user = $request->user();

        // Invité : on laisse passer (login, pages publiques). Il sera bloqué à la
        // requête suivante s'il se connecte avec un compte d'agence.
        if (! $user) {
            return $next($request);
        }

        // Super Admin : jamais bloqué (il pilote la maintenance).
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // Support en cours (admin principal en impersonation) : autorisé.
        if ($request->session()->has('impersonating_id')) {
            return $next($request);
        }

        // Laisser la déconnexion possible pour un compte d'agence bloqué.
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        return response()
            ->view('maintenance', ['message' => $this->settings->maintenanceMessage()], 503)
            ->header('Retry-After', '3600');
    }
}
