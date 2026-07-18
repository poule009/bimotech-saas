<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force un collaborateur invité (must_change_password) à choisir un nouveau mot de
 * passe avant d'accéder au reste de l'application. Les routes du changement lui-même
 * et la déconnexion sont exemptées pour éviter une boucle de redirection.
 */
class ForcePasswordChange
{
    private const EXEMPTES = [
        'admin.password.force',
        'admin.password.force.update',
        // Équivalents super-admin (collaborateur interne invité) + challenge 2FA :
        // exemptés pour éviter toute boucle de redirection.
        'superadmin.password.force',
        'superadmin.password.force.update',
        'superadmin.2fa.challenge',
        'superadmin.2fa.verify',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->must_change_password && ! $request->routeIs(self::EXEMPTES)) {
            // Redirige vers l'écran de changement forcé propre au rôle.
            return redirect()->route($user->isSuperAdmin()
                ? 'superadmin.password.force'
                : 'admin.password.force');
        }

        return $next($request);
    }
}
