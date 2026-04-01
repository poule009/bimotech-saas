<?php

namespace App\Models;

use App\Models\Scopes\AgencyScope;
use App\Models\Traits\LogsActivity;
use App\Services\NombreEnLettres;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Paiement extends Model
{
    use HasFactory, LogsActivity;

    // ── Modes de paiement disponibles au Sénégal ──────────────────────────
    public const MODES_PAIEMENT = [
        'especes'      => 'Espèces',
        'virement'     => 'Virement bancaire',
        'cheque'       => 'Chèque',
        'wave'         => 'Wave',
        'orange_money' => 'Orange Money',
        'free_money'   => 'Free Money',
        'mobile_money' => 'Mobile Money (autre)',
    ];

    public const STATUTS = [
        'en_attente' => 'En attente',
        'valide'     => 'Validé',
        'annule'     => 'Annulé',
        'impaye'     => 'Impayé',
        'unpaid'     => 'Non payé',
    ];

    protected $fillable = [
        'agency_id',
        'contrat_id',
        'periode',
        // ── Ventilation loyer ──────────────────────────────────────────
        'montant_encaisse',        // total encaissé (loyer_nu + charges + tom)
        'loyer_nu',                // loyer hors charges et hors TOM
        'charges_amount',          // charges récupérables du mois
        'tom_amount',              // Taxe sur les Ordures Ménagères
        // ── Calculs commission ─────────────────────────────────────────
        'mode_paiement',
        'taux_commission_applique',
        'commission_agence',       // commission HT sur loyer_nu
        'tva_commission',          // TVA 18% sur commission HT
        'commission_ttc',          // commission HT + TVA
        'net_proprietaire',        // montant_encaisse - commission_ttc
        // ── Caution ───────────────────────────────────────────────────
        'caution_percue',
        'est_premier_paiement',
        // ── Références ────────────────────────────────────────────────
        'date_paiement',
        'reference_paiement',      // référence quittance auto-générée
        'reference_bail',          // référence bail saisie manuellement
        'receipt_path',
        'statut',
        'notes',
    ];

    protected $casts = [
        'periode'              => 'date',
        'date_paiement'        => 'date',
        'est_premier_paiement' => 'boolean',
        'montant_encaisse'     => 'decimal:2',
        'loyer_nu'             => 'decimal:2',
        'charges_amount'       => 'decimal:2',
        'tom_amount'           => 'decimal:2',
        'commission_agence'    => 'decimal:2',
        'tva_commission'       => 'decimal:2',
        'commission_ttc'       => 'decimal:2',
        'net_proprietaire'     => 'decimal:2',
        'caution_percue'       => 'decimal:2',
    ];

    // ── Global Scope ──────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::addGlobalScope(new AgencyScope());

        static::creating(function (Paiement $paiement) {
            if (empty($paiement->agency_id) && Auth::check()) {
                $paiement->agency_id = Auth::user()->agency_id;
            }

            // Auto-calcul loyer_nu si non fourni
            if (empty($paiement->loyer_nu) || $paiement->loyer_nu == 0) {
                $paiement->loyer_nu = $paiement->montant_encaisse
                    - ($paiement->charges_amount ?? 0)
                    - ($paiement->tom_amount ?? 0);
            }
        });
    }

    // ── Relations ─────────────────────────────────────────────────────────

    public function agency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function contrat(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contrat::class);
    }

    // ── Accesseurs ────────────────────────────────────────────────────────

    /**
     * Référence du bail : priorité à la saisie manuelle, sinon générée.
     */
    public function getReferenceBailAfficheeAttribute(): string
    {
        if (! empty($this->reference_bail)) {
            return $this->reference_bail;
        }

        return 'BAIL-' . str_pad((string) $this->contrat_id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Montant total en lettres (pour la quittance).
     */
    public function getMontantEnLettresAttribute(): string
    {
        return NombreEnLettres::convertir((float) $this->montant_encaisse);
    }

    /**
     * Net propriétaire en lettres (pour la quittance).
     */
    public function getNetEnLettresAttribute(): string
    {
        return NombreEnLettres::convertir((float) $this->net_proprietaire);
    }

    /**
     * Loyer nu en lettres.
     */
    public function getLoyerNuEnLettresAttribute(): string
    {
        return NombreEnLettres::convertir((float) $this->loyer_nu);
    }

    // ── Méthodes statiques de calcul ──────────────────────────────────────

    /**
     * Calcule la ventilation complète d'un paiement.
     *
     * La commission s'applique sur le LOYER NU uniquement
     * (pas sur les charges ni sur la TOM — pratique sénégalaise standard).
     *
     * @param float $loyerNu       Loyer hors charges et hors TOM
     * @param float $tauxCommission Taux en pourcentage (ex: 10 pour 10%)
     * @param float $chargesAmount  Charges récupérables (défaut 0)
     * @param float $tomAmount      Taxe Ordures Ménagères (défaut 0)
     * @param float $tauxTva        Taux TVA (défaut 18%)
     * @return array<string, float>
     */
    public static function calculerMontants(
        float $loyerNu,
        float $tauxCommission,
        float $chargesAmount = 0.0,
        float $tomAmount     = 0.0,
        float $tauxTva       = 18.0,
    ): array {
        $commissionHt  = round($loyerNu * $tauxCommission / 100, 2);
        $tva           = round($commissionHt * $tauxTva / 100, 2);
        $commissionTtc = round($commissionHt + $tva, 2);
        $totalEncaisse = round($loyerNu + $chargesAmount + $tomAmount, 2);
        $netProprietaire = round($totalEncaisse - $commissionTtc, 2);

        return [
            'loyer_nu'        => $loyerNu,
            'charges_amount'  => $chargesAmount,
            'tom_amount'      => $tomAmount,
            'montant_encaisse'=> $totalEncaisse,
            'commission_ht'   => $commissionHt,
            'tva'             => $tva,
            'commission_ttc'  => $commissionTtc,
            'net_proprietaire'=> $netProprietaire,
        ];
    }
}