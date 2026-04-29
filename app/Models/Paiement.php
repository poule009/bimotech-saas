<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Paiement extends Model
{
    use HasFactory, HasAgencyScope;

    // ── Fillable — reflète exactement la structure après migrations fiscales ──

    protected $fillable = [
        'agency_id',
        'contrat_id',

        // ── Ventilation loyer ──────────────────────────────────────────────
        'loyer_nu',            // Loyer hors charges et hors TOM (assiette commission)
        'loyer_ht',            // = loyer_nu pour habitation, loyer HT pour commercial
        'tva_loyer',           // TVA 18% sur loyer_ht (0 pour habitation)
        'loyer_ttc',           // loyer_ht + tva_loyer
        'charges_amount',      // Charges récupérables HT
        'tva_charges',         // TVA 18% sur charges forfait bail commercial/meublé (Art. 357 CGI SN)
        'charges_ttc',         // charges_amount + tva_charges
        'tom_amount',          // Taxe ordures ménagères (jamais taxée)
        'montant_encaisse',    // Total encaissé = loyer_ttc + charges_ttc + TOM

        // ── Commission agence ──────────────────────────────────────────────
        'taux_commission_applique', // % figé à la création
        'commission_agence',        // commission HT
        'tva_commission',           // TVA 18% sur commission HT
        'commission_ttc',           // commission HT + TVA

        // ── Nets propriétaire ──────────────────────────────────────────────
        'net_proprietaire',          // montant_encaisse - commission_ttc
        'brs_amount',                // BRS retenu (Art. 196bis CGI SN)
        'taux_brs_applique',         // Taux BRS figé (0, 5 ou 15%)
        'net_a_verser_proprietaire', // net_proprietaire - brs_amount

        // ── Snapshot fiscal immuable ───────────────────────────────────────
        'regime_fiscal_snapshot',   // JSON — photographie de tous les paramètres

        // ── Informations paiement ──────────────────────────────────────────
        'periode',             // Date du mois concerné (format Y-m-01)
        'date_paiement',       // Date effective d'encaissement
        'mode_paiement',       // especes|virement|wave|orange_money|cheque
        'statut',              // en_attente|valide|annule
        'reference_paiement',  // Référence unique du paiement
        'reference_bail',      // Référence du bail (dénormalisée pour la quittance)

        // ── Champs spéciaux ────────────────────────────────────────────────
        // DEUX champs caution coexistent intentionnellement :
        //   caution_percue  = montant réellement versé par le locataire (input formulaire)
        //   caution_montant = montant contractuel calculé par FiscalService depuis Contrat.caution
        // → Afficher caution_percue dans les documents officiels (reçu réel).
        // → Utiliser caution_montant pour les calculs FiscalResult (nets, totaux).
        'caution_percue',              // Reçu réel — peut différer du contractuel
        'est_premier_paiement',        // Flag premier mois du bail

        // ── Frais d'entrée (premier paiement uniquement, 0 sinon) ─────────
        'frais_agence_ht',             // Honoraires agence HT à la signature
        'tva_frais_agence',            // TVA 18% sur honoraires
        'frais_agence_ttc',            // frais_agence_ht + tva_frais_agence
        'caution_montant',             // Caution contractuelle depuis FiscalService
        'total_encaissement_initial',  // montant_encaisse + frais_agence_ttc + caution_montant

        // ── Nets consolidés (calculés par FiscalService, jamais dans les vues) ─
        'montant_net_locataire',       // total_encaissement_initial - brs_amount
        'montant_net_bailleur',        // net_a_verser_proprietaire [+ caution si remise]

        // ── DGID — snapshot premier paiement (0 sur tous les paiements récurrents) ─
        'dgid_droits_enregistrement',  // assiette × taux% (non nul uniquement au 1er paiement)
        'dgid_timbre_fiscal',          // timbre fiscal fixe (2 000 FCFA)
        'dgid_total',                  // droits_enregistrement + timbre_fiscal

        'notes',               // Observations libres

        // ── Annulation ────────────────────────────────────────────────────
        'annule_le',
        'annule_par',
    ];

    // ── Casts ─────────────────────────────────────────────────────────────────

    protected $casts = [
        'periode'                    => 'date',
        'date_paiement'              => 'date',
        'annule_le'                  => 'datetime',

        // Montants
        'loyer_nu'                   => 'decimal:2',
        'loyer_ht'                   => 'decimal:2',
        'tva_loyer'                  => 'decimal:2',
        'loyer_ttc'                  => 'decimal:2',
        'charges_amount'             => 'decimal:2',
        'tva_charges'                => 'decimal:2',
        'charges_ttc'                => 'decimal:2',
        'tom_amount'                 => 'decimal:2',
        'montant_encaisse'           => 'decimal:2',
        'taux_commission_applique'   => 'decimal:2',
        'commission_agence'          => 'decimal:2',
        'tva_commission'             => 'decimal:2',
        'commission_ttc'             => 'decimal:2',
        'net_proprietaire'           => 'decimal:2',
        'brs_amount'                 => 'decimal:2',
        'taux_brs_applique'          => 'decimal:2',
        'net_a_verser_proprietaire'  => 'decimal:2',
        'caution_percue'             => 'decimal:2',
        'frais_agence_ht'            => 'decimal:2',
        'tva_frais_agence'           => 'decimal:2',
        'frais_agence_ttc'           => 'decimal:2',
        'caution_montant'            => 'decimal:2',
        'total_encaissement_initial' => 'decimal:2',
        'montant_net_locataire'        => 'decimal:2',
        'montant_net_bailleur'         => 'decimal:2',
        'dgid_droits_enregistrement'   => 'decimal:2',
        'dgid_timbre_fiscal'           => 'decimal:2',
        'dgid_total'                   => 'decimal:2',

        // Flags
        'est_premier_paiement'       => 'boolean',

        // JSON
        'regime_fiscal_snapshot'     => 'array',
    ];

    // ── Auto-injection agency_id ───────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $paiement) {
            if (empty($paiement->agency_id) && Auth::check()) {
                $paiement->agency_id = Auth::user()->agency_id;
            }
        });
    }

    // ── Relations ─────────────────────────────────────────────────────────────

    public function agency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function contrat(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contrat::class);
    }

    public function annulePar(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'annule_par');
    }

    public function quittance(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Quittance::class);
    }

    /**
     * Dépenses de gestion engagées par l'agence pour ce mois de gestion.
     * (plombier, électricien, gardien, etc. — réglés pour le compte du bailleur)
     */
    public function depenses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DepenseGestion::class);
    }

    // ── Accesseurs calculés ───────────────────────────────────────────────────

    /**
     * Net final à reverser au bailleur après déduction des dépenses de gestion.
     *
     * Principe d'isolation (invariant métier) :
     *   - Le locataire paie toujours montant_net_locataire — JAMAIS modifié.
     *   - Les dépenses s'imputent UNIQUEMENT sur la part bailleur.
     *
     * Formule :
     *   net_final_bailleur = montant_net_bailleur − SUM(depenses_gestion.montant)
     *
     * Exemple :
     *   montant_net_bailleur = 264 600 F
     *   Facture plombier     =  25 000 F
     *   → net_final_bailleur = 239 600 F
     *
     * Note : charge les dépenses si pas déjà eager-loadées (relation depenses).
     */
    public function getNetFinalBailleurAttribute(): float
    {
        $totalDepenses = (float) $this->depenses->sum('montant');
        return round((float) ($this->montant_net_bailleur ?? $this->net_a_verser_proprietaire ?? 0) - $totalDepenses, 2);
    }

    /**
     * Somme des dépenses de gestion du mois (alias pratique pour les vues).
     */
    public function getTotalDepensesAttribute(): float
    {
        return (float) $this->depenses->sum('montant');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeValides($query)
    {
        return $query->where('statut', 'valide');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeAnnules($query)
    {
        return $query->where('statut', 'annule');
    }

    public function scopeEnRetard($query)
    {
        return $query->where('statut', 'en_attente')
                     ->where('periode', '<', now()->startOfMonth()->toDateString());
    }

    public function scopePourPeriode($query, string $periode)
    {
        return $query->where('periode', $periode);
    }

    // ── Accesseurs ────────────────────────────────────────────────────────────

    /**
     * Mois concerné au format Y-m (ex: "2025-06"), calculé depuis le champ `periode`.
     * Utilisé par QuittanceService pour remplir quittances.mois_concerne.
     */
    public function getMoisConcerneAttribute(): string
    {
        return $this->periode
            ? \Carbon\Carbon::instance($this->periode)->format('Y-m')
            : '';
    }

    /**
     * Label lisible du mode de paiement.
     */
    public function getModePaiementLabelAttribute(): string
    {
        return match($this->mode_paiement) {
            'especes'      => 'Espèces',
            'virement'     => 'Virement bancaire',
            'wave'         => 'Wave',
            'orange_money' => 'Orange Money',
            'free_money'   => 'Free Money',
            'cheque'       => 'Chèque',
            default        => ucfirst($this->mode_paiement ?? '—'),
        };
    }

    /**
     * Label lisible du statut.
     */
    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'valide'     => 'Validé',
            'en_attente' => 'En attente',
            'annule'     => 'Annulé',
            default      => ucfirst($this->statut ?? '—'),
        };
    }

    // ── Méthode utilitaire statique ────────────────────────────────────────────

    /**
     * Calcule les montants d'un paiement depuis les paramètres bruts.
     * Utilisé par le DemoDataSeeder et les tests.
     */
    public static function calculerMontants(
        float $loyerNu,
        float $tauxCommission,
        float $chargesAmount = 0,
        float $tomAmount     = 0,
    ): array {
        $montantEncaisse = $loyerNu + $chargesAmount + $tomAmount;
        $commissionHt    = round($loyerNu * ($tauxCommission / 100), 2);
        $tvaCommission   = round($commissionHt * 0.18, 2);
        $commissionTtc   = $commissionHt + $tvaCommission;
        $netProprietaire = $montantEncaisse - $commissionTtc;

        return [
            'loyer_nu'         => $loyerNu,
            'charges_amount'   => $chargesAmount,
            'tom_amount'       => $tomAmount,
            'montant_encaisse' => $montantEncaisse,
            'commission_ht'    => $commissionHt,
            'tva'              => $tvaCommission,
            'commission_ttc'   => $commissionTtc,
            'net_proprietaire' => $netProprietaire,
        ];
    }

    // ── Constantes ────────────────────────────────────────────────────────────

    public const MODES_PAIEMENT = [
        'especes'      => 'Espèces',
        'virement'     => 'Virement bancaire',
        'wave'         => 'Wave',
        'orange_money' => 'Orange Money',
        'free_money'   => 'Free Money',
        'cheque'       => 'Chèque',
    ];

    public const STATUTS = [
        'en_attente' => 'En attente',
        'valide'     => 'Validé',
        'annule'     => 'Annulé',
    ];
}