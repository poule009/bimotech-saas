<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Paiement extends Model
{
    use HasFactory;
    protected $fillable = [
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
        'statut',
        'notes',
    ];

    protected $casts = [
        'periode'             => 'date',
        'date_paiement'       => 'date',
        'montant_encaisse'    => 'decimal:2',
        'commission_agence'   => 'decimal:2',
        'tva_commission'      => 'decimal:2',
        'commission_ttc'      => 'decimal:2',
        'net_proprietaire'    => 'decimal:2',
        'caution_percue'      => 'decimal:2',
        'est_premier_paiement'=> 'boolean',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    // Accès rapide au bien via le contrat
    public function bien()
    {
        return $this->hasOneThrough(Bien::class, Contrat::class, 'id', 'id', 'contrat_id', 'bien_id');
    }

    // ─── Constantes métier ───────────────────────────────────────────────────

    const TAUX_TVA = 18.0;   // TVA Sénégal en %
    const TAUX_TVA_FACTEUR = 0.18;

    // ─── Calculs métier (méthodes statiques réutilisables) ───────────────────

    /**
     * Calcule l'ensemble des montants à partir du loyer brut et du taux de commission.
     * Centraliser ici évite de dupliquer la logique dans le Controller et les tests.
     *
     * @return array ['commission_ht', 'tva', 'commission_ttc', 'net_proprietaire']
     */
    public static function calculerMontants(float $montantEncaisse, float $tauxCommission): array
    {
        $commissionHT  = round($montantEncaisse * ($tauxCommission / 100), 2);
        $tva           = round($commissionHT * self::TAUX_TVA_FACTEUR, 2);
        $commissionTTC = round($commissionHT + $tva, 2);
        $netProprietaire = round($montantEncaisse - $commissionTTC, 2);

        return [
            'commission_ht'    => $commissionHT,
            'tva'              => $tva,
            'commission_ttc'   => $commissionTTC,
            'net_proprietaire' => $netProprietaire,
        ];
    }

    // ─── Accesseurs pratiques ────────────────────────────────────────────────

    public function getPeriodeLabelAttribute(): string
    {
        return $this->periode->translatedFormat('F Y');
    }

    public function getReferencePaiementGenereeAttribute(): string
    {
        return 'QUITT-' . $this->contrat_id . '-' . $this->periode->format('Ym') . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}