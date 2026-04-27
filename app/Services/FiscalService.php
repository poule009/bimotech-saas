<?php

namespace App\Services;

use App\Models\Paiement;
use InvalidArgumentException;

/**
 * FiscalService — Moteur de calcul fiscal pour le marché sénégalais.
 *
 * Références légales :
 *  - TVA 18%         : Code Général des Impôts (CGI), article 357
 *  - BRS 15%         : CGI article 196bis — Retenue à la source locataire entreprise
 *  - Commission HT   : Base de facturation de l'agence hors taxes
 *  - Loi 81-18       : Encadrement des loyers au Sénégal
 *  - IRPP            : CGI article 65, barème progressif, abattement 30% (Art. 58)
 *  - CFPB            : CGI articles 95-110 (Contribution Foncière des Propriétés Bâties)
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

    public const TVA_TAUX              = 18.0;   // 18% — CGI art. 357
    public const TVA_TAUX_DECIMAL     = 0.18;
    public const BRS_TAUX_LEGAL       = 15.0;   // 15% — CGI art. 196bis
    public const COMMISSION_TAUX      = 10.0;   // 10% — standard marché SN
    public const ABATTEMENT_IRPP      = 0.30;   // 30% forfaitaire — CGI art. 58
    public const CFPB_TAUX            = 0.05;   // ~5% de la valeur locative brute

    // ── Droits d'enregistrement DGID (CGI SN art. 442) ──────────────────────
    public const DGID_TAUX_HABITATION = 1.0;    // 1% × loyer annuel — bail d'habitation
    public const DGID_TAUX_COMMERCIAL = 2.0;    // 2% × loyer annuel — bail commercial/mixte
    public const DGID_TIMBRE_FISCAL   = 2000.0; // Timbre fiscal fixe (FCFA)

    // Tranches IRPP progressif (CGI art. 65) — montants en FCFA
    public const IRPP_TRANCHES = [
        ['min' => 0,        'max' => 1_500_000,  'taux' => 0],
        ['min' => 1_500_001, 'max' => 4_000_000,  'taux' => 20],
        ['min' => 4_000_001, 'max' => 8_000_000,  'taux' => 30],
        ['min' => 8_000_001, 'max' => PHP_INT_MAX, 'taux' => 40],
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
     * Règles d'assiette :
     *   - TVA loyer   → sur loyer_ht uniquement (jamais charges ni TOM)
     *   - Commission  → % × loyer_ht uniquement
     *   - BRS         → % × loyer_ttc (si locataire entreprise)
     *   - Charges/TOM → hors TVA, hors commission, hors BRS
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
        $assujetti = $ctx->tauxTvaLoyerOverride !== null
            ? ($ctx->tauxTvaLoyerOverride > 0)
            : self::loyerEstAssujetti($ctx->typeBail, $ctx->estMeuble);

        $tauxTvaLoyer = $ctx->tauxTvaLoyerOverride ?? ($assujetti ? self::TVA_TAUX : 0.0);

        $loyerHt  = round($loyerNu, 2);
        // Art. 354 CGI SN : TVA sur loyer + TOM (les charges récupérables restent hors TVA)
        $tvaLoyer = round(($loyerHt + $tom) * ($tauxTvaLoyer / 100), 2);
        $loyerTtc = round($loyerHt + $tvaLoyer, 2);

        // ── 2. Total encaissé ────────────────────────────────────────────────
        // TVA sur charges : obligatoire si facturées en forfait (DGI SN — prestation de service).
        // Même taux que le loyer : si bail exonéré (habitation), tauxTvaLoyer = 0 → tvaCharges = 0 automatiquement.
        $tvaCharges      = $ctx->chargesAssujettiesATva
            ? round($charges * ($tauxTvaLoyer / 100), 2)
            : 0.0;
        $chargesTtc      = round($charges + $tvaCharges, 2);
        $montantEncaisse = round($loyerTtc + $chargesTtc + $tom, 2);

        // ── 3. Commission agence ────────────────────────────────────────────
        $commissionHt  = round($loyerHt * ($ctx->tauxCommission / 100), 2);
        $tvaCommission = round($commissionHt * ($ctx->tauxTvaCommission / 100), 2);
        $commissionTtc = round($commissionHt + $tvaCommission, 2);

        // ── 4. Net propriétaire (avant BRS) ─────────────────────────────────
        $netProprietaire = round($montantEncaisse - $commissionTtc, 2);

        // ── 5. BRS — priorité : contrat > locataire > légal 15% ────────────
        $brsApplicable = $ctx->locataireEstEntreprise;
        $tauxBrs       = 0.0;
        $brsAmount     = 0.0;

        if ($brsApplicable) {
            $tauxBrs   = $ctx->tauxBrsContrat ?? $ctx->tauxBrsLocataire ?? self::BRS_TAUX_LEGAL;
            // Art. 156 CGI SN : BRS sur montant brut TTC = loyer TTC + TOM (hors charges)
            $brsAmount = round(($loyerTtc + $tom) * ($tauxBrs / 100), 2);
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
        $fraisAgenceHt           = round($ctx->fraisAgenceHt, 2);
        $tvaFraisAgence          = round($fraisAgenceHt * (self::TVA_TAUX / 100), 2);
        $fraisAgenceTtc          = round($fraisAgenceHt + $tvaFraisAgence, 2);
        $cautionMontant          = round($ctx->cautionMontant, 2);
        $totalEncaissementInitial = round($montantEncaisse + $fraisAgenceTtc + $cautionMontant, 2);

        // ── 8. Nets consolidés ───────────────────────────────────────────────
        // Net locataire : ce que le locataire verse effectivement après retenue BRS.
        // Le BRS est une retenue à la source → il déduit avant de payer.
        $netLocataire = round($totalEncaissementInitial - $brsAmount, 2);

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
            $dgidResult = self::calculerDroitsBail(
                loyerMensuel:       $ctx->loyerMensuelDgid,
                dureeMois:          $ctx->dureeMoisDgid,
                tauxPct:            $ctx->tauxEnregistrementDgid ?? self::dgidTauxDefaut($ctx->typeBail),
                timbreFiscal:       $ctx->timbreFiscalDgid,
            );
            $dgidDroits = $dgidResult['droits_enregistrement'];
            $dgidTimbre = $dgidResult['timbre_fiscal'];
            $dgidTotal  = $dgidResult['total_dgid'];
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
     * Retourne le taux BRS applicable à un locataire.
     *
     * Priorité : override locataire → taux légal 15% → 0% si particulier.
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
                'paiements.commission_agence',
                'paiements.tva_commission',
                'paiements.commission_ttc',
                'paiements.brs_amount',
                'paiements.net_proprietaire',
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

        $commissionsHt    = (float) $paiements->sum('commission_agence');
        $tvaCommissions   = (float) $paiements->sum('tva_commission');
        $tvaLoyerCollecte = (float) $paiements->sum('tva_loyer');
        $brsRetenuTotal   = (float) $paiements->sum('brs_amount');
        $netProprietaire  = (float) $paiements->sum('net_proprietaire');

        $nbBiensGeres = $paiements->pluck('bien_reference')->unique()->count();

        // ── Calcul IRPP (CGI art. 58 et 65) ────────────────────────────────
        $abattement30      = round($revenusBrutsLoyers * self::ABATTEMENT_IRPP, 2);
        $baseImposable     = round($revenusBrutsLoyers - $abattement30, 2);
        $irppEstime        = self::calculerIRPP($baseImposable);

        // ── CFPB (CGI art. 95-110) ──────────────────────────────────────────
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
            'brs_retenu_total'          => $brsRetenuTotal,

            // Commissions agence
            'commissions_agence_ht'     => $commissionsHt,
            'tva_commissions'           => $tvaCommissions,

            // Net propriétaire
            'net_proprietaire_total'    => $netProprietaire,

            // Méta
            'nb_paiements'              => $paiements->count(),
            'nb_biens_geres'            => $nbBiensGeres,
            'calcule_le'                => now(),

            // Snapshot paiements pour le PDF
            'paiements'                 => $paiements,
        ];
    }

    /**
     * Calcule les droits d'enregistrement DGID d'un bail (CGI SN art. 442).
     *
     * Formule :
     *   Assiette   = loyerMensuel × dureeMois
     *   Droits     = Assiette × tauxPct / 100
     *   Total DGID = Droits + timbreFiscal
     *
     * Appelé par : FiscalService::calculer() (étape 9, premier paiement uniquement)
     *              et directement depuis ContratController pour preview à la création.
     *
     * Scénario test :
     *   loyerMensuel=250 000, dureeMois=12, tauxPct=5%, timbreFiscal=2 000
     *   → assiette=3 000 000, droits=150 000, total=152 000
     *
     * @param  float $loyerMensuel   Loyer nu + charges (assiette mensuelle)
     * @param  int   $dureeMois      Durée du bail en mois (default : 12)
     * @param  float $tauxPct        Taux en % (1.0 hab / 2.0 commercial / override contrat)
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
     * Formule : loyer_mensuel × 12 × taux (1% hab / 2% commercial)
     *
     * @param  float  $loyerMensuel  Loyer nu mensuel (FCFA)
     * @param  string $typeBail      habitation | commercial | mixte | saisonnier
     * @return float                 Droit estimé en FCFA (hors timbre)
     */
    public static function droitDeBailEstime(float $loyerMensuel, string $typeBail): float
    {
        $taux = self::dgidTauxDefaut($typeBail);
        return round($loyerMensuel * 12 * ($taux / 100), 2);
    }

    /**
     * Retourne le taux DGID légal selon le type de bail.
     *
     * CGI SN art. 442 :
     *   Habitation / saisonnier → 1%
     *   Commercial / mixte      → 2%
     */
    private static function dgidTauxDefaut(string $typeBail): float
    {
        return match($typeBail) {
            'commercial', 'mixte' => self::DGID_TAUX_COMMERCIAL,
            default               => self::DGID_TAUX_HABITATION,
        };
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
     * Calcule une décomposition fiscale estimée depuis des montants bruts.
     * Utilisé pour les previews rapides et le DemoDataSeeder.
     * N'accède pas à la base de données.
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
}
