<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Statuts sélectionnables dans la fiche règle (Super Admin), regroupés.
     * Conserve la richesse réelle du backend (8 statuts) plutôt que le binaire
     * confirmé/non-vérifié de la maquette — le moteur et les badges s'appuient
     * déjà sur ces nuances.
     */
    public const STATUTS = [
        'confirme'                => 'Confirmé (source externe indépendante)',
        'confirme_officiel'       => 'Confirmé officiel (texte / administration)',
        'confirme_source_privee'  => 'Confirmé source privée (à recouper)',
        'non_verifie'             => 'Non vérifié (plausible, à confirmer)',
        'conflit_non_tranche'     => 'Conflit non tranché entre sources',
        'decision_produit'        => 'Décision produit interne',
        'hors_perimetre'          => 'Hors périmètre (non implémenté)',
        'hors_perimetre_delibere' => 'Hors périmètre délibéré (jamais calculé)',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    /** Historique des modifications (le plus récent d'abord via ->latest()). */
    public function historiques(): HasMany
    {
        return $this->hasMany(RegleFiscaleHistorique::class, 'regle_fiscale_id');
    }

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
            'confirme'                => 'Confirmé (source externe indépendante)',
            'confirme_officiel'       => 'Confirmé officiel',
            'confirme_source_privee'  => 'Confirmé source privée (à recouper)',
            'conflit_non_tranche'     => 'Conflit non tranché',
            'decision_produit'        => 'Décision produit interne',
            'hors_perimetre'          => 'Hors périmètre (non implémenté)',
            'hors_perimetre_delibere' => 'Hors périmètre délibéré',
            default                   => 'À vérifier (non confirmé)',
        };
    }

    /**
     * Variante de couleur du badge de statut (mapping validé) :
     *   green = confirmé ferme · teal = confirmé source privée ·
     *   gold = à vérifier / conflit · gray = décision produit / hors périmètre.
     */
    public function getStatutVariantAttribute(): string
    {
        return match ($this->statut) {
            'confirme', 'confirme_officiel'                       => 'green',
            'confirme_source_privee'                             => 'teal',
            'non_verifie', 'conflit_non_tranche'                 => 'gold',
            'decision_produit', 'hors_perimetre',
            'hors_perimetre_delibere'                            => 'gray',
            default                                              => 'gold',
        };
    }

    /** Groupe « confirmé » au sens large (tout statut confirme_*). */
    public function getEstConfirmeeAttribute(): bool
    {
        return in_array($this->statut, ['confirme', 'confirme_officiel', 'confirme_source_privee'], true);
    }
}
