<?php

namespace App\Models;

use App\Models\Scopes\AgencyScope;
use App\Models\Traits\LogsActivity;
use App\Services\FiscalService;
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
     * Les colonnes calculées sont INTENTIONNELLEMENT absentes de $fillable.
     * Elles sont assignées explicitement dans PaiementController::store().
     */
    protected $fillable = [
        'agency_id',
        'contrat_id',
        'periode',
        // Ventilation loyer
        'loyer_ht',
        'tva_loyer',
        'loyer_ttc',
        'loyer_nu',           // alias rétro-compat = loyer_ht
        'charges_amount',
        'tom_amount',
        'montant_encaisse',
        // Mode
        'mode_paiement',
        'taux_commission_applique',
        // Divers
        'caution_percue',
        'est_premier_paiement',
        'date_paiement',
        'reference_paiement',
        'reference_bail',
        'notes',
    ];

    protected $casts = [
        'periode'                   => 'date',
        'date_paiement'             => 'date',
        'est_premier_paiement'      => 'boolean',
        'montant_encaisse'          => 'decimal:2',
        'loyer_ht'                  => 'decimal:2',
        'tva_loyer'                 => 'decimal:2',
        'loyer_ttc'                 => 'decimal:2',
        'loyer_nu'                  => 'decimal:2',
        'charges_amount'            => 'decimal:2',
        'tom_amount'                => 'decimal:2',
        'commission_agence'         => 'decimal:2',
        'tva_commission'            => 'decimal:2',
        'commission_ttc'            => 'decimal:2',
        'net_proprietaire'          => 'decimal:2',
        'brs_amount'                => 'decimal:2',
        'taux_brs_applique'         => 'decimal:2',
        'net_a_verser_proprietaire' => 'decimal:2',
        'caution_percue'            => 'decimal:2',
        'regime_fiscal_snapshot'    => 'array',
    ];

    // ── Global Scope ──────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::addGlobalScope(new AgencyScope());

        static::creating(function (Paiement $paiement) {
            if (empty($paiement->agency_id) && Auth::check()) {
                $paiement->agency_id = Auth::user()->agency_id;
            }

            // Alias rétro-compat : loyer_nu = loyer_ht
            if ((empty($paiement->loyer_nu) || $paiement->loyer_nu == 0)
                && ! empty($paiement->loyer_ht)
            ) {
                $paiement->loyer_nu = $paiement->loyer_ht;
            }

            // Inverse : si loyer_ht non fourni mais loyer_nu l'est
            if ((empty($paiement->loyer_ht) || $paiement->loyer_ht == 0)
                && ! empty($paiement->loyer_nu)
            ) {
                $paiement->loyer_ht = $paiement->loyer_nu;
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

    // ── Accesseurs ────────────────────────────────────────────────────────────

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

    public function getNetAVerserEnLettresAttribute(): string
    {
        return NombreEnLettres::convertir((float) ($this->net_a_verser_proprietaire ?? $this->net_proprietaire));
    }

    public function getLoyerNuEnLettresAttribute(): string
    {
        return NombreEnLettres::convertir((float) ($this->loyer_ht ?? $this->loyer_nu));
    }

    /**
     * Régime fiscal depuis le snapshot ou recontruit depuis les montants.
     */
    public function getRegimeFiscalLabelAttribute(): string
    {
        $snapshot = $this->regime_fiscal_snapshot;
        if ($snapshot && isset($snapshot['regime_fiscal'])) {
            return $snapshot['regime_fiscal'];
        }
        // Fallback : déduire depuis les montants
        if (($this->tva_loyer ?? 0) > 0) {
            return 'Bail commercial/meublé (TVA 18% loyer)';
        }
        return 'Habitation nue (exonéré TVA loyer)';
    }

    // ── Méthode de calcul ────────────────────────────────────────────────────

    /**
     * Méthode de compatibilité — utilisée par les Seeders existants.
     * Préférer FiscalService::calculer(FiscalContext $ctx) pour les nouveaux appels.
     
     *
     * Conservé pour rétro-compatibilité avec DemoDataSeeder, BimoTechSeeder,
     * et les tests existants. Délègue à FiscalService::calculerLegacy().
     *
     * ATTENTION : cette méthode traite toujours comme un bail habitation particulier.
     * Pour les cas commerciaux/meublés/entreprise, utiliser FiscalService directement.
     */
    public static function calculerMontants(
        float $loyerNu,
        float $tauxCommission,
        float $chargesAmount = 0.0,
        float $tomAmount     = 0.0,
        float $tauxTva       = 18.0,
    ): array {
        return FiscalService::calculerLegacy(
            loyerNu: $loyerNu,
            tauxCommission: $tauxCommission,
            chargesAmount: $chargesAmount,
            tomAmount: $tomAmount,
            tauxTva: $tauxTva,
        );
    }
}
