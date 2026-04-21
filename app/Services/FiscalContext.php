<?php

namespace App\Services;

use App\Models\Contrat;
use Carbon\Carbon;

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
        public readonly float  $chargesAmount,      // Charges mensuelles HT (FCFA)
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

        // ── Prorata temporel (premier paiement en cours de mois) ─────────────
        // null = mois complet (coefficient = 1.0)
        public readonly ?Carbon $dateDebutOccupation = null,  // Date d'entrée réelle du locataire
        public readonly ?Carbon $dateFinPeriode      = null,  // Dernier jour du mois concerné

        // ── Frais d'entrée (premier paiement uniquement) ─────────────────────
        // 0.0 pour tous les paiements récurrents
        public readonly float   $fraisAgenceHt  = 0.0,  // Honoraires agence HT (contrat.frais_agence)
        public readonly float   $cautionMontant = 0.0,  // Dépôt de garantie (contrat.caution, non taxable)

        // ── TVA sur charges ──────────────────────────────────────────────────
        // false (défaut) = charges passées en débours (HT, hors TVA)
        // true           = charges facturées en forfait → TVA 18% obligatoire (DGI SN)
        public readonly bool    $chargesAssujettiesATva = false,

        // ── Politique de caution ─────────────────────────────────────────────
        // false (défaut) → caution remise au bailleur (incluse dans netBailleur)
        // true           → caution conservée par l'agence (exclue du versement bailleur)
        public readonly bool    $cautionGardeeParAgence = false,

        // ── Droits d'enregistrement DGID (CGI SN art. 442) ──────────────────
        // avecDgid = true UNIQUEMENT au premier paiement et si non exonéré.
        // Sur tous les paiements récurrents : avecDgid = false → résultats à 0.
        public readonly bool    $avecDgid               = false,
        public readonly bool    $enregistrementExonere  = false,
        // Loyer mensuel de l'assiette DGID = loyer_nu + charges_mensuelles (loyer_contractuel)
        public readonly float   $loyerMensuelDgid       = 0.0,
        // Durée du bail en mois pour l'assiette annuelle (max 12 pour la première année)
        public readonly int     $dureeMoisDgid          = 12,
        // null → FiscalService::dgidTauxDefaut(typeBail) : 1% hab / 2% commercial
        public readonly ?float  $tauxEnregistrementDgid = null,
        // Timbre fiscal fixe (2 000 FCFA — CGI SN) ; surchargeable si législation change
        public readonly float   $timbreFiscalDgid       = 2000.0,
    ) {}

    /**
     * Calcule le coefficient de prorata (0 < coeff ≤ 1.0).
     *
     * Formule : joursOccupes / joursDansMois
     * Exemple : entrée le 16 avril → 15 / 30 = 0.5
     *
     * Retourne 1.0 si aucune date de prorata n'est définie (mois complet).
     */
    public function coefficientProrata(): float
    {
        if ($this->dateDebutOccupation === null || $this->dateFinPeriode === null) {
            return 1.0;
        }

        $joursDansMois = (int) $this->dateFinPeriode->daysInMonth;
        // +1 car le jour d'entrée est inclus (ex: 16 au 30 = 15 jours)
        $joursOccupes  = (int) $this->dateDebutOccupation->diffInDays($this->dateFinPeriode) + 1;

        return min(1.0, max(0.0, $joursOccupes / $joursDansMois));
    }

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
    public static function fromContrat(
        Contrat  $contrat,
        ?Carbon  $dateDebutOccupation = null,
        ?Carbon  $dateFinPeriode      = null,
        bool     $avecFraisInitiaux   = false,  // true = premier paiement : lit frais_agence + caution
    ): self
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
            dateDebutOccupation:    $dateDebutOccupation,
            dateFinPeriode:         $dateFinPeriode,
            fraisAgenceHt:          $avecFraisInitiaux ? (float) ($contrat->frais_agence ?? 0) : 0.0,
            cautionMontant:         $avecFraisInitiaux ? (float) ($contrat->caution       ?? 0) : 0.0,
            chargesAssujettiesATva: (bool) ($contrat->charges_assujetties_tva ?? false),
            cautionGardeeParAgence: (bool) ($contrat->caution_gardee_par_agence ?? false),

            // ── DGID : actif uniquement au premier paiement non exonéré ──────
            avecDgid:               $avecFraisInitiaux && !($contrat->enregistrement_exonere ?? false),
            enregistrementExonere:  (bool) ($contrat->enregistrement_exonere ?? false),
            // Assiette mensuelle = loyer_nu + charges_mensuelles (loyer_contractuel)
            loyerMensuelDgid:       (float) ($contrat->loyer_contractuel
                                        ?? ((float)($contrat->loyer_nu ?? 0)
                                           + (float)($contrat->charges_mensuelles ?? 0))),
            // Durée : de date_debut à date_fin si connues, sinon 12 mois par défaut
            dureeMoisDgid:          ($contrat->date_debut && $contrat->date_fin)
                                        ? max(1, (int) $contrat->date_debut->diffInMonths($contrat->date_fin))
                                        : 12,
            // Override de taux par le contrat (null = FiscalService détermine via type_bail)
            tauxEnregistrementDgid: $contrat->taux_enregistrement_dgid !== null
                                        ? (float) $contrat->taux_enregistrement_dgid
                                        : null,
            timbreFiscalDgid:       FiscalService::DGID_TIMBRE_FISCAL,
        );
    }
}