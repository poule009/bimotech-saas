<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * AgencyScope — Scope global d'isolation multi-tenant.
 *
 * Appliqué automatiquement à tous les modèles utilisant le trait HasAgencyScope.
 * Garantit qu'aucune requête ne peut retourner des données d'une autre agence,
 * même en cas d'oubli d'un where() dans un contrôleur.
 *
 * EXCEPTIONS intentionnelles :
 *  - Superadmin (role = 'superadmin') : accès cross-agency pour le backoffice
 *  - Commandes Artisan / Jobs en queue : pas d'utilisateur authentifié
 */
class AgencyScope implements Scope
{
    /**
     * Applique le scope à la requête Eloquent.
     *
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

        // Tous les autres rôles sont cloisonnés à leur agence
        if ($user->agency_id) {
            $builder->where($model->getTable() . '.agency_id', $user->agency_id);
        }
    }
}