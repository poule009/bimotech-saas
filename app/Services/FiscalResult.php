<?php

namespace App\Services;

/**
 * FiscalResult — Résultat complet du calcul fiscal d'un paiement.
 *
 * Objet immuable (readonly). Toutes les propriétés sont calculées une seule fois
 * par FiscalService::calculer() et ne peuvent plus être modifiées ensuite.
 *
 * STRUCTURE DU DÉCOMPTE :
 *
 *  loyer_ht                    ← loyer hors TVA (assiette commission et BRS)
 *  + tva_loyer (0 ou 18%)      ← UNIQUEMENT sur loyer_ht, jamais sur charges/TOM
 *  = loyer_ttc
 *  + charges_amount            ← hors TVA, hors commission
 *  + tom_amount                ← hors TVA, hors commission
 *  = montant_encaisse          ← total que le locataire doit régler
 *  ─────────────────────────────────────────────────────────────────
 *  commission_ht               ← % sur loyer_ht uniquement
 *  + tva_commission            ← 18% sur commission_ht (Art. 357 CGI SN)
 *  = commission_ttc
 *  ─────────────────────────────────────────────────────────────────
 *  net_proprietaire            ← montant_encaisse - commission_ttc
 *  - brs_amount                ← % × loyer_ttc si locataire entreprise (Art. 196bis)
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
        public readonly bool   $loyerAssujetti,       // TVA loyer s'applique ?
        public readonly string $regimeFiscal,          // label lisible pour UI/PDF
        public readonly float  $tauxTvaLoyerApplique,  // 0 ou 18
    ) {}

    /**
     * Sérialise le résultat en tableau pour persistance (JSON snapshot).
     * Ce snapshot est stocké dans paiements.regime_fiscal_snapshot.
     * Il garantit que la quittance reste exacte même si les taux changent après.
     */
    public function toArray(): array
    {
        return [
            // Loyer
            'loyer_ht'                   => $this->loyerHt,
            'tva_loyer'                  => $this->tvaLoyer,
            'loyer_ttc'                  => $this->loyerTtc,
            'taux_tva_loyer_applique'    => $this->tauxTvaLoyerApplique,
            'loyer_assujetti'            => $this->loyerAssujetti,

            // Ventilation
            'charges_amount'             => $this->chargesAmount,
            'tom_amount'                 => $this->tomAmount,
            'montant_encaisse'           => $this->montantEncaisse,

            // Commission
            'commission_ht'              => $this->commissionHt,
            'tva_commission'             => $this->tvaCommission,
            'commission_ttc'             => $this->commissionTtc,

            // Nets
            'net_proprietaire'           => $this->netProprietaire,

            // BRS
            'taux_brs_applique'          => $this->tauxBrsApplique,
            'brs_amount'                 => $this->brsAmount,
            'brs_applicable'             => $this->brsApplicable,
            'net_a_verser_proprietaire'  => $this->netAVerserProprietaire,

            // Méta
            'regime_fiscal'              => $this->regimeFiscal,
            'calcule_le'                 => now()->toIso8601String(),
        ];
    }

    /**
     * Convertit vers le tableau de champs prêts à être passés à Paiement::create().
     * Utilisé par PaiementService et GenerateMonthlyRent.
     */
    public function toPaiementFields(): array
    {
        return [
            'loyer_nu'                   => $this->loyerHt,
            'loyer_ht'                   => $this->loyerHt,
            'tva_loyer'                  => $this->tvaLoyer,
            'loyer_ttc'                  => $this->loyerTtc,
            'charges_amount'             => $this->chargesAmount,
            'tom_amount'                 => $this->tomAmount,
            'montant_encaisse'           => $this->montantEncaisse,
            'commission_agence'          => $this->commissionHt,
            'tva_commission'             => $this->tvaCommission,
            'commission_ttc'             => $this->commissionTtc,
            'net_proprietaire'           => $this->netProprietaire,
            'brs_amount'                 => $this->brsAmount,
            'taux_brs_applique'          => $this->tauxBrsApplique,
            'net_a_verser_proprietaire'  => $this->netAVerserProprietaire,
            'regime_fiscal_snapshot'     => $this->toArray(),
        ];
    }
}