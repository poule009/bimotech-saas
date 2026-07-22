<?php

namespace App\Models\Concerns;

use App\Models\Scopes\AgencyThroughUserScope;

/**
 * HasAgencyScopeThroughUser — Isolation multi-tenant pour les modèles rattachés
 * à une agence via leur relation `user` (et non par une colonne agency_id locale).
 *
 * À utiliser sur Proprietaire et Locataire, dont l'agence se déduit de
 * users.agency_id. Remplace le scope inline auparavant copié-collé dans chaque
 * modèle → une seule implémentation à maintenir.
 *
 * Le modèle DOIT exposer une relation `user()`.
 */
trait HasAgencyScopeThroughUser
{
    protected static function bootHasAgencyScopeThroughUser(): void
    {
        static::addGlobalScope(new AgencyThroughUserScope());
    }

    /** Échappe le scope pour une opération cross-agency explicite (superadmin, migrations). */
    public static function withoutAgencyScope(): \Illuminate\Database\Eloquent\Builder
    {
        return static::withoutGlobalScope(AgencyThroughUserScope::class);
    }
}
