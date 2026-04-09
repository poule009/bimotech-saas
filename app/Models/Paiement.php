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
        'charges_amount',      // Charges récupérables (jamais taxées)
        'tom_amount',          // Taxe ordures ménagères (jamais taxée)
        'montant_encaisse',    // Total encaissé = loyer_ttc + charges + TOM

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
        'caution_percue',      // Caution encaissée au premier paiement
        'est_premier_paiement',// Flag premier mois du bail
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