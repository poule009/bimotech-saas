<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AgencyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Pas de filtre en CLI (migrations, seeders, queues)
        if (! Auth::check()) {
            return;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Le superadmin voit toutes les agences — aucun filtre appliqué
        if ($user->role === 'superadmin') {
            return;
        }

        // Tous les autres ne voient que les données de leur agence
        $builder->where($model->getTable() . '.agency_id', $user->agency_id);
    }
}