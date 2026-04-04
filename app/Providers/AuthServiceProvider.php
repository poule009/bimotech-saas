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
     * Laravel résout automatiquement la bonne Policy selon le modèle injecté.
     */
    protected $policies = [
        Bien::class     => BienPolicy::class,
        Contrat::class  => ContratPolicy::class,
        Paiement::class => PaiementPolicy::class,
    ];

    /**
     * Enregistrement des Gates et Policies.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * Gate transversal : vérifie si l'utilisateur est admin de son agence.
         * Usage : Gate::allows('admin-agence') ou @can('admin-agence') en Blade.
         */
        Gate::define('admin-agence', function ($user) {
            return in_array($user->role, ['admin', 'superadmin']);
        });

        /**
         * Gate : accès au tableau de bord propriétaire.
         */
        Gate::define('dashboard-proprietaire', function ($user) {
            return in_array($user->role, ['proprietaire', 'admin', 'superadmin']);
        });

        /**
         * Gate : accès au tableau de bord locataire.
         */
        Gate::define('dashboard-locataire', function ($user) {
            return in_array($user->role, ['locataire', 'admin', 'superadmin']);
        });

        /**
         * Gate : accès aux rapports financiers (données sensibles).
         */
        Gate::define('voir-rapports-financiers', function ($user) {
            return in_array($user->role, ['admin', 'superadmin']);
        });

        /**
         * Gate : accès aux logs d'activité (superadmin uniquement).
         */
        Gate::define('voir-activity-logs', function ($user) {
            return $user->role === 'superadmin';
        });
    }
}