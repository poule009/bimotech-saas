<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsProprietaire
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! in_array($user->role, ['proprietaire', 'admin', 'superadmin'])) {
            abort(403, 'Accès réservé aux propriétaires.');
        }

        return $next($request);
    }
}