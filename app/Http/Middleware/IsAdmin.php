<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    // Email autorisé — changez ici si nécessaire
    const ADMIN_EMAIL = 'admin@bimotech.sn';

    public function handle(Request $request, Closure $next): Response
    {
        // Non connecté → login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Double vérification : rôle ET email exact
        if ($user->role !== 'admin' || $user->email !== self::ADMIN_EMAIL) {
            abort(403, 'Accès réservé à l\'administrateur BIMO-Tech.');
        }

        return $next($request);
    }
}