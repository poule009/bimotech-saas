<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * RegleFiscale — Traçabilité d'une règle fiscale et de ses sources.
 *
 * Persiste le catalogue de config/fiscal_sources.php (via ReglesFiscalesSeeder).
 * Permet de retrouver, pour chaque chiffre affiché, d'où vient la règle :
 * description, statut (confirmé / à vérifier), sources (libellé + URL),
 * date de dernière vérification.
 *
 * Aucune logique de calcul ici — c'est un référentiel documentaire.
 */
class RegleFiscale extends Model
{
    protected $table = 'regles_fiscales';

    protected $fillable = [
        'cle',
        'categorie',
        'titre',
        'description',
        'statut',
        'sources',
        'note',
        'date_verification',
    ];

    protected $casts = [
        'sources'           => 'array',
        'date_verification' => 'date',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeConfirmees($query)
    {
        return $query->where('statut', 'confirme');
    }

    public function scopeAVerifier($query)
    {
        return $query->where('statut', 'non_verifie');
    }

    public function scopeCategorie($query, string $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /** Libellé lisible du statut, pour l'UI. */
    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'confirme'         => 'Confirmé par source externe',
            'decision_produit' => 'Décision produit interne (pas une exigence légale)',
            default            => 'À vérifier (non confirmé)',
        };
    }

    /** Vrai si la règle repose sur une source externe indépendante. */
    public function getEstConfirmeeAttribute(): bool
    {
        return $this->statut === 'confirme';
    }
}
