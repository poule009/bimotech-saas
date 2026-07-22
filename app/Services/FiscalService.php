<?php

namespace App\Services;

use App\Models\Paiement;
use InvalidArgumentException;

/**
 * FiscalService — Moteur de calcul fiscal pour le marché sénégalais.
 *
 * Références légales :
 *  - TVA 18%         : Code Général des Impôts (CGI), article 369
 *  - BRS 5%          : CGI article 201 §3 — Retenue à la source sur loyers (texte officiel vérifié)
 *  - Commission HT   : Base de facturation de l'agence hors taxes
 *  - Loi 81-18       : Encadrement des loyers au Sénégal
 *  - IRPP            : CGI article 65, barème progressif, abattement 30% (Art. 68 §c)
 *  - CFPB            : CGI articles 283-294 (Contribution Foncière des Propriétés Bâties)
 *
 * ARCHITECTURE :
 *  Méthodes statiques (API principale) :
 *    - calculer(FiscalContext)              → FiscalResult   [cœur du moteur]
 *    - loyerEstAssujetti(type, meuble)      → bool           [règle TVA]
 *    - calculerBilanAnnuel(id, annee, agId) → array          [agrégation DB]
 *
 *  Méthodes d'instance (utilitaires projections) :
 *    - calculerDecompositionLoyer(...)      → array          [estimation rapide]
 *    - projeterBilanAnnuel(...)             → array          [projection sans DB]
 */
class FiscalService
{
    // ─── Constantes fiscales ────────────────────────────────────────────────

    public const TVA_TAUX              = 18.0;   // 18% — CGI art. 369 (texte officiel lu)
    public const TVA_TAUX_DECIMAL     = 0.18;
    public const BRS_TAUX_LEGAL       = 5.0;    // 5% — CGI art. 201 §3 (texte officiel lu)
    public const BRS_SEUIL_MENSUEL   = 150000.0; // Art. 200 §4 CGI SN — retenue non obligatoire si loyer mensuel < 150 000 F
    public const COMMISSION_TAUX      = 10.0;   // 10% — standard marché SN (taux libre, non encadré)
    public const ABATTEMENT_IRPP      = 0.30;   // 30% forfaitaire — CGI art. 68 §c (texte officiel lu)
    /**
     * ESTIMATION INDICATIVE — L'assiette légale de la CFPB est la valeur locative CADASTRALE
     * fixée par la DGID (Art. 290-291 CGI SN), pas le loyer réel perçu.
     * Ce taux appliqué aux loyers réels est une approximation ; ne pas utiliser pour déclaration officielle.
     * Taux unique 5% retenu (CFPB-01) : le 7,5% industriel n'est pas codé (hors périmètre).
     * Aucun abattement (40% ou résidence principale) appliqué : CFPB-02 non vérifié, CFPB-03 en conflit.
     */
    public const CFPB_TAUX            = 0.05;

    /**
     * Statut PERMANENT de l'estimation CFPB : l'app ne connaît JAMAIS la valeur locative
     * cadastrale réelle (fixée par la DGID). Contrairement aux badges « à confirmer »
     * (Droits d'enregistrement, bornes CGF), celui-ci ne pourra jamais être levé — il est
     * structurel. Voir regles_fiscales CFPB-01.
     */
    public const CFPB_STATUT = 'estimation_structurelle';

    /**
     * TEOM (Taxe d'Enlèvement des Ordures Ménagères) — même assiette que la CFPB
     * (valeur locative), même badge estimation_structurelle. Taux selon la commune
     * du bien : 3,6% à Dakar, 3% ailleurs (TEOM-01). Déclaration = mêmes conditions
     * que la CFPB, 31 janvier (TEOM-03).
     */
    public const TEOM_TAUX_DAKAR = 3.6;
    public const TEOM_TAUX_AUTRE = 3.0;

    // ── Droits d'enregistrement DGID (CGI SN art. 464 B + 472 IV.6) ────────
    public const DGID_TAUX_HABITATION = 2.0;    // 2% — Art. 472 IV.6 : TOUS baux à durée limitée
    public const DGID_TAUX_COMMERCIAL = 2.0;    // 2% — identique (pas de distinction hab/commercial)
    public const DGID_TIMBRE_FISCAL   = 2000.0; // Timbre fiscal fixe (FCFA) — à confirmer DGID

    // Tranches IRPP progressif — CGI SN art. 173 — montants en FCFA
    // Source : CGI Sénégal annoté Janvier 2023 (kof-experts.sn) — texte officiel lu intégralement
    public const IRPP_TRANCHES = [
        ['min' => 0,           'max' => 630_000,     'taux' => 0],
        ['min' => 630_000,     'max' => 1_500_000,   'taux' => 20],
        ['min' => 1_500_000,   'max' => 4_000_000,   'taux' => 30],
        ['min' => 4_000_000,   'max' => 8_000_000,   'taux' => 35],
        ['min' => 8_000_000,   'max' => 13_500_000,  'taux' => 37],
        ['min' => 13_500_000,  'max' => 50_000_000,  'taux' => 40],
        ['min' => 50_000_000,  'max' => PHP_INT_MAX,  'taux' => 43],
    ];

    // Seuil et barème CGF — Contribution Globale Foncière (Art. 75 CGI SN)
    // Régime OPTIONNEL synthétique (remplace IRPP foncier + IMF + CFPB, PAS la TVA),
    // réservé aux personnes physiques avec loyer brut annuel ≤ 30 000 000 F.
    // Réf. traçabilité : regles_fiscales CGF-01 (seuil), CGF-03 (barème + plancher).
    public const CGF_SEUIL = 30_000_000;

    // Plancher absolu : quel que soit le calcul, minimum 30 000 F dus (CGF-03).
    public const CGF_PLANCHER = 30_000;

    // Barème en fraction de mois de loyer (brief §2 R2 / CGF-03).
    // 'fraction' = nombre de mois de loyer → montant = revenu × fraction / 12.
    //   ≤ 12 000 000 F        → 1/12   (1 mois,   ≈ 8,33%)
    //   12 000 001–18 000 000 → 1,5/12 (1,5 mois, ≈ 12,5%)
    //   18 000 001–30 000 000 → 2/12   (2 mois,   ≈ 16,67%)
    // ⚠ Bornes 12M/18M = source privée (confirme_source_privee), pas texte brut Art. 75.
    public const CGF_BAREME = [
        ['max' => 12_000_000, 'fraction' => 1.0,  'label' => '1 mois de loyer (1/12)'],
        ['max' => 18_000_000, 'fraction' => 1.5,  'label' => '1,5 mois de loyer (1,5/12)'],
        ['max' => 30_000_000, 'fraction' => 2.0,  'label' => '2 mois de loyer (2/12)'],
    ];

    // Tranches loi 81-18 (plafonds loyer mensuel en FCFA selon surface m²)
    public const LOI_8118_TRANCHES = [
        ['surface_max' => 60,   'loyer_max' => 150_000],
        ['surface_max' => 100,  'loyer_max' => 300_000],
        ['surface_max' => 150,  'loyer_max' => 500_000],
        ['surface_max' => null, 'loyer_max' => null],   // Au-delà : libre
    ];

    // ═══════════════════════════════════════════════════════════════════════
    // API STATIQUE PRINCIPALE
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Calcule la ventilation fiscale complète d'un paiement.
     *
     * Point d'entrée unique pour tout enregistrement de paiement.
     * Appelé par : PaiementService, PaiementController.
     *
     * Règles d'assiette (traçabilité complète : table regles_fiscales) :
     *   - TVA loyer   → sur (loyer_ht + TOM) — clé 'tva_loyer_assiette' [NON VÉRIFIÉ]
     *   - Commission  → % × loyer_ht uniquement — clé 'tva_commission' [confirmé]
     *   - BRS         → % × loyer_ht brut — Art. 201 §3 ("montant brut hors taxes")
     *   - BRS actif   → si bailleur est personne physique — Art. 201 §2
     *   - Charges     → hors commission, hors BRS ; TVA si forfait — clé 'tva_charges' [NON VÉRIFIÉ]
     *
     * INDÉPENDANCE DU RÉGIME (clé 'tva_independante_du_regime_cgf', confirmé) :
     *   La TVA s'applique quel que soit le régime du propriétaire. La CGF remplace
     *   uniquement l'IRPP et la CFPB — jamais la TVA. Ne JAMAIS désactiver le calcul
     *   TVA ci-dessous au motif qu'un propriétaire est en CGF.
     */
    public static function calculer(FiscalContext $ctx): FiscalResult
    {
        // ── 0. Prorata temporel ──────────────────────────────────────────────
        // Coefficient = 1.0 pour un mois complet, < 1.0 pour une entrée en cours de mois.
        // Loyer, charges et TOM sont tous proratisés sur la même base (jours réels / jours mois).
        $coeff    = $ctx->coefficientProrata();
        $loyerNu  = round($ctx->loyerNu       * $coeff, 2);
        $charges  = round($ctx->chargesAmount * $coeff, 2);
        $tom      = round($ctx->tomAmount     * $coeff, 2);

        // ── 1. TVA loyer ────────────────────────────────────────────────────
        // Assujettissement (quel taux s'applique) : CONFIRMÉ — voir regles_fiscales
        // clé 'tva_loyer_assujettissement' (Immoplus Sablux). Seul 18% est codé
        // (clé 'tva_taux_standard' — Eurocham) ; le taux réduit 10% n'est jamais utilisé.
        $operationTaxable = $ctx->tauxTvaLoyerOverride !== null
            ? ($ctx->tauxTvaLoyerOverride > 0)
            : self::loyerEstAssujetti($ctx->typeBail, $ctx->estMeuble);

        // F2 : un bailleur NON assujetti à la TVA ne facture pas de TVA sur le loyer,
        // même si l'opération est taxable par nature (meublé, commercial, mixte).
        $assujetti    = $operationTaxable && $ctx->proprietaireAssujettiTva;
        $tauxTvaLoyer = $assujetti
            ? ($ctx->tauxTvaLoyerOverride ?? self::TVA_TAUX)
            : 0.0;

        $loyerHt  = round($loyerNu, 2);
        // RÈGLE NON VÉRIFIÉE PAR SOURCE OFFICIELLE INDÉPENDANTE
        // Origine : document interne uniquement (référentiel fiscal Bimotech, 06/05/2026)
        // À confirmer avec un fiscaliste avant audit ou contrôle fiscal réel
        // Statut : PLAUSIBLE mais NON CONFIRMÉ
        // Traçabilité : regles_fiscales, clé 'tva_loyer_assiette'
        // Assiette = loyer_HT + TOM (charges récupérables et commission agence exclues)
        $tvaLoyer = round(($loyerHt + $tom) * ($tauxTvaLoyer / 100), 2);
        $loyerTtc = round($loyerHt + $tvaLoyer, 2);

        // ── 2. Total encaissé ────────────────────────────────────────────────
        // RÈGLE NON VÉRIFIÉE PAR SOURCE OFFICIELLE INDÉPENDANTE
        // Origine : document interne uniquement (référentiel fiscal Bimotech, 06/05/2026)
        // À confirmer avec un fiscaliste avant audit ou contrôle fiscal réel
        // Statut : PLAUSIBLE mais NON CONFIRMÉ
        // Traçabilité : regles_fiscales, clé 'tva_charges'
        // Débours purs (refacturés à l'identique, factures au nom du locataire) → 0%.
        // Forfait mensuel fixe (chargesAssujettiesATva = true) → 18%.
        // F1 : taux FIXE 18% (et non le taux du loyer) → un forfait est taxé à 18%
        //      même si le loyer est exonéré, conformément au libellé et au brief.
        // F2 : gate sur l'assujettissement du bailleur (comme le loyer).
        $tvaCharges      = ($ctx->chargesAssujettiesATva && $ctx->proprietaireAssujettiTva)
            ? round($charges * (self::TVA_TAUX / 100), 2)
            : 0.0;
        $chargesTtc      = round($charges + $tvaCharges, 2);
        $montantEncaisse = round($loyerTtc + $chargesTtc + $tom, 2);

        // ── 3. Commission agence ────────────────────────────────────────────
        // F2 : une agence NON assujettie à la TVA ne facture pas de TVA sur sa commission.
        $tauxTvaCommissionEff = $ctx->agenceAssujettieTva ? $ctx->tauxTvaCommission : 0.0;
        $commissionHt  = round($loyerHt * ($ctx->tauxCommission / 100), 2);
        $tvaCommission = round($commissionHt * ($tauxTvaCommissionEff / 100), 2);
        $commissionTtc = round($commissionHt + $tvaCommission, 2);

        // ── 4. Net propriétaire (avant BRS) ─────────────────────────────────
        $netProprietaire = round($montantEncaisse - $commissionTtc, 2);

        // ── 5. BRS — priorité : contrat > locataire > légal 5% ─────────────
        // Art. 201 §2 CGI SN : la retenue est faite par l'agence pour le compte du bailleur
        // personne physique. Elle ne s'applique PAS si le bailleur est une personne morale IS.
        $brsApplicable = config('features.fiscalite') && $ctx->brsApplicable;
        $tauxBrs       = 0.0;
        $brsAmount     = 0.0;

        if ($brsApplicable) {
            $hasOverride = ($ctx->tauxBrsContrat !== null || $ctx->tauxBrsLocataire !== null);
            // Art. 200 §4 CGI SN : retenue non obligatoire si loyer mensuel < 150 000 F (sauf override manuel)
            if (!$hasOverride && $ctx->loyerNu < self::BRS_SEUIL_MENSUEL) {
                $brsApplicable = false;
            } else {
                $tauxBrs   = $ctx->tauxBrsContrat ?? $ctx->tauxBrsLocataire ?? self::BRS_TAUX_LEGAL;
                // Art. 201 §3 CGI SN : "5% du montant brut hors taxes des loyers encaissés"
                $brsAmount = round($loyerHt * ($tauxBrs / 100), 2);
            }
        }

        $netAVerser = round($netProprietaire - $brsAmount, 2);

        // ── 6. Label régime fiscal (lisible pour UI/PDF) ────────────────────
        $regime = match(true) {
            $assujetti && $brsApplicable => 'commercial_avec_brs',
            $assujetti                   => 'commercial',
            $brsApplicable               => 'habitation_avec_brs',
            default                      => 'habitation',
        };

        // ── 7. Frais de dossier agence (premier paiement uniquement) ────────
        // fraisAgenceHt = 0 pour tous les paiements récurrents → calculs neutres
        // F2 : TVA sur honoraires uniquement si l'agence est assujettie.
        $fraisAgenceHt           = round($ctx->fraisAgenceHt, 2);
        $tvaFraisAgence          = round($fraisAgenceHt * (($ctx->agenceAssujettieTva ? self::TVA_TAUX : 0.0) / 100), 2);
        $fraisAgenceTtc          = round($fraisAgenceHt + $tvaFraisAgence, 2);
        $cautionMontant          = round($ctx->cautionMontant, 2);
        $totalEncaissementInitial = round($montantEncaisse + $fraisAgenceTtc + $cautionMontant, 2);

        // ── 8. Nets consolidés ───────────────────────────────────────────────
        // Net locataire : ce que le locataire verse effectivement à l'agence.
        // Dans le contexte agence, le locataire paie le montant total.
        // Le BRS est retenu par l'agence et déduit du reversement au bailleur (netAVerser) — pas du paiement locataire.
        $netLocataire = round($totalEncaissementInitial, 2);

        // Net bailleur : dépend de qui détient la caution.
        //  - false (défaut) : agence remet la caution au bailleur → incluse
        //  - true           : agence garde la caution en séquestre → exclue
        $netBailleur = $ctx->cautionGardeeParAgence
            ? round($netAVerser, 2)
            : round($netAVerser + $cautionMontant, 2);

        // ── 9. Droits d'enregistrement DGID ─────────────────────────────────
        // Calculés UNIQUEMENT au premier paiement (avecDgid = true).
        // Obligation fiscale séparée — ne modifient PAS montant_encaisse ni netLocataire.
        // Sur tous les paiements récurrents : avecDgid = false → tous à 0.0.
        $dgidDroits = 0.0;
        $dgidTimbre = 0.0;
        $dgidTotal  = 0.0;

        if ($ctx->avecDgid && !$ctx->enregistrementExonere) {
            // SOURCE UNIQUE : si le contrat porte déjà les droits calculés par
            // ContratObserver (tracker droit_enreg_*), on les reprend TELS QUELS
            // → le snapshot du 1er paiement = exactement le montant de la fiche
            // (même assiette loyer+charges, même timbre × feuilles, même base).
            if ($ctx->dgidDroitsPrecalcule !== null) {
                $dgidDroits = round($ctx->dgidDroitsPrecalcule, 2);
                $dgidTimbre = round($ctx->dgidTimbrePrecalcule ?? 0.0, 2);
                $dgidTotal  = round($dgidDroits + $dgidTimbre, 2);
            } else {
                // Fallback (contrats antérieurs au tracker) : calcul historique.
                $dgidResult = self::calculerDroitsBail(
                    loyerMensuel:       $ctx->loyerMensuelDgid,
                    dureeMois:          $ctx->dureeMoisDgid,
                    tauxPct:            $ctx->tauxEnregistrementDgid ?? self::dgidTauxDefaut(),
                    timbreFiscal:       $ctx->timbreFiscalDgid,
                );
                $dgidDroits = $dgidResult['droits_enregistrement'];
                $dgidTimbre = $dgidResult['timbre_fiscal'];
                $dgidTotal  = $dgidResult['total_dgid'];
            }
        }

        return new FiscalResult(
            loyerHt:                  $loyerHt,
            tvaLoyer:                 $tvaLoyer,
            loyerTtc:                 $loyerTtc,
            chargesAmount:            $charges,
            tvaCharges:               $tvaCharges,
            chargesTtc:               $chargesTtc,
            tomAmount:                $tom,
            montantEncaisse:          $montantEncaisse,
            commissionHt:             $commissionHt,
            tvaCommission:            $tvaCommission,
            commissionTtc:            $commissionTtc,
            netProprietaire:          $netProprietaire,
            tauxBrsApplique:          $tauxBrs,
            brsAmount:                $brsAmount,
            brsApplicable:            $brsApplicable,
            netAVerserProprietaire:   $netAVerser,
            loyerAssujetti:           $assujetti,
            regimeFiscal:             $regime,
            tauxTvaLoyerApplique:     $tauxTvaLoyer,
            tauxCommission:           $ctx->tauxCommission,
            fraisAgenceHt:            $fraisAgenceHt,
            tvaFraisAgence:           $tvaFraisAgence,
            fraisAgenceTtc:           $fraisAgenceTtc,
            cautionMontant:           $cautionMontant,
            totalEncaissementInitial: $totalEncaissementInitial,
            netLocataire:                $netLocataire,
            netBailleur:                 $netBailleur,
            dgidDroitsEnregistrement:    $dgidDroits,
            dgidTimbreFiscal:            $dgidTimbre,
            dgidTotal:                   $dgidTotal,
        );
    }

    /**
     * Retourne le taux BRS applicable.
     *
     * Priorité : override → taux légal 5% (Art. 201 §3) → 0% si non applicable.
     * Utilisé par LocataireObserver pour propager les changements de profil fiscal.
     */
    public static function tauxBrs(bool $estEntreprise, ?float $overrideLocataire = null): float
    {
        if (! $estEntreprise) {
            return 0.0;
        }
        return $overrideLocataire ?? self::BRS_TAUX_LEGAL;
    }

    /**
     * Détermine si le loyer est assujetti à la TVA.
     *
     * Règle CGI SN art. 355 :
     *  - Habitation non meublée  → exonérée
     *  - Habitation meublée      → assujettie (équivaut à prestation de service)
     *  - Commercial / mixte      → toujours assujetti
     *  - Saisonnier meublé       → assujetti
     *
     * Appelé par : FiscalService::calculer()
     */
    public static function loyerEstAssujetti(string $typeBail, bool $estMeuble = false): bool
    {
        return match($typeBail) {
            'commercial'  => true,
            'mixte'       => true,
            'habitation'  => $estMeuble,
            'saisonnier'  => $estMeuble,
            default       => false,
        };
    }

    /**
     * Calcule le bilan annuel réel depuis la base de données.
     *
     * Agrège les paiements validés d'une année pour un propriétaire.
     * Calcule IRPP (barème progressif) et CFPB depuis les vrais montants.
     *
     * Appelé par : BilanFiscalController::calculate()
     *
     * @param  int $proprietaireId  ID de l'utilisateur (rôle proprietaire)
     * @param  int $annee           Année fiscale (ex: 2025)
     * @param  int $agencyId        Isolation multi-tenant
     * @return array                Données prêtes pour BilanFiscalProprietaire::updateOrCreate()
     */
    public static function calculerBilanAnnuel(int $proprietaireId, int $annee, int $agencyId): array
    {
        // ── Agrégation des paiements validés depuis la DB ───────────────────
        $paiements = Paiement::withoutGlobalScopes()
            ->join('contrats', 'paiements.contrat_id', '=', 'contrats.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->where('paiements.agency_id', $agencyId)
            ->where('paiements.statut', 'valide')
            ->whereYear('paiements.date_paiement', $annee)
            ->where('biens.proprietaire_id', $proprietaireId)
            ->select([
                'paiements.id',
                'paiements.periode',
                'paiements.loyer_ht',
                'paiements.loyer_nu',
                'paiements.tva_loyer',
                'paiements.charges_amount',
                'paiements.tva_charges',
                'paiements.commission_agence',
                'paiements.tva_commission',
                'paiements.commission_ttc',
                'paiements.brs_amount',
                'paiements.net_proprietaire',
                'paiements.net_a_verser_proprietaire',
                'paiements.tom_amount',
                'paiements.date_paiement',
                'biens.reference as bien_reference',
                'biens.meuble as bien_meuble',
                'contrats.type_bail',
            ])
            ->orderBy('paiements.periode')
            ->get();

        // ── Agrégats ────────────────────────────────────────────────────────
        $revenusBrutsLoyers  = (float) $paiements->sum(fn($p) => $p->loyer_ht ?? $p->loyer_nu ?? 0);
        $revenusBrutsCharges = (float) $paiements->sum('charges_amount');
        $revenusBrutsTotal   = $revenusBrutsLoyers + $revenusBrutsCharges;

        $commissionsHt      = (float) $paiements->sum('commission_agence');
        $tvaCommissions     = (float) $paiements->sum('tva_commission');
        $tvaLoyerCollecte   = (float) $paiements->sum('tva_loyer');
        $tvaChargesCollecte = (float) $paiements->sum('tva_charges');   // C1/R5 — TVA charges forfait
        $brsRetenuTotal     = (float) $paiements->sum('brs_amount');
        $netProprietaire    = (float) $paiements->sum('net_proprietaire');
        $netAVerserTotal    = (float) $paiements->sum('net_a_verser_proprietaire'); // C3 — après BRS
        $tomTotal           = (float) $paiements->sum('tom_amount');                // C5 — TOM annuel

        $nbBiensGeres = $paiements->pluck('bien_reference')->unique()->count();

        // ── Calcul IRPP (CGI art. 68 et 173) ───────────────────────────────
        // Assiette IRPP foncier = LOYERS bruts seuls (charges EXCLUES), abattement 30%.
        // Aligné sur le brief IRPP §3 + regles_fiscales IR-01 et sur
        // FiscalService::estimerIrppFoncier (encart fiche propriétaire) → un seul
        // montant IRPP, quel que soit l'écran. (revenus_bruts_total, charges incluses,
        // reste utilisé pour la CGF et l'affichage des recettes.)
        $abattement30      = round($revenusBrutsLoyers * self::ABATTEMENT_IRPP, 2);
        $baseImposable     = round($revenusBrutsLoyers - $abattement30, 2);
        $irppEstime        = self::calculerIRPP($baseImposable);
        $irppDetail        = self::calculerIRPPDetail($baseImposable);

        // ── CFPB estimée (CGI art. 283-294) ─────────────────────────────────
        // Art. 290-291 : assiette légale = valeur locative CADASTRALE (méthode cadastrale)
        // Faute de données cadastrales, on approxime sur les loyers réels — indicatif uniquement.
        $cfpbEstimee = round($revenusBrutsLoyers * self::CFPB_TAUX, 2);

        return [
            // Revenus
            'revenus_bruts_loyers'      => $revenusBrutsLoyers,
            'revenus_bruts_charges'     => $revenusBrutsCharges,
            'revenus_bruts_total'       => $revenusBrutsTotal,

            // Calcul fiscal
            'abattement_forfaitaire_30' => $abattement30,
            'base_imposable'            => $baseImposable,
            'irpp_estime'               => $irppEstime,
            'cfpb_estimee'              => $cfpbEstimee,

            // Taxes collectées
            'tva_loyer_collectee'       => $tvaLoyerCollecte,
            'tva_charges_total'         => $tvaChargesCollecte,  // C1/R5
            'brs_retenu_total'          => $brsRetenuTotal,
            'tom_total'                 => $tomTotal,            // C5

            // Commissions agence
            'commissions_agence_ht'     => $commissionsHt,
            'tva_commissions'           => $tvaCommissions,

            // Net propriétaire
            'net_proprietaire_total'    => $netProprietaire,
            'net_a_verser_total'        => $netAVerserTotal,     // C3 — après BRS

            // Détail IRPP par tranche (C5 — évite recalcul en Blade)
            'irpp_detail'               => $irppDetail,

            // Méta
            'nb_paiements'              => $paiements->count(),
            'nb_biens_geres'            => $nbBiensGeres,
            'calcule_le'                => now(),

            // Snapshot paiements pour le PDF
            'paiements'                 => $paiements,
        ];
    }

    /**
     * Calcule les droits d'enregistrement DGID d'un bail (CGI SN art. 464 B + 472 IV.6).
     *
     * Formule :
     *   Assiette   = loyerMensuel × dureeMois (loyer nu + charges du preneur — Art. 468 §5)
     *   Droits     = Assiette × tauxPct / 100
     *   Total DGID = Droits + timbreFiscal
     *
     * Appelé par : FiscalService::calculer() (étape 9, premier paiement uniquement)
     *              et directement depuis ContratController pour preview à la création.
     *
     * Scénario test :
     *   loyerMensuel=250 000, dureeMois=12, tauxPct=2%, timbreFiscal=2 000
     *   → assiette=3 000 000, droits=60 000, total=62 000
     *
     * @param  float $loyerMensuel   Loyer nu + charges (assiette mensuelle — Art. 468 §5)
     * @param  int   $dureeMois      Durée du bail en mois
     * @param  float $tauxPct        Taux en % (2.0 pour tous les baux — Art. 472 IV.6)
     * @param  float $timbreFiscal   Timbre fixe en FCFA (default : DGID_TIMBRE_FISCAL = 2 000)
     * @return array{base_annuelle: float, taux_enregistrement: float, droits_enregistrement: float, timbre_fiscal: float, total_dgid: float}
     */
    public static function calculerDroitsBail(
        float $loyerMensuel,
        int   $dureeMois    = 12,
        float $tauxPct      = self::DGID_TAUX_HABITATION,
        float $timbreFiscal = self::DGID_TIMBRE_FISCAL,
    ): array {
        if ($loyerMensuel < 0 || $dureeMois <= 0 || $tauxPct < 0) {
            throw new \InvalidArgumentException(
                "calculerDroitsBail : paramètres invalides (loyerMensuel={$loyerMensuel}, dureeMois={$dureeMois}, tauxPct={$tauxPct})"
            );
        }

        $baseAnnuelle = round($loyerMensuel * $dureeMois, 2);
        $droits       = round($baseAnnuelle * ($tauxPct / 100), 2);
        $total        = round($droits + $timbreFiscal, 2);

        return [
            'base_annuelle'          => $baseAnnuelle,
            'taux_enregistrement'    => $tauxPct,
            'droits_enregistrement'  => $droits,
            'timbre_fiscal'          => $timbreFiscal,
            'total_dgid'             => $total,
        ];
    }

    /**
     * Estimation rapide du droit de bail DGID (sans timbre fiscal).
     *
     * Utilisée dans les alertes et aperçus (alerte-dgid.blade.php, etc.)
     * pour donner un ordre de grandeur sans charger la DB.
     *
     * Formule : loyer_mensuel × 12 × 2% (Art. 472 IV.6 — taux unique tous baux)
     *
     * @param  float  $loyerMensuel  Loyer nu mensuel (FCFA)
     * @param  string $typeBail      habitation | commercial | mixte | saisonnier
     * @return float                 Droit estimé en FCFA (hors timbre)
     */
    public static function droitDeBailEstime(float $loyerMensuel, string $typeBail): float
    {
        $taux = self::dgidTauxDefaut();
        return round($loyerMensuel * 12 * ($taux / 100), 2);
    }

    /**
     * Retourne le taux DGID légal selon le type de bail.
     *
     * CGI SN art. 472 IV.6 : 2% pour TOUS les baux à durée limitée.
     * Pas de distinction habitation / commercial dans le texte officiel.
     */
    private static function dgidTauxDefaut(): float
    {
        return self::DGID_TAUX_HABITATION; // 2% — Art. 472 IV.6 (tous types confondus)
    }

    /**
     * Calcule le détail IRPP tranche par tranche (stocké en JSON dans le bilan).
     * Permet d'afficher le barème sans recalculer côté Blade.
     *
     * @return array  [{min, max, taux, assiette_tranche, impot_tranche}, ...]
     */
    public static function calculerIRPPDetail(float $baseImposable): array
    {
        $detail = [];
        foreach (self::IRPP_TRANCHES as $tranche) {
            if ($baseImposable <= $tranche['min']) {
                $detail[] = ['min' => $tranche['min'], 'max' => $tranche['max'], 'taux' => $tranche['taux'], 'assiette' => 0.0, 'impot' => 0.0];
                continue;
            }
            $assiette = min($baseImposable, (float) $tranche['max']) - $tranche['min'];
            $impot    = round($assiette * ($tranche['taux'] / 100), 2);
            $detail[] = ['min' => $tranche['min'], 'max' => $tranche['max'], 'taux' => $tranche['taux'], 'assiette' => round($assiette, 2), 'impot' => $impot];
        }
        return $detail;
    }

    /**
     * Calcule l'IRPP selon le barème progressif sénégalais (CGI art. 65).
     *
     * @param  float $baseImposable  Revenus nets après abattement 30%
     * @return float                 IRPP total estimé en FCFA
     */
    public static function calculerIRPP(float $baseImposable): float
    {
        $irpp = 0.0;

        foreach (self::IRPP_TRANCHES as $tranche) {
            if ($baseImposable <= $tranche['min']) {
                break;
            }
            $imposable = min($baseImposable, (float) $tranche['max']) - $tranche['min'];
            $irpp     += $imposable * ($tranche['taux'] / 100);
        }

        return round($irpp, 2);
    }

    /**
     * Estimation de l'IRPP FONCIER d'un propriétaire PERSONNE PHYSIQUE, année N.
     *
     * PÉRIMÈTRE PARTIEL par construction (statut 'perimetre_partiel') : ne porte
     * QUE sur les loyers gérés dans l'app (mêmes paiements validés que la
     * Comptabilité / le bilan). L'IRPP réel dépend de TOUS les revenus du
     * contribuable et de sa situation familiale. Ce n'est PAS un doute sur les
     * taux (R1 abattement 30% + R2 barème 7 tranches sont raisonnablement fiables,
     * regles_fiscales IR-01/IR-02) : c'est le périmètre des DONNÉES qui est partiel.
     *
     * Assiette = loyers HT encaissés (charges EXCLUES — §3 du brief IRPP) ;
     * base = loyers × (1 − 30%) ; barème progressif par tranche marginale.
     * NB : à réserver aux propriétaires Particuliers (est_personne_morale_is=false) —
     * les personnes morales relèvent de l'IS, pas de l'IRPP.
     *
     * @return array{annee:int, revenu_brut_annuel:float, base_apres_abattement:float,
     *               montant_estime:float, detail:array, statut_calcul:string}
     */
    public static function estimerIrppFoncier(int $proprietaireId, int $annee, int $agencyId): array
    {
        // Loyers HT encaissés (paiements validés) — MÊME source que le bilan/Compta.
        // Charges exclues (§3) : on ne somme que loyer_ht (fallback loyer_nu).
        $rows = Paiement::withoutGlobalScopes()
            ->join('contrats', 'paiements.contrat_id', '=', 'contrats.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->where('paiements.agency_id', $agencyId)
            ->where('paiements.statut', 'valide')
            ->whereYear('paiements.date_paiement', $annee)
            ->where('biens.proprietaire_id', $proprietaireId)
            ->select('paiements.loyer_ht', 'paiements.loyer_nu')
            ->get();

        $revenuBrut = (float) $rows->sum(fn ($p) => (float) ($p->loyer_ht ?? $p->loyer_nu ?? 0));
        $base       = round($revenuBrut * (1 - self::ABATTEMENT_IRPP), 2);

        return [
            'annee'                 => $annee,
            'revenu_brut_annuel'    => round($revenuBrut, 2),
            'base_apres_abattement' => $base,
            'montant_estime'        => self::calculerIRPP($base),
            'detail'                => self::calculerIRPPDetail($base),
            'statut_calcul'         => 'perimetre_partiel',
        ];
    }

    /**
     * Calcule la CGF (Contribution Globale Foncière) — Art. 75 CGI SN.
     *
     * Barème en fraction de mois de loyer (brief §3 / regles_fiscales CGF-03) :
     *   ≤ 12 000 000 F        → revenu × 1/12
     *   12 000 001–18 000 000 → revenu × 1,5/12
     *   18 000 001–30 000 000 → revenu × 2/12
     * Plancher absolu 30 000 F. Non éligible si revenu > 30 000 000 F.
     * Assiette = loyer brut annuel (prévisionnel pour l'option, sans abattement).
     * Montant arrondi au franc près.
     *
     * `taux_applique` = taux ÉQUIVALENT (fraction/12 × 100) — conservé pour
     * l'affichage du bilan et la comparaison de régimes ; le calcul réel passe
     * par la fraction, pas par ce taux.
     *
     * @return array{applicable: bool, montant: float, montant_avant_plancher: float,
     *   fraction: float, taux_applique: float, plancher_applique: bool,
     *   tranche_label: string, fraction_label: string}
     */
    public static function calculerCGF(float $revenusbrutsAnnuels): array
    {
        if ($revenusbrutsAnnuels > self::CGF_SEUIL) {
            return [
                'applicable'             => false,
                'montant'                => 0.0,
                'montant_avant_plancher' => 0.0,
                'fraction'               => 0.0,
                'taux_applique'          => 0.0,
                'plancher_applique'      => false,
                'tranche_label'          => 'Hors CGF (loyer brut > 30 000 000 F)',
                'fraction_label'         => '—',
            ];
        }

        $revenu   = max(0.0, $revenusbrutsAnnuels);
        $prevMax  = 0;
        foreach (self::CGF_BAREME as $tranche) {
            if ($revenu <= $tranche['max']) {
                $montantBrut = round($revenu * $tranche['fraction'] / 12, 0);
                $montant     = max($montantBrut, (float) self::CGF_PLANCHER);

                return [
                    'applicable'             => true,
                    'montant'                => $montant,
                    'montant_avant_plancher' => $montantBrut,
                    'fraction'               => $tranche['fraction'],
                    'taux_applique'          => round($tranche['fraction'] / 12 * 100, 2),
                    'plancher_applique'      => $montantBrut < self::CGF_PLANCHER,
                    'tranche_label'          => number_format($prevMax === 0 ? 0 : $prevMax + 1, 0, ',', ' ')
                        . ' — ' . number_format($tranche['max'], 0, ',', ' ') . ' F',
                    'fraction_label'         => $tranche['label'],
                ];
            }
            $prevMax = $tranche['max'];
        }

        // Inatteignable (revenu ≤ SEUIL == dernier max), gardé par sécurité.
        return [
            'applicable'             => true,
            'montant'                => (float) self::CGF_PLANCHER,
            'montant_avant_plancher' => 0.0,
            'fraction'               => self::CGF_BAREME[0]['fraction'],
            'taux_applique'          => round(self::CGF_BAREME[0]['fraction'] / 12 * 100, 2),
            'plancher_applique'      => true,
            'tranche_label'          => '0 — 12 000 000 F',
            'fraction_label'         => self::CGF_BAREME[0]['label'],
        ];
    }

    /**
     * Construit l'échéancier de paiement de la CGF (regles_fiscales CGF-05).
     *
     * - mode 'unique'           : 1 versement (montant total) fin février.
     * - mode 'trois_versements' : 3 versements ÉGAUX fin février / fin avril / fin juin.
     *   Le reste éventuel de la division est reporté sur le dernier versement pour
     *   que la somme des échéances égale exactement le montant.
     *
     * @return list<array{rang:int, libelle:string, date:string, montant:float}>
     */
    public static function calculerEcheancierCgf(float $montant, string $mode, int $annee): array
    {
        // Fin février de l'année concernée (gère les années bissextiles).
        $finFevrier = date('Y-m-d', strtotime("last day of February {$annee}"));

        if ($mode === 'trois_versements') {
            $part    = round($montant / 3, 0);
            $dernier = round($montant - 2 * $part, 0); // absorbe l'arrondi

            return [
                ['rang' => 1, 'libelle' => 'Fin février', 'date' => $finFevrier,         'montant' => $part],
                ['rang' => 2, 'libelle' => 'Fin avril',   'date' => "{$annee}-04-30",    'montant' => $part],
                ['rang' => 3, 'libelle' => 'Fin juin',    'date' => "{$annee}-06-30",    'montant' => $dernier],
            ];
        }

        // Mode 'unique' par défaut.
        return [
            ['rang' => 1, 'libelle' => 'Versement unique — fin février', 'date' => $finFevrier, 'montant' => round($montant, 0)],
        ];
    }

    /**
     * Estime la CFPB d'un Bien (Art. 283-294 CGI SN) — ESTIMATION STRUCTURELLE.
     *
     * L'assiette légale est la valeur locative CADASTRALE fixée par la DGID, que
     * l'app ne connaît PAS. On l'approxime par le loyer annuel de référence :
     *   valeur_locative_estimee = loyer_mensuel × 12
     *   cfpb_estimee            = valeur_locative_estimee × 5%
     * Aucun abattement (CFPB-02 non vérifié / CFPB-03 en conflit), taux unique 5%
     * (CFPB-01, pas de 7,5% industriel). Le montant réel peut différer sensiblement.
     *
     * @return array{valeur_locative_estimee:int, montant_estime:int, statut_calcul:string}
     */
    public static function estimerCfpbBien(float $loyerMensuel): array
    {
        $valeurLocative = (int) round(max(0.0, $loyerMensuel) * 12, 0);
        $montant        = (int) round($valeurLocative * self::CFPB_TAUX, 0);

        return [
            'valeur_locative_estimee' => $valeurLocative,
            'montant_estime'          => $montant,
            'statut_calcul'           => self::CFPB_STATUT,
        ];
    }

    /**
     * Le bien est-il situé à Dakar ? (détermine le taux TEOM — TEOM-01).
     * Dérivé de la ville du bien : v1 simple « Dakar / autre commune » plutôt
     * qu'une liste exhaustive des communes.
     */
    public static function bienEstADakar(?string $ville): bool
    {
        return $ville !== null && str_contains(mb_strtolower(trim($ville)), 'dakar');
    }

    /**
     * Estime la TEOM d'un Bien (Taxe d'Enlèvement des Ordures Ménagères).
     *
     * Même assiette que la CFPB (valeur locative estimée — RÉUTILISÉE, pas
     * recalculée) et même badge estimation_structurelle. Taux selon la commune :
     * 3,6% à Dakar, 3% ailleurs (TEOM-01/02).
     *
     * @param  int  $valeurLocativeEstimee  Reprend cfpb_valeur_locative_estimee du bien.
     * @return array{taux_applique:float, montant_estime:int, statut_calcul:string}
     */
    public static function estimerTeomBien(int $valeurLocativeEstimee, bool $estDakar): array
    {
        $taux    = $estDakar ? self::TEOM_TAUX_DAKAR : self::TEOM_TAUX_AUTRE;
        $montant = (int) round(max(0, $valeurLocativeEstimee) * $taux / 100, 0);

        return [
            'taux_applique'  => $taux,
            'montant_estime' => $montant,
            'statut_calcul'  => self::CFPB_STATUT,
        ];
    }

    /**
     * Compare le régime CGF et le régime IRPP pour un propriétaire.
     *
     * Retourne le régime le plus avantageux et l'économie potentielle.
     *
     * @return array{
     *   regime_recommande: 'cgf'|'irpp'|'hors_cgf',
     *   cgf_montant: float,
     *   irpp_montant: float,
     *   economie_potentielle: float,
     *   message: string
     * }
     */
    public static function comparerRegimes(float $revenusbruts, float $irppEstime): array
    {
        $cgf = self::calculerCGF($revenusbruts);

        if (! $cgf['applicable']) {
            return [
                'regime_recommande'    => 'hors_cgf',
                'cgf_montant'          => 0.0,
                'irpp_montant'         => $irppEstime,
                'economie_potentielle' => 0.0,
                'message'              => 'Revenus supérieurs à 30 000 000 FCFA : régime réel IRPP obligatoire (Art. 77 CGI SN).',
            ];
        }

        $economie = round(abs($cgf['montant'] - $irppEstime), 2);

        if ($cgf['montant'] <= $irppEstime) {
            return [
                'regime_recommande'    => 'cgf',
                'cgf_montant'          => $cgf['montant'],
                'irpp_montant'         => $irppEstime,
                'economie_potentielle' => $economie,
                'message'              => 'Le régime CGF est plus avantageux : économie de '
                    . number_format($economie, 0, ',', ' ') . ' FCFA vs l\'IRPP.',
            ];
        }

        return [
            'regime_recommande'    => 'irpp',
            'cgf_montant'          => $cgf['montant'],
            'irpp_montant'         => $irppEstime,
            'economie_potentielle' => $economie,
            'message'              => 'Le régime réel IRPP est plus avantageux : économie de '
                . number_format($economie, 0, ',', ' ') . ' FCFA vs la CGF.',
        ];
    }

    /**
     * Vérifie si un loyer respecte les plafonds de la loi 81-18.
     *
     * @param  float    $loyerMensuel  Loyer nu mensuel en FCFA
     * @param  int|null $surfaceM2     Surface habitable en m²
     * @return array{conforme: bool, plafond: int|null, message: string}
     */
    public static function verifierLoi8118(float $loyerMensuel, ?int $surfaceM2 = null): array
    {
        if ($surfaceM2 === null) {
            return ['conforme' => true, 'plafond' => null, 'message' => 'Surface non renseignée — vérification impossible'];
        }

        foreach (self::LOI_8118_TRANCHES as $tranche) {
            if ($tranche['surface_max'] === null || $surfaceM2 <= $tranche['surface_max']) {
                if ($tranche['loyer_max'] === null) {
                    return ['conforme' => true, 'plafond' => null, 'message' => 'Loyer libre (surface > 150 m²)'];
                }
                $conforme = $loyerMensuel <= $tranche['loyer_max'];
                return [
                    'conforme' => $conforme,
                    'plafond'  => $tranche['loyer_max'],
                    'message'  => $conforme
                        ? "Conforme loi 81-18 (plafond {$tranche['loyer_max']} F)"
                        : "Dépasse le plafond loi 81-18 ({$tranche['loyer_max']} F pour {$surfaceM2} m²)",
                ];
            }
        }

        return ['conforme' => true, 'plafond' => null, 'message' => 'Hors tranches connues'];
    }

    // ═══════════════════════════════════════════════════════════════════════
    // MÉTHODES D'INSTANCE — Projections et utilitaires (PAS de DB)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Calcule une décomposition fiscale ESTIMÉE depuis des montants bruts.
     * Utilisé pour les previews rapides et le DemoDataSeeder. N'accède pas à la DB.
     *
     * ⚠️ ESTIMATION UNIQUEMENT (audit TVA — F5) : ne calcule que la TVA sur commission.
     * Ne calcule NI la TVA sur loyer (assujettissement meublé/commercial), NI la TVA
     * charges, NI le BRS, NI l'assiette loyer+TOM. Pour tout calcul fiscal réel
     * (quittances, paiements, déclarations), utiliser calculer(FiscalContext) qui est
     * la source unique. Ne pas se fier à cette méthode pour un document officiel.
     *
     * @param  float $loyerHorsCharges   Loyer de base HT (FCFA)
     * @param  float $charges            Charges mensuelles (FCFA)
     * @param  float $tauxTom            Taux TOM local (défaut 5%, format décimal 0-1)
     * @param  float $tauxCommission     Taux commission (défaut 10%, format décimal 0-1)
     */
    public function calculerDecompositionLoyer(
        float $loyerHorsCharges,
        float $charges = 0,
        float $tauxTom = 0.05,
        float $tauxCommission = 0.10
    ): array {
        $this->validerMontant($loyerHorsCharges, 'loyer_hors_charges');
        $this->validerMontant($charges, 'charges');
        $this->validerTauxDecimal($tauxTom, 'taux_tom');
        $this->validerTauxDecimal($tauxCommission, 'taux_commission');

        $loyerBrut       = $loyerHorsCharges + $charges;
        $commissionHt    = round($loyerHorsCharges * $tauxCommission);
        $tva             = round($commissionHt * self::TVA_TAUX_DECIMAL);
        $commissionTtc   = $commissionHt + $tva;
        $tom             = round($loyerHorsCharges * $tauxTom);
        $netProprietaire = $loyerHorsCharges - $commissionHt - $tom;

        return [
            'loyer_hors_charges'  => round($loyerHorsCharges),
            'charges'             => round($charges),
            'loyer_brut'          => round($loyerBrut),
            'commission_taux'     => $tauxCommission,
            'commission_ht'       => $commissionHt,
            'tva_taux'            => self::TVA_TAUX_DECIMAL,
            'tva_montant'         => $tva,
            'commission_ttc'      => $commissionTtc,
            'tom_taux'            => $tauxTom,
            'tom_montant'         => $tom,
            'net_proprietaire'    => $netProprietaire,
            'total_locataire'     => round($loyerBrut),
            'ratio_commission'    => $loyerHorsCharges > 0
                ? round(($commissionTtc / $loyerHorsCharges) * 100, 2)
                : 0,
        ];
    }

    /**
     * Projette un bilan annuel estimé SANS base de données.
     *
     * @param  float $loyerHorsCharges
     * @param  float $charges
     * @param  int   $moisOccupes       Mois effectivement occupés (0-12)
     */
    public function projeterBilanAnnuel(
        float $loyerHorsCharges,
        float $charges = 0,
        int   $moisOccupes = 12,
        float $tauxTom = 0.05,
        float $tauxCommission = 0.10
    ): array {
        $mensuel        = $this->calculerDecompositionLoyer($loyerHorsCharges, $charges, $tauxTom, $tauxCommission);
        $multiplicateur = max(0, min(12, $moisOccupes));

        return [
            'mois_occupes'            => $multiplicateur,
            'taux_occupation'         => round(($multiplicateur / 12) * 100, 1),
            'loyer_brut_annuel'       => $mensuel['loyer_brut'] * $multiplicateur,
            'commission_ht_annuel'    => $mensuel['commission_ht'] * $multiplicateur,
            'tva_annuel'              => $mensuel['tva_montant'] * $multiplicateur,
            'commission_ttc_annuel'   => $mensuel['commission_ttc'] * $multiplicateur,
            'tom_annuel'              => $mensuel['tom_montant'] * $multiplicateur,
            'net_proprietaire_annuel' => $mensuel['net_proprietaire'] * $multiplicateur,
            'mensuel'                 => $mensuel,
        ];
    }

    /**
     * Calcule la caution selon la loi 81-18.
     * Plafond légal : 2 mois de loyer hors charges.
     */
    public function calculerCaution(float $loyerHorsCharges, int $moisCaution = 1): float
    {
        $this->validerMontant($loyerHorsCharges, 'loyer_hors_charges');
        return round($loyerHorsCharges * min($moisCaution, 2));
    }

    /**
     * Formate un montant en FCFA avec séparateurs de milliers.
     */
    public function formaterFCFA(float $montant): string
    {
        return number_format(round($montant), 0, ',', ' ') . ' FCFA';
    }

    /**
     * Formate un taux en pourcentage.
     */
    public function formaterTaux(float $taux): string
    {
        return number_format($taux * 100, 0) . '%';
    }

    // ─── Validations internes ────────────────────────────────────────────────

    private function validerMontant(float $valeur, string $champ): void
    {
        if ($valeur < 0) {
            throw new InvalidArgumentException(
                "Le champ '{$champ}' ne peut pas être négatif. Valeur reçue : {$valeur}"
            );
        }
    }

    private function validerTauxDecimal(float $taux, string $champ): void
    {
        if ($taux < 0 || $taux > 1) {
            throw new InvalidArgumentException(
                "Le taux '{$champ}' doit être entre 0 et 1 (ex: 0.10 pour 10%). Valeur reçue : {$taux}"
            );
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // MONTANT EN LETTRES (français — FCFA)
    // ═══════════════════════════════════════════════════════════════════════

    public static function montantEnLettresFr(float $amount): string
    {
        $n = (int) round(abs($amount));
        if ($n === 0) return 'Zéro franc CFA';
        return ucfirst(self::_nombreFr($n)) . ' francs CFA';
    }

    private static function _nombreFr(int $n): string
    {
        if ($n === 0) return '';

        $units = [
            '', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf',
            'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize',
            'dix-sept', 'dix-huit', 'dix-neuf',
        ];

        if ($n < 20) return $units[$n];

        if ($n < 100) {
            $t = intdiv($n, 10);
            $u = $n % 10;
            // 70-79 : soixante-dix... | 90-99 : quatre-vingt-dix...
            // 71 = « soixante-et-onze » (liaison « et ») ; 91 = « quatre-vingt-onze » (sans « et »).
            if ($t === 7 || $t === 9) {
                $liaison = ($t === 7 && $u === 1) ? '-et-' : '-';
                return ($t === 7 ? 'soixante' : 'quatre-vingt') . $liaison . $units[10 + $u];
            }
            // 80-89 : quatre-vingts, quatre-vingt-un...
            if ($t === 8) {
                return $u === 0 ? 'quatre-vingts' : 'quatre-vingt-' . $units[$u];
            }
            $tens = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante'];
            if ($u === 0) return $tens[$t];
            return $tens[$t] . ($u === 1 ? '-et-' : '-') . $units[$u];
        }

        if ($n < 1_000) {
            $h = intdiv($n, 100);
            $r = $n % 100;
            $s = ($h === 1 ? 'cent' : $units[$h] . ' cent') . ($r === 0 && $h > 1 ? 's' : '');
            return $s . ($r > 0 ? ' ' . self::_nombreFr($r) : '');
        }

        if ($n < 1_000_000) {
            $m = intdiv($n, 1_000);
            $r = $n % 1_000;
            $s = ($m === 1 ? 'mille' : self::_nombreFr($m) . ' mille');
            return $s . ($r > 0 ? ' ' . self::_nombreFr($r) : '');
        }

        if ($n < 1_000_000_000) {
            $m = intdiv($n, 1_000_000);
            $r = $n % 1_000_000;
            $s = self::_nombreFr($m) . ' million' . ($m > 1 ? 's' : '');
            return $s . ($r > 0 ? ' ' . self::_nombreFr($r) : '');
        }

        $b = intdiv($n, 1_000_000_000);
        $r = $n % 1_000_000_000;
        $s = self::_nombreFr($b) . ' milliard' . ($b > 1 ? 's' : '');
        return $s . ($r > 0 ? ' ' . self::_nombreFr($r) : '');
    }
}
