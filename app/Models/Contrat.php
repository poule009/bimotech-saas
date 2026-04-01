<?php

namespace App\Models;

use App\Models\Scopes\AgencyScope;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Contrat extends Model
{
    use HasFactory, LogsActivity;

    // ── Constantes ────────────────────────────────────────────────────────

    public const TYPES_BAIL = [
        'habitation'  => "Bail d'habitation",
        'commercial'  => 'Bail commercial',
        'mixte'       => 'Bail mixte',
        'saisonnier'  => 'Bail saisonnier',
    ];

    public const STATUTS = [
        'actif'    => 'Actif',
        'resilié'  => 'Résilié',
        'expiré'   => 'Expiré',
    ];

    // ── Fillable ──────────────────────────────────────────────────────────

    protected $fillable = [
        'agency_id',
        'bien_id',
        'locataire_id',
        'date_debut',
        'date_fin',
        // ── Ventilation du loyer ──────────────────────────────────────
        'loyer_nu',             // loyer hors charges et hors TOM
        'loyer_contractuel',    // total = loyer_nu + charges_mensuelles + tom_amount
        'tom_amount',           // Taxe sur les Ordures Ménagères (part locataire)
        'caution',
        'statut',
        'type_bail',
        'frais_agence',
        'charges_mensuelles',   // charges récupérables mensuelles
        'indexation_annuelle',
        'nombre_mois_caution',
        'garant_nom',
        'garant_telephone',
        'garant_adresse',
        'observations',
        'reference_bail',       // référence bail manuelle (nullable)
    ];

    // ── Casts ─────────────────────────────────────────────────────────────

    protected $casts = [
        'date_debut'          => 'date',
        'date_fin'            => 'date',
        'loyer_nu'            => 'decimal:2',
        'loyer_contractuel'   => 'decimal:2',
        'tom_amount'          => 'decimal:2',
        'caution'             => 'decimal:2',
        'frais_agence'        => 'decimal:2',
        'charges_mensuelles'  => 'decimal:2',
        'indexation_annuelle' => 'decimal:2',
        'nombre_mois_caution' => 'integer',
    ];

    // ── Global Scope ──────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::addGlobalScope(new AgencyScope());

        static::creating(function (Contrat $contrat) {
            if (empty($contrat->agency_id) && Auth::check()) {
                $contrat->agency_id = Auth::user()->agency_id;
            }

            // Auto-calcul loyer_contractuel si non fourni
            // loyer_contractuel = loyer_nu + charges_mensuelles + tom_amount
            if (
                ! empty($contrat->loyer_nu)
                && (empty($contrat->loyer_contractuel) || $contrat->loyer_contractuel == 0)
            ) {
                $contrat->loyer_contractuel = (float) $contrat->loyer_nu
                    + (float) ($contrat->charges_mensuelles ?? 0)
                    + (float) ($contrat->tom_amount ?? 0);
            }

            // Inverse : si loyer_nu non renseigné mais loyer_contractuel oui
            // (rétro-compatibilité)
            if (empty($contrat->loyer_nu) && ! empty($contrat->loyer_contractuel)) {
                $contrat->loyer_nu = (float) $contrat->loyer_contractuel
                    - (float) ($contrat->charges_mensuelles ?? 0)
                    - (float) ($contrat->tom_amount ?? 0);
            }
        });
    }

    // ── Relations ─────────────────────────────────────────────────────────

    public function agency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function bien(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Bien::class);
    }

    public function locataire(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'locataire_id');
    }

    public function paiements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function contratActif(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Paiement::class)->where('statut', 'actif');
    }

    // ── Accesseurs ────────────────────────────────────────────────────────

    /**
     * Total loyer = loyer nu + charges + TOM.
     * Utile pour l'affichage et les calculs sans recharger depuis la DB.
     *
     * Usage : $contrat->total_loyer
     */
    public function getTotalLoyerAttribute(): float
    {
        return round(
            (float) ($this->loyer_nu ?? $this->loyer_contractuel)
            + (float) ($this->charges_mensuelles ?? 0)
            + (float) ($this->tom_amount ?? 0),
            2
        );
    }

    /**
     * Référence bail affichée : priorité manuelle > générée.
     * Format généré : BIMO-YYYY-NNNNN
     *
     * Usage : $contrat->reference_bail_affichee
     */
    public function getReferenceBailAfficheeAttribute(): string
    {
        if (! empty($this->reference_bail)) {
            return $this->reference_bail;
        }

        return sprintf(
            'BIMO-%s-%s',
            now()->year,
            str_pad((string) $this->id, 5, '0', STR_PAD_LEFT)
        );
    }

    /**
     * Loyer nu effectif — avec fallback rétro-compatible.
     *
     * Usage : $contrat->loyer_nu_effectif
     */
    public function getLoyerNuEffectifAttribute(): float
    {
        if (! empty($this->loyer_nu) && (float) $this->loyer_nu > 0) {
            return (float) $this->loyer_nu;
        }

        // Rétro-compatibilité : déduire les charges du loyer contractuel
        return max(
            0,
            (float) $this->loyer_contractuel
            - (float) ($this->charges_mensuelles ?? 0)
            - (float) ($this->tom_amount ?? 0)
        );
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Retourne la décomposition complète du loyer mensuel.
     * Pratique pour passer à la vue ou au contrôleur PDF.
     *
     * @return array{loyer_nu: float, charges: float, tom: float, total: float}
     */
    public function decompositionLoyer(): array
    {
        return [
            'loyer_nu' => $this->loyer_nu_effectif,
            'charges'  => (float) ($this->charges_mensuelles ?? 0),
            'tom'      => (float) ($this->tom_amount ?? 0),
            'total'    => $this->total_loyer,
        ];
    }
}