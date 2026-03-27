<?php

namespace App\Providers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Policies\BienPolicy;
use App\Policies\ContratPolicy;
use App\Policies\PaiementPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ── Policies ──────────────────────────────────────────────────────
        Gate::policy(Paiement::class, PaiementPolicy::class);
        Gate::policy(Bien::class,     BienPolicy::class);
        Gate::policy(Contrat::class,  ContratPolicy::class);

        // ── Gates rôles ───────────────────────────────────────────────────
        Gate::define('isSuperAdmin', fn($user)
            => $user->role === 'superadmin'
        );

        Gate::define('isAdmin', fn($user)
            => $user->role === 'admin'
        );

        Gate::define('isProprietaire', fn($user)
            => $user->role === 'proprietaire'
        );

        Gate::define('isLocataire', fn($user)
            => $user->role === 'locataire'
        );

        // Admin OU Propriétaire (accès staff de l'agence)
        Gate::define('isStaff', fn($user)
            => in_array($user->role, ['admin', 'proprietaire'])
        );

        // SuperAdmin OU Admin (accès back-office)
        Gate::define('isBackOffice', fn($user)
            => in_array($user->role, ['superadmin', 'admin'])
        );

        // ── Injecter l'agence courante dans toutes les vues Blade ─────────
        // Cela permet d'afficher le logo et les couleurs dynamiquement
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('currentAgency', Auth::user()->agency);
            }
        });
    }
}