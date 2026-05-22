<?php

namespace App\Providers;

use App\Services\FiscalService;
use App\Services\PlanFeatureService;
use App\Services\QuittanceService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FiscalService::class);
        $this->app->singleton(QuittanceService::class);
        $this->app->singleton(PlanFeatureService::class);
    }

    public function boot(): void
    {
        // @canAccessFeature('feature') ... @endcanAccessFeature
        Blade::if('canAccessFeature', function (string $feature): bool {
            return app(PlanFeatureService::class)->canAccess($feature);
        });
    }
}
