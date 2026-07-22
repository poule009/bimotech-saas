<?php

namespace App\Http\Middleware;

use App\Support\PlatformSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applique l'authentification à deux facteurs aux comptes super-admin.
 *
 * Deux niveaux :
 *  - 2FA ACTIVÉ  → la session doit être vérifiée (sinon → challenge).
 *  - 2FA ABSENT  → si le réglage plateforme « 2FA obligatoire »
 *                  (PlatformSettings::deuxFacteursObligatoire, défaut true) est actif,
 *                  on force l'enrôlement (redirection vers le setup). Sinon, pass-through.
 *
 * Les routes de setup / challenge / mot de passe forcé / logout sont exemptées pour
 * éviter toute boucle de redirection (elles vivent à l'intérieur de ce middleware).
 */
class Require2FA
{
    /** Routes atteignables sans 2FA vérifié — anti-boucle. */
    private const EXEMPTES = [
        'superadmin.2fa.setup',
        'superadmin.2fa.confirm',
        'superadmin.2fa.challenge',
        'superadmin.2fa.verify',
        'superadmin.password.force',
        'superadmin.password.force.update',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isSuperAdmin()) {
            return $next($request);
        }

        // Ces routes doivent rester accessibles pour permettre l'enrôlement / la
        // vérification / la sortie, sans quoi le superadmin serait bloqué en boucle.
        if ($request->routeIs(self::EXEMPTES)) {
            return $next($request);
        }

        // 2FA non encore activé : forcer l'enrôlement s'il est obligatoire.
        if (! $user->hasTwoFactorEnabled()) {
            return app(PlatformSettings::class)->deuxFacteursObligatoire()
                ? redirect()->route('superadmin.2fa.setup')
                : $next($request);
        }

        // 2FA activé : exiger la vérification de la session courante.
        if ($request->session()->get('two_factor_verified') === true) {
            return $next($request);
        }

        return redirect()->route('superadmin.2fa.challenge');
    }
}
