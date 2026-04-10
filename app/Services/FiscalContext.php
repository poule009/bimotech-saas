<?php

namespace App\Services;

use App\Models\Contrat;

/**
 * FiscalContext — Objet de transfert de données pour FiscalService::calculer().
 *
 * Encapsule tous les paramètres fiscaux nécessaires au calcul d'un paiement.
 * Immuable (readonly) : construit une seule fois, transmis à FiscalService.
 *
 * Usage :
 *   $ctx    = FiscalContext::fromContrat($contrat);
 *   $result = FiscalService::calculer($ctx);
 */
final class FiscalContext
{
    public function __construct(
        // ── Loyer de base ────────────────────────────────────────────────────
        public readonly float  $loyerNu,           // Loyer hors charges, hors TVA (FCFA)
        public readonly float  $chargesAmount,      // Charges mensuelles (FCFA)
        public readonly float  $tomAmount,          // Taxe sur Opérations Mobilières (FCFA)

        // ── Caractéristiques du contrat ──────────────────────────────────────
        public readonly string $typeBail,           // habitation | commercial | mixte | saisonnier
        public readonly bool   $estMeuble,          // Détermine l'assujettissement TVA habitation

        // ── Locataire ────────────────────────────────────────────────────────
        public readonly bool   $locataireEstEntreprise, // Active le calcul BRS (CGI art. 196bis)

        // ── Taux agence ──────────────────────────────────────────────────────
        public readonly float  $tauxCommission,     // Ex: 10.0 pour 10% (format pourcentage)
        public readonly float  $tauxTvaCommission,  // Toujours 18.0 au Sénégal (format pourcentage)

        // ── Overrides fiscaux ────────────────────────────────────────────────
        // null  = FiscalService détermine automatiquement via loyerEstAssujetti()
        // 0.0   = loyer exonéré de TVA (override manuel)
        // 18.0  = loyer assujetti à TVA à 18% (override manuel)
        public readonly ?float $tauxTvaLoyerOverride,

        // null = pas de taux BRS spécifique sur ce contrat
        public readonly ?float $tauxBrsContrat,     // Taux BRS manuel du contrat (format pourcentage)

        // null = pas de taux BRS override sur ce locataire
        public readonly ?float $tauxBrsLocataire,   // Taux BRS override du profil locataire (format pourcentage)
    ) {}

    /**
     * Construit un FiscalContext depuis un Contrat chargé avec ses relations.
     *
     * Prérequis : le contrat doit avoir les relations suivantes chargées :
     *   - bien                  (pour taux_commission, meuble)
     *   - locataire.locataire   (User → profil Locataire, pour est_entreprise et taux_brs_override)
     *
     * Priorité TVA loyer :
     *   1. Override manuel sur le contrat (loyer_assujetti_tva + taux_tva_loyer)
     *   2. Détection automatique par FiscalService::loyerEstAssujetti(typeBail, estMeuble)
     *
     * Priorité taux BRS :
     *   1. taux_brs_manuel sur le contrat
     *   2. taux_brs_override sur le profil locataire
     *   3. Taux légal 15% (appliqué par FiscalService si les deux sont null)
     */
    public static function fromContrat(Contrat $contrat): self
    {
        $bien       = $contrat->bien;
        $locUser    = $contrat->locataire;               // User (rôle locataire)
        $locProfile = $locUser?->locataire;              // Profil Locataire (HasOne sur User)

        // ── Taux TVA loyer ────────────────────────────────────────────────
        // Si le contrat spécifie explicitement l'assujettissement, on l'honore.
        // Sinon (null), FiscalService applique la règle automatique.
        $tauxTvaOverride = null;
        if ($contrat->loyer_assujetti_tva !== null) {
            $tauxTvaOverride = $contrat->loyer_assujetti_tva
                ? (float) ($contrat->taux_tva_loyer ?? FiscalService::TVA_TAUX)
                : 0.0;
        }

        // ── Taux BRS ──────────────────────────────────────────────────────
        $tauxBrsContrat   = null;
        $tauxBrsLocataire = null;

        if ($contrat->brs_applicable && $contrat->taux_brs_manuel !== null) {
            $tauxBrsContrat = (float) $contrat->taux_brs_manuel;
        }

        if ($locProfile?->taux_brs_override !== null) {
            $tauxBrsLocataire = (float) $locProfile->taux_brs_override;
        }

        return new self(
            loyerNu:                (float) ($contrat->loyer_nu ?? 0),
            chargesAmount:          (float) ($contrat->charges_mensuelles ?? 0),
            tomAmount:              (float) ($contrat->tom_amount ?? 0),
            typeBail:               $contrat->type_bail ?? 'habitation',
            estMeuble:              (bool)  ($bien?->meuble ?? false),
            locataireEstEntreprise: (bool)  ($locProfile?->est_entreprise ?? false),
            tauxCommission:         (float) ($bien?->taux_commission ?? FiscalService::COMMISSION_TAUX),
            tauxTvaCommission:      FiscalService::TVA_TAUX,
            tauxTvaLoyerOverride:   $tauxTvaOverride,
            tauxBrsContrat:         $tauxBrsContrat,
            tauxBrsLocataire:       $tauxBrsLocataire,
        );
    }
}