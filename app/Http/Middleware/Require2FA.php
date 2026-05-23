<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Require2FA
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isSuperAdmin()) {
            return $next($request);
        }

        if (! $user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        if ($request->session()->get('two_factor_verified') === true) {
            return $next($request);
        }

        return redirect()->route('superadmin.2fa.challenge');
    }
}
