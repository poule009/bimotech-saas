<?php

namespace App\Models;

use App\Enums\ContratStatut;
use App\Models\Concerns\HasAgencyScope;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Contrat extends Model
{
    use HasAgencyScope, HasFactory, LogsActivity, SoftDeletes;

    // ── Constantes ────────────────────────────────────────────────────────────

    public function getActivityLogTitle(): string
    {
        $this->loadMissing(['bien:id,reference', 'locataire:id,name']);
        $bien      = $this->bien?->reference      ?? 'Bien #' . $this->bien_id;
        $locataire = $this->locataire?->name       ?? 'Locataire #' . $this->locataire_id;
        return "Contrat {$bien} / {$locataire}";
    }

    protected static array $activityFieldLabels = [
        'statut'                  => 'Statut',
        'loyer_contractuel'       => 'Loyer contractuel',
        'charges_contractuelles'  => 'Charges',
        'caution'                 => 'Caution',
        'date_debut'              => 'Date début',
        'date_fin'                => 'Date fin',
        'type_bail'               => 'Type de bail',
        'bien_id'                 => 'Bien',
        'locataire_id'            => 'Locataire',
        'index_annuel'            => 'Indexation annuelle',
        'taux_commission'         => 'Commission',
    ];

    /**
     * Journal d'activité — modifier le loyer/charges d'un bail ACTIF (déjà signé)
     * est une action sensible (fort impact financier/légal).
     */
    public function isSensitiveActivity(string $action, array $fields): bool
    {
        return $action === 'updated'
            && (string) $this->statut === 'actif'
            && (bool) array_intersect($fields, ['loyer_contractuel', 'loyer_nu', 'charges_mensuelles']);
    }

    public const TYPES_BAIL = [
        'habitation'  => "Bail d'habitation",
        'commercial'  => 'Bail commercial',
        'mixte'       => 'Bail mixte',
        'saisonnier'  => 'Bail saisonnier',
    ];

    public const STATUTS = [
        'actif'   => 'Actif',
        'resilié' => 'Résilié',
        'expiré'  => 'Expiré',
    ];

    /**
     * Mode de facturation des charges (quand des charges existent).
     * Pilote charges_assujetties_tva — voir regles_fiscales, clé 'tva_charges'.
     *   debours → refacturé à l'identique, sans marge, factures au nom du locataire → TVA 0%
     *   forfait → forfait mensuel fixe → TVA 18%
     */
    public const MODES_FACTURATION_CHARGES = [
        'debours' => 'Débours — refacturé à l\'identique (exonéré de TVA)',
        'forfait' => 'Forfait mensuel fixe (TVA 18 %)',
    ];

    // ── Fillable ──────────────────────────────────────────────────────────────

    protected $fillable = [
        // agency_id exclu du fillable — injecté uniquement via booted() hook
        'bien_id',
        'locataire_id',
        'date_debut',
        'date_fin',

        // ── Ventilation du loyer ───────────────────────────────────────────
        'loyer_nu',             // Loyer hors charges et hors TOM — assiette commission et TVA (≠ Bien.loyer_mensuel qui est un prix de référence)
        'loyer_contractuel',    // Total = loyer_nu + charges_mensuelles + tom_amount
        'charges_mensuelles',        // Charges récupérables mensuelles
        'charges_assujetties_tva',   // Bool — charges facturées en forfait → TVA 18% obligatoire (DGI SN)
        'mode_facturation_charges',  // 'debours' (0%) | 'forfait' (18%) — pilote charges_assujetties_tva
        'tom_amount',           // Taxe sur les Ordures Ménagères (FCFA fixe)

        // ── Financier ─────────────────────────────────────────────────────
        'caution',
        'nombre_mois_caution',
        'caution_gardee_par_agence',   // Bool — l'agence garde la caution en séquestre
        'frais_agence',
        'indexation_annuelle',
        'annee_derniere_indexation',

        // ── Statut et type ─────────────────────────────────────────────────
        'statut',
        'type_bail',
        'reference_bail',

        // ── Fiscal — CORRIGÉ : ces champs étaient absents ─────────────────
        'loyer_assujetti_tva',      // Bool — TVA loyer applicable
        'taux_tva_loyer',           // Taux TVA loyer (0 ou 18)
        'brs_applicable',           // Bool — BRS applicable
        'taux_brs_manuel',          // Override taux BRS (null = légal)
        'date_enregistrement_dgid',    // Date enregistrement bail à la DGID
        'numero_quittance_dgid',       // N° quittance DGID après enregistrement
        'montant_droit_de_bail',       // Montant droits enregistrement payé à la DGID
        'enregistrement_exonere',      // Bool — exonéré d'enregistrement
        'taux_enregistrement_dgid',    // Override taux DGID (null = légal : 2%)

        // ── Droits d'enregistrement — tracker contrat (calculé par Observer) ─
        'droit_enreg_montant',          // Droits = 2% × base × mois (calculé)
        'droit_enreg_timbre',           // Timbre = 2 000 × nombre_feuilles (calculé)
        'droit_enreg_nombre_feuilles',  // Nb feuilles de l'acte (défaut 2, modifiable)
        'droit_enreg_statut_calcul',    // 'confirme' | 'estimation' (>12 mois)
        'droit_enreg_date_limite',      // Signature (date_debut) + 1 mois (calculé)
        'droit_enreg_renouvelable',     // Bail ≤12 mois renouvelable ? (défaut true)
        'droit_enreg_effectue',         // Bail enregistré à la DGID ?
        'droit_enreg_date_effectue',    // Date de l'enregistrement effectif

        // ── Garant ────────────────────────────────────────────────────────
        'garant_nom',
        'garant_telephone',
        'garant_adresse',
        'garant_cni',

        // ── Divers ────────────────────────────────────────────────────────
        'observations',
        'clauses_particulieres',

        // ── Import de données ──────────────────────────────────────────────
        'import_batch_id',
    ];

    // ── Casts ─────────────────────────────────────────────────────────────────

    protected $casts = [
        'date_debut'                => 'date',
        'date_fin'                  => 'date',
        'date_enregistrement_dgid'  => 'date',
        'loyer_nu'                  => 'decimal:2',
        'loyer_contractuel'         => 'decimal:2',
        'charges_mensuelles'          => 'decimal:2',
        'charges_assujetties_tva'     => 'boolean',
        'tom_amount'                => 'decimal:2',
        'caution'                   => 'decimal:2',
        'frais_agence'              => 'decimal:2',
        'indexation_annuelle'       => 'decimal:2',
        'taux_tva_loyer'            => 'decimal:2',
        'taux_brs_manuel'           => 'decimal:2',
        'nombre_mois_caution'       => 'integer',
        'loyer_assujetti_tva'          => 'boolean',
        'brs_applicable'               => 'boolean',
        'enregistrement_exonere'       => 'boolean',
        'caution_gardee_par_agence'    => 'boolean',
        'taux_enregistrement_dgid'     => 'decimal:2',
        'montant_droit_de_bail'        => 'decimal:2',
        'droit_enreg_montant'          => 'decimal:2',
        'droit_enreg_timbre'           => 'decimal:2',
        'droit_enreg_nombre_feuilles'  => 'integer',
        'droit_enreg_date_limite'      => 'date',
        'droit_enreg_renouvelable'     => 'boolean',
        'droit_enreg_effectue'         => 'boolean',
        'droit_enreg_date_effectue'    => 'date',
        // Note : pas de cast Enum — $contrat->statut reste une string en Blade.
        'deleted_at'                   => 'datetime',
    ];

    /** Accès total dû à l'enregistrement (droits + timbre). */
    public function getDroitEnregTotalAttribute(): float
    {
        return round((float) ($this->droit_enreg_montant ?? 0) + (float) ($this->droit_enreg_timbre ?? 0), 2);
    }

    // ── Global Scope + hooks ───────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Contrat $contrat) {
            // Auto-injection agency_id
            if (empty($contrat->agency_id) && Auth::check()) {
                $contrat->agency_id = Auth::user()->agency_id;
            }

            // Auto-calcul loyer_contractuel si non fourni
            if (
                ! empty($contrat->loyer_nu)
                && (empty($contrat->loyer_contractuel) || $contrat->loyer_contractuel == 0)
            ) {
                $contrat->loyer_contractuel = (float) $contrat->loyer_nu
                    + (float) ($contrat->charges_mensuelles ?? 0)
                    + (float) ($contrat->tom_amount ?? 0);
            }

            // Rétro-compat : si loyer_nu absent mais loyer_contractuel renseigné
            if (empty($contrat->loyer_nu) && ! empty($contrat->loyer_contractuel)) {
                $contrat->loyer_nu = (float) $contrat->loyer_contractuel
                    - (float) ($contrat->charges_mensuelles ?? 0)
                    - (float) ($contrat->tom_amount ?? 0);
            }
        });
    }

    // ── Relations ─────────────────────────────────────────────────────────────

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
        // locataire_id = users.id
        return $this->belongsTo(User::class, 'locataire_id');
    }

    public function paiements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function quittances(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Quittance::class, Paiement::class);
    }

    // ── Accesseurs ────────────────────────────────────────────────────────────

    /**
     * Total loyer = loyer nu + charges + TOM.
     */
    public function getTotalLoyerAttribute(): float
    {
        return round(
            (float) ($this->loyer_nu ?? $this->loyer_contractuel ?? 0)
            + (float) ($this->charges_mensuelles ?? 0)
            + (float) ($this->tom_amount ?? 0),
            2
        );
    }

    /**
     * Loyer nu effectif avec fallback rétro-compatible.
     */
    public function getLoyerNuEffectifAttribute(): float
    {
        if (! empty($this->loyer_nu)) {
            return (float) $this->loyer_nu;
        }

        return max(0, (float) ($this->loyer_contractuel ?? 0)
            - (float) ($this->charges_mensuelles ?? 0)
            - (float) ($this->tom_amount ?? 0));
    }

    /**
     * Référence bail affichée : priorité manuelle > générée auto.
     * Format généré : BIMO-YYYY-NNNNN
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
     * Nombre de mois de la durée du bail.
     */
    public function getDureeMoisAttribute(): int
    {
        if (! $this->date_debut || ! $this->date_fin) {
            return 0;
        }

        return (int) $this->date_debut->diffInMonths($this->date_fin);
    }

    /**
     * Indique si le bail arrive à échéance dans moins de 30 jours.
     */
    public function getExpireBientotAttribute(): bool
    {
        if (! $this->date_fin) {
            return false;
        }

        return $this->statut === 'actif'
            && $this->date_fin->diffInDays(now()) <= 30
            && $this->date_fin->isFuture();
    }
}