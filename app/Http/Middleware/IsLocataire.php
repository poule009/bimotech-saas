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

        if (! in_array($user->role, ['locataire', 'admin', 'superadmin'])) {
            abort(403, 'Accès réservé aux locataires.');
        }

        return $next($request);
    }
}