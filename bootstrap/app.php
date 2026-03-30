<?php

use App\Http\Middleware\CheckAgencyActif;
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsSuperAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $exceptions->report(function (\Throwable $e) {
            /** @var int $statusCode */
            $statusCode = method_exists($e, 'getStatusCode')
                ? (int) call_user_func([$e, 'getStatusCode'])
                : 500;

            if ($statusCode >= 500) {
                $user = Auth::user();

                Log::error('HTTP 500 capturée', [
                    'message' => $e->getMessage(),
                    'exception' => get_class($e),
                    'agency_id' => $user?->agency_id,
                    'user_id' => $user?->id,
                    'url' => request()?->fullUrl(),
                    'method' => request()?->method(),
                    'trace_id' => request()?->header('X-Request-Id') ?? (string) \Illuminate\Support\Str::uuid(),
                ]);
            }
        });
    })->create();
