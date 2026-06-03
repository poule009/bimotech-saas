<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Autorisé : admin d'une agence OU superadmin de la plateforme
        if (! in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        // Sécurité supplémentaire : un admin doit appartenir à une agence active
        if ($user->role === 'admin' && (! $user->agency || ! $user->agency->isActif())) {
            abort(403, 'Votre agence est inactive. Contactez le support.');
        }

        return $next($request);
    }
}