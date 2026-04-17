<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Alias des middlewares custom ─────────────────────────────────
        $middleware->alias([
            'isAdmin'             => \App\Http\Middleware\IsAdmin::class,
            'isSuperAdmin'        => \App\Http\Middleware\IsSuperAdmin::class,
            'isProprietaire'      => \App\Http\Middleware\IsProprietaire::class,
            'isLocataire'         => \App\Http\Middleware\IsLocataire::class,
            'checkSubscription'   => \App\Http\Middleware\CheckSubscription::class,
            'ensureAgencyIsActive'=> \App\Http\Middleware\EnsureAgencyIsActive::class,
        ]);

        // ── Middlewares appliqués globalement sur le groupe web ──────────
        // SecureHeaders ajoute les en-têtes de sécurité HTTP sur toutes les réponses
        // CheckSubscription vérifie l'abonnement sur toutes les routes auth
        $middleware->web(append: [
            \App\Http\Middleware\SecureHeaders::class,
            \App\Http\Middleware\CheckSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();