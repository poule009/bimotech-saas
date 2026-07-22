<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * AgencyThroughUserScope — Isolation multi-tenant pour les modèles qui ne
 * portent PAS de colonne `agency_id` mais sont rattachés à une agence via
 * leur relation `user` (Proprietaire, Locataire : user_id → users.agency_id).
 *
 * Équivalent fonctionnel d'AgencyScope, appliqué par jointure plutôt que sur
 * une colonne locale. Mêmes exceptions : superadmin et contextes sans session.
 */
class AgencyThroughUserScope implements Scope
{
    /**
     * @param Builder<Model> $builder
     * @param Model $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Pas de session active (CLI, queue worker, tests sans auth) → on laisse passer
        if (! app()->has('auth') || ! Auth::check()) {
            return;
        }

        $user = Auth::user();

        // Le superadmin voit tout (backoffice BimoTech)
        if ($user->role === 'superadmin') {
            return;
        }

        // Cloisonnement à l'agence via la relation user
        if ($user->agency_id) {
            $builder->whereHas('user', fn ($q) => $q->where('agency_id', $user->agency_id));
        } else {
            // Utilisateur sans agence (données corrompues) → aucun résultat par sécurité
            $builder->whereRaw('1 = 0');
        }
    }
}
