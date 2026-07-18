<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role !== 'superadmin') {
            abort(403, 'Accès réservé au Super Administrateur de la plateforme.');
        }

        // Accès révoqué par l'admin principal (module Équipe interne) : on coupe la
        // connexion immédiatement. Les agences apportées restent attribuées (historique
        // de commission) — la révocation ne touche que la connexion, pas amenee_par.
        if ($user->saAccesRevoque()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('warning', 'Votre accès au Super Admin a été révoqué. Contactez l\'administrateur principal.');
        }

        return $next($request);
    }
}