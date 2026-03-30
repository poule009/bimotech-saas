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

        // BUG 1 FIX : cohérence avec le middleware IsAdmin qui autorise superadmin + admin
        Gate::define('isAdmin', fn($user)
            => in_array($user->role, ['admin', 'superadmin'])
        );

        Gate::define('isProprietaire', fn($user)
            => $user->role === 'proprietaire'
        );

        Gate::define('isLocataire', fn($user)
            => $user->role === 'locataire'
        );

        // Admin OU Propriétaire (accès staff de l'agence — routes biens/photos)
        // Le superadmin gère la plateforme, pas les biens des agences → exclu de isStaff
        Gate::define('isStaff', fn($user)
            => in_array($user->role, ['admin', 'proprietaire'])
        );

        // ── Injecter l'agence courante dans toutes les vues Blade ─────────
        // Cela permet d'afficher le logo et les couleurs dynamiquement
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $view->with('currentAgency', $user->agency);

                // Badge dynamique impayés (mois courant) — admin uniquement
                if ($user->role === 'admin') {
                    $impayes_count = \App\Models\Contrat::where('statut', 'actif')
                        ->whereDoesntHave('paiements', function ($q) {
                            $q->whereYear('periode', now()->year)
                              ->whereMonth('periode', now()->month)
                              ->where('statut', '!=', 'annule');
                        })
                        ->count();
                    $view->with('impayes_count', $impayes_count);
                } else {
                    $view->with('impayes_count', 0);
                }
            }
        });
    }
}