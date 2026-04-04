<?php

namespace App\Providers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Policies\BienPolicy;
use App\Policies\ContratPolicy;
use App\Policies\PaiementPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Locataire;
use App\Observers\ContratObserver;
use App\Observers\LocataireObserver;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Paiement::class, PaiementPolicy::class);
        Gate::policy(Bien::class,     BienPolicy::class);
        Gate::policy(Contrat::class,  ContratPolicy::class);

        Gate::define('isSuperAdmin', fn($user) => $user->role === 'superadmin');
        Gate::define('isAdmin',      fn($user) => in_array($user->role, ['admin', 'superadmin']));
        Gate::define('isProprietaire', fn($user) => $user->role === 'proprietaire');
        Gate::define('isLocataire',  fn($user) => $user->role === 'locataire');
        Gate::define('isStaff',      fn($user) => in_array($user->role, ['admin', 'proprietaire']));

        // ── Observers fiscaux ─────────────────────────────────────────────────
Contrat::observe(ContratObserver::class);
Locataire::observe(LocataireObserver::class);
        // ✅ CORRECTION H1 : le calcul ne se fait plus à chaque vue imbriquée
        // Avant : 5 à 10 requêtes SQL par page (une par partial, layout, composant…)
        // Après : 1 calcul par requête HTTP grâce à "static $shared"
        //         + cache 15 min pour le compteur d'impayés
        View::composer('*', function ($view) {
            if (! Auth::check()) {
                return;
            }

            // "static" = calculé une seule fois, même si la vue est imbriquée 10 fois
            static $shared = null;

            if ($shared !== null) {
                $view->with($shared);
                return;
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();

            $agency = $user->relationLoaded('agency')
                ? $user->agency
                : $user->load('agency')->agency;

            $impayesCount = 0;

            if ($user->role === 'admin') {
                // Cache par agence + tranche de 15 minutes
                $cacheKey = sprintf('impayes_count_%d_%s', $user->agency_id, now()->format('YmdHi'));

                $impayesCount = Cache::remember($cacheKey, now()->addMinutes(15), function () {
                    return Contrat::where('statut', 'actif')
                        ->whereDoesntHave('paiements', function ($q) {
                            $q->whereYear('periode', now()->year)
                              ->whereMonth('periode', now()->month)
                              ->where('statut', '!=', 'annule');
                        })
                        ->count();
                });
            }

            $shared = [
                'currentAgency' => $agency,
                'impayes_count' => $impayesCount,
            ];

            $view->with($shared);
        });
    }
}