<?php

return [

    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),

    'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE', 0.1),

    'profiles_sample_rate' => env('SENTRY_PROFILES_SAMPLE_RATE', 0.0),

    'breadcrumbs' => [
        'logs'                     => true,
        'cache'                    => true,
        'livewire'                 => false,
        'sql_queries'              => true,
        'sql_bindings'             => false, // ne jamais logger les valeurs SQL
        'queue_info'               => true,
        'command_info'             => true,
        'http_client_requests'     => true,
        'send_default_pii'         => false, // RGPD — jamais d'IP ni d'email
    ],

    'tracing' => [
        'queue_job_transactions'        => env('SENTRY_TRACE_QUEUE_ENABLED', false),
        'queue_jobs'                    => true,
        'sql_queries'                   => true,
        'sql_origin'                    => false,
        'views'                         => false,
        'livewire'                      => false,
        'http_client_requests'          => true,
        'redis_commands'                => false,
        'redis_origin'                  => false,
        'missing_routes'                => false,
        'request_origin'                => true,
        'http_client_request_bodies'    => false,
    ],

    // Ne jamais capturer les exceptions de validation — bruit inutile
    'ignore_exceptions' => [
        \Illuminate\Validation\ValidationException::class,
        \Illuminate\Auth\AuthenticationException::class,
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
    ],

    // Ne jamais envoyer de données personnelles identifiables
    'send_default_pii' => false,

];
