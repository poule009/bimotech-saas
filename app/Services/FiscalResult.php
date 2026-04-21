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
 *  loyer_ht                    ← loyer nu hors TVA (assiette commission)
 *  + tva_loyer (0 ou 18%)      ← sur (loyer_ht + TOM) — Art. 354 CGI SN
 *  = loyer_ttc
 *  + charges_amount            ← hors TVA, hors commission, hors BRS
 *  + tom_amount                ← inclus dans assiette TVA et BRS (pas dans commission)
 *  = montant_encaisse          ← total que le locataire doit régler
 *  ─────────────────────────────────────────────────────────────────
 *  commission_ht               ← % sur loyer_ht uniquement (jamais sur TVA/TOM/charges)
 *  + tva_commission            ← 18% sur commission_ht (Art. 357 CGI SN)
 *  = commission_ttc
 *  ─────────────────────────────────────────────────────────────────
 *  net_proprietaire            ← montant_encaisse - commission_ttc
 *  - brs_amount                ← % × (loyer_ttc + TOM) si locataire entreprise (Art. 156 CGI SN)
 *  = net_a_verser_proprietaire ← montant effectivement viré au propriétaire
 *  ─────────────────────────────────────────────────────────────────
 *  [Premier paiement uniquement]
 *  frais_agence_ht             ← honoraires agence HT (one-shot, signature)
 *  + tva_frais_agence (18%)    ← TVA sur honoraires
 *  = frais_agence_ttc
 *  + caution_montant           ← dépôt de garantie (non taxable)
 *  + montant_encaisse
 *  = total_encaissement_initial ← total versé à l'entrée dans les lieux
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
        public readonly float  $tvaCharges,     // TVA sur charges forfait (0 si débours ou bail exonéré)
        public readonly float  $chargesTtc,     // chargesAmount + tvaCharges
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

        // ── Frais d'entrée (0 pour les paiements récurrents) ──────────────
        public readonly float  $fraisAgenceHt           = 0.0,
        public readonly float  $tvaFraisAgence          = 0.0,
        public readonly float  $fraisAgenceTtc          = 0.0,
        public readonly float  $cautionMontant          = 0.0,
        public readonly float  $totalEncaissementInitial = 0.0,

        // ── Nets consolidés ───────────────────────────────────────────────
        // Calculés une seule fois par FiscalService pour éviter toute logique dans les vues.
        public readonly float  $netLocataire = 0.0,  // total_encaissement_initial - brs_amount
        public readonly float  $netBailleur  = 0.0,  // net_a_verser_proprietaire [+ caution si remise]

        // ── Droits d'enregistrement DGID (CGI SN art. 442) ───────────────
        // Non nuls UNIQUEMENT au premier paiement (avecDgid=true dans FiscalContext).
        // Sur tous les paiements récurrents : 0.0 → section DGID masquée côté vues.
        // Ces montants NE modifient PAS montant_encaisse ni net_locataire :
        // ils sont une obligation fiscale séparée, réglée directement à la DGID.
        public readonly float  $dgidDroitsEnregistrement = 0.0,
        public readonly float  $dgidTimbreFiscal         = 0.0,
        public readonly float  $dgidTotal                = 0.0,
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
            'tva_charges'                => $this->tvaCharges,
            'charges_ttc'                => $this->chargesTtc,
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

            // Frais d'entrée
            'frais_agence_ht'            => $this->fraisAgenceHt,
            'tva_frais_agence'           => $this->tvaFraisAgence,
            'frais_agence_ttc'           => $this->fraisAgenceTtc,
            'caution_montant'            => $this->cautionMontant,
            'total_encaissement_initial' => $this->totalEncaissementInitial,

            // Nets consolidés
            'montant_net_locataire'      => $this->netLocataire,
            'montant_net_bailleur'       => $this->netBailleur,

            // DGID (0 pour les paiements récurrents — sécurité affichage garantie)
            'dgid_droits_enregistrement' => $this->dgidDroitsEnregistrement,
            'dgid_timbre_fiscal'         => $this->dgidTimbreFiscal,
            'dgid_total'                 => $this->dgidTotal,

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
            'frais_agence_ht'            => $this->fraisAgenceHt,
            'tva_frais_agence'           => $this->tvaFraisAgence,
            'frais_agence_ttc'           => $this->fraisAgenceTtc,
            'caution_montant'            => $this->cautionMontant,
            'total_encaissement_initial' => $this->totalEncaissementInitial,
            'montant_net_locataire'      => $this->netLocataire,
            'montant_net_bailleur'       => $this->netBailleur,
            'dgid_droits_enregistrement' => $this->dgidDroitsEnregistrement,
            'dgid_timbre_fiscal'         => $this->dgidTimbreFiscal,
            'dgid_total'                 => $this->dgidTotal,
            'regime_fiscal_snapshot'     => $this->toArray(),
        ];
    }
}