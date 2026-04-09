<?php

namespace App\Models\Concerns;

use App\Models\Scopes\AgencyScope;

/**
 * HasAgencyScope — Trait d'isolation multi-tenant.
 *
 * À ajouter sur TOUS les modèles métier qui appartiennent à une agence :
 * Bien, Contrat, Locataire, Proprietaire, Paiement, Quittance, ActivityLog
 *
 * Usage dans un modèle :
 *
 *   use App\Models\Concerns\HasAgencyScope;
 *
 *   class Bien extends Model
 *   {
 *       use HasAgencyScope;
 *   }
 *
 * Effet immédiat : toutes les requêtes Eloquent (index, show, update, delete)
 * filtrent automatiquement par agency_id de l'utilisateur connecté.
 * Aucun contrôleur ne peut "oublier" ce filtre.
 */
trait HasAgencyScope
{
    /**
     * Boot du trait — enregistre le GlobalScope sur le modèle.
     */
    protected static function bootHasAgencyScope(): void
    {
        static::addGlobalScope(new AgencyScope());
    }

    /**
     * Échappe le scope pour une opération cross-agency explicite (superadmin, migrations).
     *
     * Exemple : Bien::withoutAgencyScope()->where('statut', 'actif')->get();
     */
    public static function withoutAgencyScope(): \Illuminate\Database\Eloquent\Builder
    {
        return static::withoutGlobalScope(AgencyScope::class);
    }

    /**
     * Relation inverse standardisée vers l'agence propriétaire.
     */
    public function agency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Agency::class);
    }
}