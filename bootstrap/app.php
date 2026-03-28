<?php

use App\Http\Middleware\CheckAgencyActif;
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsSuperAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Middlewares globaux ────────────────────────────────────────────
        $middleware->appendToGroup('web', CheckAgencyActif::class);
        $middleware->appendToGroup('web', CheckSubscription::class);

        // ── Alias pour les routes ─────────────────────────────────────────
        $middleware->alias([
            'isAdmin'      => IsAdmin::class,
            'isSuperAdmin' => IsSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();