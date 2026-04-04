<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modèle Bien — Bien immobilier géré par une agence.
 *
 * Changements appliqués :
 *  - Trait HasAgencyScope : isolation multi-tenant automatique
 *  - SoftDeletes : les biens supprimés sont archivés, pas effacés (historique)
 *  - Relations avec Eager Loading hints en commentaire
 *  - Casts typés pour les montants (prévient les erreurs de calcul fiscal)
 */
class Bien extends Model
{
    use HasFactory, HasAgencyScope, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'proprietaire_id',
        'reference',
        'type',           // appartement, villa, bureau, commerce, terrain
        'titre',
        'description',
        'adresse',
        'quartier',
        'ville',
        'surface',
        'nb_pieces',
        'loyer_hors_charges',
        'charges',
        'depot_garantie',
        'statut',         // disponible, loue, en_travaux, archive
        'photos',
        'meuble',
    ];

    protected $casts = [
        'loyer_hors_charges' => 'decimal:0',
        'charges'            => 'decimal:0',
        'depot_garantie'     => 'decimal:0',
        'surface'            => 'decimal:2',
        'meuble'             => 'boolean',
        'photos'             => 'array',
        'deleted_at'         => 'datetime',
    ];

    // ─── Relations ─────────────────────────────────────────────────────────

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    /**
     * Tous les contrats (actifs, résiliés, expirés).
     * Eager load recommandé : Bien::with('contrats.locataire')
     */
    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    /**
     * Contrat actuellement actif (au maximum 1 à la fois).
     */
    public function contratActif()
    {
        return $this->hasOne(Contrat::class)->where('statut', 'actif');
    }

    /**
     * Tous les paiements de loyer liés à ce bien (via ses contrats).
     * Eager load recommandé : Bien::with('paiements')
     */
    public function paiements()
    {
        return $this->hasManyThrough(Paiement::class, Contrat::class);
    }

    // ─── Accesseurs & Calculs ───────────────────────────────────────────────

    /**
     * Loyer total charges comprises.
     */
    public function getLoyerTotalAttribute(): float
    {
        return (float) $this->loyer_hors_charges + (float) $this->charges;
    }

    /**
     * Vérifie si le bien est actuellement loué.
     */
    public function getEstLoueAttribute(): bool
    {
        return $this->statut === 'loue';
    }

    // ─── Scopes locaux ─────────────────────────────────────────────────────

    public function scopeDisponible($query)
    {
        return $query->where('statut', 'disponible');
    }

    public function scopeLoue($query)
    {
        return $query->where('statut', 'loue');
    }

    public function scopeAvecContratActif($query)
    {
        return $query->whereHas('contratActif');
    }
}