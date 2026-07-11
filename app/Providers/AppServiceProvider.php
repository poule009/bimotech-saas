<?php

namespace App\Providers;

use App\Models\Contrat;
use App\Observers\ContratObserver;
use App\Services\FiscalService;
use App\Services\PlanFeatureService;
use App\Services\QuittanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
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
        // Auto-calcul des champs fiscaux du contrat (loyer_assujetti_tva, taux_tva_loyer,
        // brs_applicable, charges_assujetties_tva depuis mode_facturation_charges,
        // loyer_contractuel). Mécanisme documenté par la migration des champs fiscaux.
        Contrat::observe(ContratObserver::class);

        // @canAccessFeature('feature') ... @endcanAccessFeature
        Blade::if('canAccessFeature', function (string $feature): bool {
            return app(PlanFeatureService::class)->canAccess($feature);
        });

        // @cspNonce → insère le nonce CSP de la requête courante (Phase 2)
        Blade::directive('cspNonce', function (): string {
            return "<?php echo app('csp-nonce'); ?>";
        });

        // @money(1234.5) → "1 235 FCFA" — convention devise unique de l'app.
        Blade::directive('money', function (string $expression): string {
            return "<?php echo number_format((float) ({$expression}), 0, ',', ' ') . ' FCFA'; ?>";
        });

        // Compteur d'impayés du mois courant, partagé au layout pour le badge
        // de la bottom-nav « Relances ». Caché 10 min par agence (perf : sinon
        // une requête à chaque page). Calculé uniquement pour le staff agence.
        View::composer('layouts.app', function ($view) {
            $user  = Auth::user();
            $count = 0;

            if ($user && ! $user->isSuperAdmin() && $user->agency_id) {
                $count = Cache::remember(
                    "nav_impayes_{$user->agency_id}",
                    now()->addMinutes(10),
                    fn () => Contrat::where('agency_id', $user->agency_id)
                        ->where('statut', 'actif')
                        ->whereDoesntHave('paiements', fn ($q) => $q
                            ->whereYear('periode', now()->year)
                            ->whereMonth('periode', now()->month)
                            ->where('statut', '!=', 'annule'))
                        ->count()
                );
            }

            $view->with('navImpayesCount', $count);
        });
    }
}
