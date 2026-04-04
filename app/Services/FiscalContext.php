<?php

namespace App\Services;

use App\Models\Contrat;

/**
 * FiscalContext — Paramètres d'entrée du moteur de calcul fiscal.
 *
 * Cet objet encapsule tous les paramètres nécessaires pour calculer
 * la ventilation fiscale complète d'un paiement de loyer.
 *
 * UTILISATION :
 *   $ctx = FiscalContext::fromContrat($contrat);
 *   $result = FiscalService::calculer($ctx);
 *
 * RÈGLES D'ASSIETTE :
 *   - TVA loyer → UNIQUEMENT sur loyer_nu (jamais sur charges ni TOM)
 *   - Commission → UNIQUEMENT sur loyer_nu (jamais sur charges ni TOM)
 *   - BRS → sur loyer TTC (loyer_nu + tva_loyer)
 *   - TOM et charges → hors champ TVA et hors commission
 */
final class FiscalContext
{
    public function __construct(
        // ── Ventilation du loyer ─────────────────────────────────────────
        public readonly float  $loyerNu,            // Loyer hors charges et hors TOM
        public readonly float  $chargesAmount,       // Charges récupérables (eau, élec...)
        public readonly float  $tomAmount,           // Taxe ordures ménagères

        // ── Type de bail et caractéristique du bien ──────────────────────
        public readonly string $typeBail,            // habitation|commercial|mixte|saisonnier
        public readonly bool   $estMeuble,           // Bien meublé → TVA loyer si habitation

        // ── Commission agence ────────────────────────────────────────────
        public readonly float  $tauxCommission,      // % sur loyer_nu uniquement
        public readonly float  $tauxTvaCommission = 18.0, // Toujours 18% (Art. 357 CGI SN)

        // ── TVA loyer (surchargeable) ────────────────────────────────────
        // Calculé auto par loyerEstAssujetti(), mais peut être forcé manuellement
        public readonly ?float $tauxTvaLoyerOverride = null,

        // ── BRS — Retenue à la Source ────────────────────────────────────
        public readonly bool   $locataireEstEntreprise = false,
        public readonly ?float $tauxBrsLocataire = null,  // Override locataire
        public readonly ?float $tauxBrsContrat   = null,  // Override contrat (priorité max)
    ) {}

    /**
     * Construit un FiscalContext depuis un Contrat Eloquent chargé.
     *
     * Relations requises (eager load avant appel) :
     *   - contrat.bien (meuble, taux_commission, type)
     *   - contrat.locataire.locataire (est_entreprise, taux_brs_override)
     */
    public static function fromContrat(Contrat $contrat): self
    {
        $bien      = $contrat->bien;
        $locataire = $contrat->locataire?->locataire; // relation User → Locataire

        return new self(
            loyerNu:              (float) ($contrat->loyer_nu_effectif ?? $contrat->loyer_nu ?? $contrat->loyer_contractuel),
            chargesAmount:        (float) ($contrat->charges_mensuelles ?? 0),
            tomAmount:            (float) ($contrat->tom_amount ?? 0),
            typeBail:             $contrat->type_bail ?? 'habitation',
            estMeuble:            (bool)  ($bien?->meuble ?? false),
            tauxCommission:       (float) ($bien?->taux_commission ?? 0),
            tauxTvaCommission:    18.0,
            // Si l'agence a surchargé manuellement sur le contrat
            tauxTvaLoyerOverride: $contrat->taux_tva_loyer > 0 && $contrat->loyer_assujetti_tva
                                    ? (float) $contrat->taux_tva_loyer
                                    : null,
            locataireEstEntreprise: (bool) ($locataire?->est_entreprise ?? false),
            tauxBrsLocataire:     $locataire?->taux_brs_override !== null
                                    ? (float) $locataire->taux_brs_override
                                    : null,
            tauxBrsContrat:       $contrat->taux_brs_manuel !== null
                                    ? (float) $contrat->taux_brs_manuel
                                    : null,
        );
    }

    /**
     * Construit un FiscalContext depuis une Request (formulaire de paiement).
     * Permet l'aperçu fiscal en temps réel (endpoint AJAX fiscalPreview).
     */
    public static function fromRequest(
        float  $loyerNu,
        float  $chargesAmount,
        float  $tomAmount,
        string $typeBail,
        bool   $estMeuble,
        float  $tauxCommission,
        bool   $locataireEstEntreprise = false,
        ?float $tauxBrsOverride = null,
    ): self {
        return new self(
            loyerNu:               $loyerNu,
            chargesAmount:         $chargesAmount,
            tomAmount:             $tomAmount,
            typeBail:              $typeBail,
            estMeuble:             $estMeuble,
            tauxCommission:        $tauxCommission,
            locataireEstEntreprise: $locataireEstEntreprise,
            tauxBrsLocataire:      $tauxBrsOverride,
        );
    }
}