<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAgencyActif
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Pas connecté → on laisse passer (la route auth s'en charge)
        if (! $user) {
            return $next($request);
        }

        // Le superadmin n'appartient à aucune agence → toujours autorisé
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // L'utilisateur n'a pas d'agence → on déconnecte
        if (! $user->agency_id || ! $user->agency) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte n\'est rattaché à aucune agence. Contactez le support.',
            ]);
        }

        // L'agence est suspendue → on déconnecte avec message
        if (! $user->agency->actif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => "Votre agence « {$user->agency->name} » a été suspendue. Contactez le support à contact@bimotech.sn.",
            ]);
        }

        return $next($request);
    }
}