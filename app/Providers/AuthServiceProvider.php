<?php

namespace App\Providers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Policies\BienPolicy;
use App\Policies\ContratPolicy;
use App\Policies\PaiementPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping Modèle → Policy.
     */
    protected $policies = [
        Bien::class     => BienPolicy::class,
        Contrat::class  => ContratPolicy::class,
        Paiement::class => PaiementPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // ── Gates rôles — utilisées via $this->authorize() dans les controllers
        // et via can:xxx dans les middlewares de routes ───────────────────────

        Gate::define('isAdmin', function ($user) {
            return in_array($user->role, ['admin', 'superadmin']);
        });

        Gate::define('isProprietaire', function ($user) {
            return in_array($user->role, ['proprietaire', 'admin', 'superadmin']);
        });

        Gate::define('isLocataire', function ($user) {
            return in_array($user->role, ['locataire', 'admin', 'superadmin']);
        });

        Gate::define('isStaff', function ($user) {
            return in_array($user->role, ['admin', 'superadmin']);
        });

        // ── Gates sémantiques — utilisées dans les vues via @can ─────────────

        Gate::define('admin-agence', function ($user) {
            return in_array($user->role, ['admin', 'superadmin']);
        });

        Gate::define('dashboard-proprietaire', function ($user) {
            return in_array($user->role, ['proprietaire', 'admin', 'superadmin']);
        });

        Gate::define('dashboard-locataire', function ($user) {
            return in_array($user->role, ['locataire', 'admin', 'superadmin']);
        });

        Gate::define('voir-rapports-financiers', function ($user) {
            return in_array($user->role, ['admin', 'superadmin']);
        });

        Gate::define('voir-activity-logs', function ($user) {
            return $user->role === 'superadmin';
        });
    }
}
