<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAgencyPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Superadmin et directeur d'agence (is_owner) passent toujours
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return $next($request);
        }

        // Vérifie au moins une des permissions demandées
        foreach ($permissions as $permission) {
            if ($user->hasAgencyPermission($permission)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        abort(403, 'Vous n\'avez pas la permission d\'effectuer cette action.');
    }
}
