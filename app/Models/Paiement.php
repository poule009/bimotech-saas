<?php

namespace App\Models;

use App\Models\Scopes\AgencyScope;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Paiement extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'agency_id',
        'contrat_id',
        'periode',
        'montant_encaisse',
        'mode_paiement',
        'taux_commission_applique',
        'commission_agence',
        'tva_commission',
        'commission_ttc',
        'net_proprietaire',
        'caution_percue',
        'est_premier_paiement',
        'date_paiement',
        'reference_paiement',
        'receipt_path',
        'statut',
        'notes',
    ];

    protected $casts = [
        'periode'              => 'date',
        'date_paiement'        => 'date',
        'est_premier_paiement' => 'boolean',
        'montant_encaisse'     => 'decimal:2',
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

    // ── Calcul des montants avec TVA ──────────────────────────────────────

    public static function calculerMontants(float $montant, float $tauxCommission, float $tauxTva = 18.0): array
    {
        $commissionHt  = round($montant * $tauxCommission / 100, 2);
        $tva           = round($commissionHt * $tauxTva / 100, 2);
        $commissionTtc = round($commissionHt + $tva, 2);
        $netProprietaire = round($montant - $commissionTtc, 2);

        return [
            // Clés métier actuelles
            'commission_agence'        => $commissionHt,
            'tva_commission'           => $tva,
            'commission_ttc'           => $commissionTtc,
            'net_proprietaire'         => $netProprietaire,
            'taux_commission_applique' => $tauxCommission,

            // Compatibilité tests/unit legacy
            'commission_ht'            => $commissionHt,
            'tva'                      => $tva,
        ];
    }
}