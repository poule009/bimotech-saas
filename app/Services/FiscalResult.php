<?php

namespace App\Services;

/**
 * FiscalResult — Résultat complet du calcul fiscal d'un paiement.
 *
 * Toutes les propriétés sont en lecture seule (readonly).
 * Une fois calculé, le résultat est immuable.
 *
 * STRUCTURE DU DÉCOMPTE :
 *
 *  loyer_ht                    ← loyer hors TVA (assiette commission et BRS)
 *  + tva_loyer (0 ou 18%)      ← UNIQUEMENT sur loyer_ht, jamais sur charges/TOM
 *  = loyer_ttc
 *  + charges_amount            ← hors TVA, hors commission
 *  + tom_amount                ← hors TVA, hors commission
 *  = montant_encaisse          ← total que le locataire doit régler
 *  ─────────────────────────────────────────
 *  commission_ht               ← % sur loyer_ht uniquement
 *  + tva_commission            ← 18% sur commission_ht (Art. 357 CGI SN)
 *  = commission_ttc
 *  ─────────────────────────────────────────
 *  net_proprietaire            ← montant_encaisse - commission_ttc
 *  - brs_amount                ← 15% × loyer_ttc si locataire entreprise
 *  = net_a_verser_proprietaire ← montant effectivement viré au propriétaire
 */
final class FiscalResult
{
    public function __construct(
        // ── Loyer ────────────────────────────────────────────────────────
        public readonly float  $loyerHt,
        public readonly float  $tvaLoyer,
        public readonly float  $loyerTtc,

        // ── Ventilation complète ──────────────────────────────────────────
        public readonly float  $chargesAmount,
        public readonly float  $tomAmount,
        public readonly float  $montantEncaisse,

        // ── Commission agence ─────────────────────────────────────────────
        public readonly float  $commissionHt,
        public readonly float  $tvaCommission,
        public readonly float  $commissionTtc,

        // ── Nets ──────────────────────────────────────────────────────────
        public readonly float  $netProprietaire,

        // ── BRS ───────────────────────────────────────────────────────────
        public readonly float  $tauxBrsApplique,
        public readonly float  $brsAmount,
        public readonly bool   $brsApplicable,
        public readonly float  $netAVerserProprietaire,

        // ── Flags utiles ──────────────────────────────────────────────────
        public readonly bool   $loyerAssujetti,     // TVA loyer s'applique ?
        public readonly string $regimeFiscal,        // label lisible pour l'UI
        public readonly float  $tauxTvaLoyerApplique, // 0 ou 18
    ) {}

    /**
     * Sérialise le résultat en tableau pour persistance en base (JSON snapshot).
     * Ce snapshot est stocké dans paiements.regime_fiscal_snapshot.
     */
    public function toArray(): array
    {
        return [
            'loyer_ht'                   => $this->loyerHt,
            'tva_loyer'                  => $this->tvaLoyer,
            'loyer_ttc'                  => $this->loyerTtc,
            'charges_amount'             => $this->chargesAmount,
            'tom_amount'                 => $this->tomAmount,
            'montant_encaisse'           => $this->montantEncaisse,
            'commission_ht'              => $this->commissionHt,
            'tva_commission'             => $this->tvaCommission,
            'commission_ttc'             => $this->commissionTtc,
            'net_proprietaire'           => $this->netProprietaire,
            'taux_brs_applique'          => $this->tauxBrsApplique,
            'brs_amount'                 => $this->brsAmount,
            'brs_applicable'             => $this->brsApplicable,
            'net_a_verser_proprietaire'  => $this->netAVerserProprietaire,
            'loyer_assujetti'            => $this->loyerAssujetti,
            'regime_fiscal'              => $this->regimeFiscal,
            'taux_tva_loyer_applique'    => $this->tauxTvaLoyerApplique,
            'snapshot_at'                => now()->toISOString(),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}