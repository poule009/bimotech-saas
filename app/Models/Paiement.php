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

    /**
     * SÉCURITÉ — Mass Assignment :
     *
     * Les colonnes suivantes sont INTENTIONNELLEMENT absentes de $fillable :
     *
     * - `statut`             : ne doit être passé à 'valide' que par le contrôleur après vérification
     * - `commission_agence`  : valeur calculée — ne doit jamais venir d'un formulaire
     * - `tva_commission`     : idem
     * - `commission_ttc`     : idem
     * - `net_proprietaire`   : idem
     * - `receipt_path`       : chemin fichier serveur — ne doit jamais être injecté par l'extérieur
     *
     * Ces colonnes sont assignées explicitement dans PaiementController::store()
     * via un tableau construit côté serveur, jamais via $request->all() ou fill().
     */
    protected $fillable = [
        'agency_id',
        'contrat_id',
        'periode',
        'loyer_nu',
        'charges_amount',
        'tom_amount',
        'montant_encaisse',
        'mode_paiement',
        'taux_commission_applique',
        'caution_percue',
        'est_premier_paiement',
        'date_paiement',
        'reference_paiement',
        'reference_bail',
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
            if ((empty($paiement->loyer_nu) || $paiement->loyer_nu == 0) && ! empty($paiement->montant_encaisse)) {
                $paiement->loyer_nu = (float) $paiement->montant_encaisse
                    - (float) ($paiement->charges_amount ?? 0)
                    - (float) ($paiement->tom_amount ?? 0);
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

    public function getReferenceBailAfficheeAttribute(): string
    {
        if (! empty($this->reference_bail)) {
            return $this->reference_bail;
        }
        return 'BAIL-' . str_pad((string) $this->contrat_id, 5, '0', STR_PAD_LEFT);
    }

    public function getMontantEnLettresAttribute(): string
    {
        return NombreEnLettres::convertir((float) $this->montant_encaisse);
    }

    public function getNetEnLettresAttribute(): string
    {
        return NombreEnLettres::convertir((float) $this->net_proprietaire);
    }

    public function getLoyerNuEnLettresAttribute(): string
    {
        return NombreEnLettres::convertir((float) $this->loyer_nu);
    }

    // ── Calcul des montants (méthode statique) ────────────────────────────

    /**
     * Calcule la ventilation complète d'un paiement.
     * La commission s'applique sur le loyer nu uniquement.
     *
     * @return array{loyer_nu: float, charges_amount: float, tom_amount: float,
     *               montant_encaisse: float, commission_ht: float, tva: float,
     *               commission_ttc: float, net_proprietaire: float}
     */
    public static function calculerMontants(
        float $loyerNu,
        float $tauxCommission,
        float $chargesAmount = 0.0,
        float $tomAmount     = 0.0,
        float $tauxTva       = 18.0,
    ): array {
        $commissionHt    = round($loyerNu * $tauxCommission / 100, 2);
        $tva             = round($commissionHt * $tauxTva / 100, 2);
        $commissionTtc   = round($commissionHt + $tva, 2);
        $totalEncaisse   = round($loyerNu + $chargesAmount + $tomAmount, 2);
        $netProprietaire = round($totalEncaisse - $commissionTtc, 2);

        return [
            'loyer_nu'         => $loyerNu,
            'charges_amount'   => $chargesAmount,
            'tom_amount'       => $tomAmount,
            'montant_encaisse' => $totalEncaisse,
            'commission_ht'    => $commissionHt,
            'tva'              => $tva,
            'commission_ttc'   => $commissionTtc,
            'net_proprietaire' => $netProprietaire,
        ];
    }
}