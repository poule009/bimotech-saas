<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsLocataire
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Seul le rôle 'locataire' est autorisé sur ces routes.
        // Avant : ['locataire', 'admin', 'superadmin'] — un admin pouvait accéder
        // au dashboard locataire, ce qui brisait le cloisonnement des rôles.
        // Les admins qui doivent consulter les données locataires passent par
        // leurs propres routes (/admin/…) avec leur propre middleware isAdmin.
        if ($user->role !== 'locataire') {
            abort(403, 'Accès réservé aux locataires.');
        }

        return $next($request);
    }
}