<?php

namespace App\Providers;

use App\Services\FiscalService;
use App\Services\PaiementService;
use App\Services\QuittanceService;
use Illuminate\Support\ServiceProvider;

/**
 * AppServiceProvider — Enregistrement des Services dans le conteneur IoC.
 *
 * Les Services sont enregistrés en singleton :
 * une seule instance par requête, partagée entre tous les contrôleurs.
 *
 * Injection automatique via le constructeur (type-hinting Laravel).
 * Exemple dans un contrôleur :
 *
 *   public function __construct(
 *       private readonly FiscalService    $fiscalService,
 *       private readonly PaiementService  $paiementService,
 *       private readonly QuittanceService $quittanceService
 *   ) {}
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // FiscalService : pas de dépendances → singleton simple
        $this->app->singleton(FiscalService::class);

        // PaiementService dépend de FiscalService → résolution automatique
        $this->app->singleton(PaiementService::class, function ($app) {
            return new PaiementService(
                $app->make(FiscalService::class)
            );
        });

        // QuittanceService : singleton simple (dépendances auto-résolues)
        $this->app->singleton(QuittanceService::class);
    }

    public function boot(): void
    {
        //
    }
}