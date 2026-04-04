<?php

namespace App\Services;

use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log; 

/**
 * FiscalService — Moteur de calcul fiscal unique pour BimoTech.
 *
 * ⚠️  RÈGLE CARDINALE ⚠️
 * Ce fichier est la SEULE source de vérité pour tous les calculs fiscaux.
 * Ne jamais implémenter de calcul fiscal (TVA, BRS, commission, abattement)
 * ailleurs dans l'application. Toujours appeler FiscalService::calculer().
 *
 * TEXTES DE RÉFÉRENCE :
 *  - Art. 196bis CGI SN  → Retenue à la Source (BRS)
 *  - Art. 355-359 CGI SN → TVA sur loyers
 *  - Art. 357 CGI SN     → TVA sur prestations de services (commission)
 *  - Art. 58-62 CGI SN   → Revenus fonciers, abattement 30%
 *  - Art. 65 CGI SN      → Barème IRPP progressif
 *  - Art. 95-110 CGI SN  → Contribution Foncière des Propriétés Bâties
 *  - Art. 442 CGI SN     → Droit d'enregistrement des baux
 */
final class FiscalService
{
    // ─────────────────────────────────────────────────────────────────────────
    // MÉTHODE PRINCIPALE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Calcule la ventilation fiscale complète d'un paiement.
     *
     * RÈGLES D'ASSIETTE (immuables) :
     *   1. TVA loyer → sur loyer_ht UNIQUEMENT. Jamais sur charges ni TOM.
     *   2. Commission → sur loyer_ht UNIQUEMENT. Jamais sur charges, TOM, ni TVA loyer.
     *   3. BRS → sur loyer_ttc (loyer_ht + tva_loyer). Jamais sur charges ni TOM.
     *
     * Ces règles sont conformes aux Art. 355-359 et 196bis CGI Sénégal.
     */
    public static function calculer(FiscalContext $ctx): FiscalResult
    {
        // ── 1. Déterminer le régime fiscal ──────────────────────────────────
        $loyerAssujetti   = self::loyerEstAssujetti($ctx->typeBail, $ctx->estMeuble);
        $tauxTvaLoyer     = $ctx->tauxTvaLoyerOverride
                              ?? ($loyerAssujetti ? 18.0 : 0.0);
        $regimeFiscal     = self::labelRegime($ctx->typeBail, $ctx->estMeuble);

        // ── 2. Calcul TVA loyer ─────────────────────────────────────────────
        // ASSIETTE : loyer_ht uniquement. Charges et TOM = hors champ TVA.
        $loyerHt  = $ctx->loyerNu; // alias sémantique
        $tvaLoyer = round($loyerHt * $tauxTvaLoyer / 100, 2);
        $loyerTtc = round($loyerHt + $tvaLoyer, 2);

        // ── 3. Total encaissé ───────────────────────────────────────────────
        // Charges et TOM s'ajoutent au loyer TTC, jamais taxées.
        $montantEncaisse = round($loyerTtc + $ctx->chargesAmount + $ctx->tomAmount, 2);

        // ── 4. Commission agence ────────────────────────────────────────────
        // ASSIETTE : loyer_ht uniquement. Jamais sur TVA loyer ni charges ni TOM.
        // Conforme aux usages APIMM et conventions agences sénégalaises.
        $commissionHt  = round($loyerHt * $ctx->tauxCommission / 100, 2);
        $tvaCommission = round($commissionHt * $ctx->tauxTvaCommission / 100, 2);
        $commissionTtc = round($commissionHt + $tvaCommission, 2);

        // ── 5. Net propriétaire ──────────────────────────────────────────────
        $netProprietaire = round($montantEncaisse - $commissionTtc, 2);

        // ── 6. BRS — Retenue à la Source (Art. 196bis CGI SN) ───────────────
        // ASSIETTE : loyer_ttc (loyer HT + TVA loyer si applicable).
        // PAS sur les charges ni la TOM (qui ne sont pas des "revenus fonciers").
        // CASCADE TAUX : contrat > locataire > légal (15% si entreprise, 0% sinon)
        $tauxBrs  = self::tauxBrs(
            $ctx->locataireEstEntreprise,
            $ctx->tauxBrsLocataire,
            $ctx->tauxBrsContrat
        );
        $brsAmount       = round($loyerTtc * $tauxBrs / 100, 2);
        $brsApplicable   = $tauxBrs > 0;

        // ── 7. Net à verser au propriétaire ─────────────────────────────────
        $netAVerser = round($netProprietaire - $brsAmount, 2);

        return new FiscalResult(
            loyerHt:                  $loyerHt,
            tvaLoyer:                 $tvaLoyer,
            loyerTtc:                 $loyerTtc,
            chargesAmount:            $ctx->chargesAmount,
            tomAmount:                $ctx->tomAmount,
            montantEncaisse:          $montantEncaisse,
            commissionHt:             $commissionHt,
            tvaCommission:            $tvaCommission,
            commissionTtc:            $commissionTtc,
            netProprietaire:          $netProprietaire,
            tauxBrsApplique:          $tauxBrs,
            brsAmount:                $brsAmount,
            brsApplicable:            $brsApplicable,
            netAVerserProprietaire:   $netAVerser,
            loyerAssujetti:           $loyerAssujetti,
            regimeFiscal:             $regimeFiscal,
            tauxTvaLoyerApplique:     $tauxTvaLoyer,
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RÈGLES MÉTIER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Détermine si le loyer est soumis à TVA 18% selon les règles sénégalaises.
     *
     * Règle (Art. 355 CGI SN) :
     *  - Habitation nue (non meublée) → EXONÉRÉ
     *  - Habitation meublée           → ASSUJETTI (activité commerciale para-hôtelière)
     *  - Commercial, mixte            → ASSUJETTI
     *  - Saisonnier                   → ASSUJETTI
     */
    public static function loyerEstAssujetti(string $typeBail, bool $estMeuble): bool
    {
        return match($typeBail) {
            'habitation' => $estMeuble,   // meublé → assujetti, nu → exonéré
            'commercial' => true,
            'mixte'      => true,
            'saisonnier' => true,
            default      => false,
        };
    }

    /**
     * Détermine le taux BRS applicable selon la cascade de priorité.
     *
     * CASCADE (priorité décroissante) :
     *  1. taux_brs_contrat   → override spécifique à ce contrat (ex: convention fiscale)
     *  2. taux_brs_locataire → override sur tous les contrats de ce locataire
     *  3. Règle légale       → 15% si entreprise (Art. 196bis), 0% si particulier
     */
    public static function tauxBrs(
        bool   $estEntreprise,
        ?float $overrideLocataire = null,
        ?float $overrideContrat   = null,
    ): float {
        // Priorité 1 : override contrat
        if ($overrideContrat !== null) {
            return max(0.0, $overrideContrat);
        }

        // Priorité 2 : override locataire
        if ($overrideLocataire !== null) {
            return max(0.0, $overrideLocataire);
        }

        // Priorité 3 : règle légale
        return $estEntreprise ? 15.0 : 0.0;
    }

    /**
     * Retourne un label lisible du régime fiscal pour l'affichage UI et la quittance.
     */
    public static function labelRegime(string $typeBail, bool $estMeuble): string
    {
        return match(true) {
            $typeBail === 'commercial'                  => 'Bail commercial (TVA 18% loyer)',
            $typeBail === 'mixte'                       => 'Bail mixte (TVA 18% loyer)',
            $typeBail === 'saisonnier'                  => 'Bail saisonnier (TVA 18% loyer)',
            $typeBail === 'habitation' && $estMeuble    => 'Habitation meublée (TVA 18% loyer)',
            default                                     => 'Habitation nue (exonéré TVA loyer)',
        };
    }

    /**
     * Calcule le droit de bail estimé pour l'enregistrement DGID.
     *
     * Tarifs (Art. 442 CGI SN) :
     *  - Habitation : 1% × loyer annuel
     *  - Commercial/autres : 2% × loyer annuel
     */
    public static function droitDeBailEstime(float $loyerNu, string $typeBail): float
    {
        $loyerAnnuel = $loyerNu * 12;
        $taux        = ($typeBail === 'habitation') ? 0.01 : 0.02;
        return round($loyerAnnuel * $taux, 0);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BILAN FISCAL ANNUEL
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Calcule le bilan fiscal annuel d'un propriétaire.
     *
     * Agrège tous les paiements de l'année pour ce propriétaire
     * et applique l'abattement 30% + barème IRPP.
     *
     * @return array Données du bilan prêtes à être persistées
     */
    public static function calculerBilanAnnuel(
        int $proprietaireId,
        int $annee,
        int $agencyId,
    ): array {
        // Agrégats SQL directs pour performance
        $agreg = DB::table('paiements')
            ->join('contrats', 'contrats.id', '=', 'paiements.contrat_id')
            ->join('biens', 'biens.id', '=', 'contrats.bien_id')
            ->where('biens.proprietaire_id', $proprietaireId)
            ->where('paiements.agency_id', $agencyId)
            ->where('paiements.statut', 'valide')
            ->whereYear('paiements.date_paiement', $annee)
            ->selectRaw('
                COALESCE(SUM(paiements.loyer_ht),  0)                   AS revenus_loyers,
                COALESCE(SUM(paiements.charges_amount), 0)              AS revenus_charges,
                COALESCE(SUM(paiements.tva_loyer), 0)                   AS tva_loyer_collectee,
                COALESCE(SUM(paiements.brs_amount), 0)                  AS brs_retenu_total,
                COALESCE(SUM(paiements.commission_agence), 0)           AS commissions_ht,
                COALESCE(SUM(paiements.tva_commission), 0)              AS tva_commissions,
                COALESCE(SUM(paiements.net_proprietaire), 0)            AS net_proprio_total,
                COUNT(paiements.id)                                     AS nb_paiements,
                COUNT(DISTINCT biens.id)                                AS nb_biens_geres
            ')
            ->first();

        $revenusLoyers = (float) ($agreg->revenus_loyers ?? 0);
        $abattement    = round($revenusLoyers * 0.30, 2);
        $baseImposable = round($revenusLoyers * 0.70, 2);
        $irpp          = self::calculerIrpp($baseImposable);
        $cfpb          = self::calculerCFPB($revenusLoyers);

        return [
            'agency_id'                 => $agencyId,
            'proprietaire_id'           => $proprietaireId,
            'annee'                     => $annee,
            'revenus_bruts_loyers'      => $revenusLoyers,
            'revenus_bruts_charges'     => (float) ($agreg->revenus_charges ?? 0),
            'revenus_bruts_total'       => $revenusLoyers + (float) ($agreg->revenus_charges ?? 0),
            'abattement_forfaitaire_30' => $abattement,
            'base_imposable'            => $baseImposable,
            'irpp_estime'               => $irpp,
            'cfpb_estimee'              => $cfpb,
            'tva_loyer_collectee'       => (float) ($agreg->tva_loyer_collectee ?? 0),
            'brs_retenu_total'          => (float) ($agreg->brs_retenu_total ?? 0),
            'commissions_agence_ht'     => (float) ($agreg->commissions_ht ?? 0),
            'tva_commissions'           => (float) ($agreg->tva_commissions ?? 0),
            'net_proprietaire_total'    => (float) ($agreg->net_proprio_total ?? 0),
            'nb_paiements'              => (int)   ($agreg->nb_paiements ?? 0),
            'nb_biens_geres'            => (int)   ($agreg->nb_biens_geres ?? 0),
            'calcule_le'                => now(),
        ];
    }

    /**
     * Barème IRPP progressif sénégalais sur revenus fonciers.
     *
     * ⚠️  IMPORTANT : Vérifier ce barème annuellement avec la Loi de Finances
     * de l'année en cours. Les tranches peuvent être révisées par la DGI.
     *
     * Barème 2024/2025 (Art. 65 CGI SN — revenus annuels) :
     *  - 0 → 1 500 000 FCFA          : 0%
     *  - 1 500 001 → 4 000 000 FCFA  : 20%
     *  - 4 000 001 → 8 000 000 FCFA  : 30%
     *  - > 8 000 000 FCFA            : 40%
     */
    public static function calculerIrpp(float $baseImposable): float
    {
        $impot = 0.0;

        if ($baseImposable <= 1_500_000) {
            return 0.0;
        }

        // Tranche 20% : 1 500 001 → 4 000 000
        if ($baseImposable > 1_500_000) {
            $tranche = min($baseImposable, 4_000_000) - 1_500_000;
            $impot  += $tranche * 0.20;
        }

        // Tranche 30% : 4 000 001 → 8 000 000
        if ($baseImposable > 4_000_000) {
            $tranche = min($baseImposable, 8_000_000) - 4_000_000;
            $impot  += $tranche * 0.30;
        }

        // Tranche 40% : > 8 000 000
        if ($baseImposable > 8_000_000) {
            $tranche = $baseImposable - 8_000_000;
            $impot  += $tranche * 0.40;
        }

        return round($impot, 0);
    }

    /**
     * Contribution Foncière des Propriétés Bâties (CFPB) estimée.
     *
     * ⚠️  Estimation simplifiée. La CFPB réelle est calculée par la DGI
     * sur la valeur locative cadastrale, qui peut différer du loyer effectif.
     *
     * Approximation : 5% de la valeur locative annuelle brute.
     * (Art. 95-110 CGI SN)
     */
    public static function calculerCFPB(float $loyerAnnuelBrut): float
    {
        return round($loyerAnnuelBrut * 0.05, 0);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RÉTRO-COMPATIBILITÉ
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * @deprecated Utiliser FiscalService::calculer(FiscalContext $ctx) à la place.
     *
     * Méthode conservée pour compatibilité avec le Seeder et les tests existants.
     * Les nouveaux appels DOIVENT utiliser calculer() via FiscalContext.
     *
     * @see Paiement::calculerMontants() qui délègue ici
     */
    public static function calculerLegacy(
        float $loyerNu,
        float $tauxCommission,
        float $chargesAmount = 0.0,
        float $tomAmount     = 0.0,
        float $tauxTva       = 18.0,
    ): array {
        $ctx = FiscalContext::fromRequest(
            loyerNu:               $loyerNu,
            chargesAmount:         $chargesAmount,
            tomAmount:             $tomAmount,
            typeBail:              'habitation',  // legacy = habitation par défaut
            estMeuble:             false,
            tauxCommission:        $tauxCommission,
        );

        $result = self::calculer($ctx);

        // Retourne le même format array qu'avant pour ne rien casser
        return [
            'loyer_nu'         => $result->loyerHt,
            'charges_amount'   => $result->chargesAmount,
            'tom_amount'       => $result->tomAmount,
            'montant_encaisse' => $result->montantEncaisse,
            'commission_ht'    => $result->commissionHt,
            'tva'              => $result->tvaCommission,
            'commission_ttc'   => $result->commissionTtc,
            'net_proprietaire' => $result->netProprietaire,
        ];
    }
}