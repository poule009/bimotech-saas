<?php

namespace App\Providers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Policies\BienPolicy;
use App\Policies\ContratPolicy;
use App\Policies\PaiementPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ── Policies ─────────────────────────────────────────────────────
        Gate::policy(Paiement::class, PaiementPolicy::class);
        Gate::policy(Bien::class,     BienPolicy::class);
        Gate::policy(Contrat::class,  ContratPolicy::class);

        // ── Gates rôles ───────────────────────────────────────────────────
        Gate::define('isAdmin', fn($user)
            => $user->role === 'admin'
        );

        Gate::define('isProprietaire', fn($user)
            => $user->role === 'proprietaire'
        );

        Gate::define('isLocataire', fn($user)
            => $user->role === 'locataire'
        );

        // ── Gate combinée : accès admin OU proprio ────────────────────────
        Gate::define('isStaff', fn($user)
            => in_array($user->role, ['admin', 'proprietaire'])
        );
    }
}