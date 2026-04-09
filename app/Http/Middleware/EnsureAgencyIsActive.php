<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgencyIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

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
                ->withErrors(['email' => 'Votre compte n\'est rattaché à aucune agence. Contactez votre administrateur.']);
        }

        $agency = $user->agency;

        // CORRIGÉ : utilise agency->actif (boolean) au lieu de agency->statut (inexistant)
        if (! $agency->actif) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre agence a été désactivée. Contactez BimoTech Immo.']);
        }

        return $next($request);
    }
}