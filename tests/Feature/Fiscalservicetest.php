<?php

namespace Tests\Unit\Services;

use App\Services\FiscalContext;
use App\Services\FiscalResult;
use App\Services\FiscalService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * FiscalServiceTest — Tests unitaires du moteur de calcul fiscal sénégalais.
 *
 * Aucune base de données requise : FiscalService est pur (pas d'I/O).
 *
 * Lancer : php artisan test --filter=FiscalServiceTest
 */
class FiscalServiceTest extends TestCase
{
    private FiscalService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FiscalService();
    }

    // ════════════════════════════════════════════════════════════════════════
    // FiscalService::calculer() — moteur principal
    // ════════════════════════════════════════════════════════════════════════

    #[\PHPUnit\Framework\Attributes\Test]
    public function calculer_bail_habitation_non_meuble_sans_tva(): void
    {
        // Habitation non meublée → exonérée TVA loyer (CGI art. 355)
        $ctx = new FiscalContext(
            loyerNu:                200_000,
            chargesAmount:          0,
            tomAmount:              0,
            typeBail:               'habitation',
            estMeuble:              false,
            locataireEstEntreprise: false,
            tauxCommission:         10.0,
            tauxTvaCommission:      18.0,
            tauxTvaLoyerOverride:   null,
            tauxBrsContrat:         null,
            tauxBrsLocataire:       null,
        );

        $result = FiscalService::calculer($ctx);

        $this->assertFalse($result->loyerAssujetti);
        $this->assertSame(0.0,       $result->tvaLoyer);
        $this->assertSame(200_000.0, $result->loyerTtc);   // Pas de TVA loyer
        $this->assertSame(0.0,       $result->brsAmount);  // Pas de BRS (particulier)
        $this->assertSame('habitation', $result->regimeFiscal);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function calculer_bail_commercial_avec_tva_18_pourcent(): void
    {
        // Commercial → assujetti TVA 18% sur le loyer
        $ctx = new FiscalContext(
            loyerNu:                200_000,
            chargesAmount:          0,
            tomAmount:              0,
            typeBail:               'commercial',
            estMeuble:              false,
            locataireEstEntreprise: false,
            tauxCommission:         10.0,
            tauxTvaCommission:      18.0,
            tauxTvaLoyerOverride:   null,
            tauxBrsContrat:         null,
            tauxBrsLocataire:       null,
        );

        $result = FiscalService::calculer($ctx);

        $this->assertTrue($result->loyerAssujetti);
        $this->assertSame(36_000.0, $result->tvaLoyer);    // 18% × 200 000
        $this->assertSame(236_000.0, $result->loyerTtc);   // 200 000 + 36 000
        $this->assertSame('commercial', $result->regimeFiscal);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function calculer_habitation_meublee_est_assujettie_tva(): void
    {
        $ctx = new FiscalContext(
            loyerNu:                150_000,
            chargesAmount:          0,
            tomAmount:              0,
            typeBail:               'habitation',
            estMeuble:              true,   // ← meublé = assujetti
            locataireEstEntreprise: false,
            tauxCommission:         10.0,
            tauxTvaCommission:      18.0,
            tauxTvaLoyerOverride:   null,
            tauxBrsContrat:         null,
            tauxBrsLocataire:       null,
        );

        $result = FiscalService::calculer($ctx);

        $this->assertTrue($result->loyerAssujetti);
        $this->assertSame(27_000.0, $result->tvaLoyer);  // 18% × 150 000
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function calculer_brs_applique_si_locataire_entreprise(): void
    {
        // BRS 15% sur loyer TTC si locataire est une entreprise (CGI art. 196bis)
        $ctx = new FiscalContext(
            loyerNu:                200_000,
            chargesAmount:          0,
            tomAmount:              0,
            typeBail:               'commercial',
            estMeuble:              false,
            locataireEstEntreprise: true,   // ← entreprise
            tauxCommission:         10.0,
            tauxTvaCommission:      18.0,
            tauxTvaLoyerOverride:   null,
            tauxBrsContrat:         null,   // → taux légal 15%
            tauxBrsLocataire:       null,
        );

        $result = FiscalService::calculer($ctx);

        $loyerTtcAttendu = 236_000.0; // 200 000 + 18%
        $brsAttendu      = round($loyerTtcAttendu * 0.15, 2); // 35 400

        $this->assertSame(15.0,      $result->tauxBrsApplique);
        $this->assertSame($brsAttendu, $result->brsAmount);
        $this->assertSame('commercial_avec_brs', $result->regimeFiscal);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function calculer_brs_priorite_contrat_sur_legal(): void
    {
        // Si le contrat spécifie un taux BRS, il prime sur les 15% légaux
        $ctx = new FiscalContext(
            loyerNu:                200_000,
            chargesAmount:          0,
            tomAmount:              0,
            typeBail:               'commercial',
            estMeuble:              false,
            locataireEstEntreprise: true,
            tauxCommission:         10.0,
            tauxTvaCommission:      18.0,
            tauxTvaLoyerOverride:   null,
            tauxBrsContrat:         5.0,    // ← taux négocié au contrat
            tauxBrsLocataire:       null,
        );

        $result = FiscalService::calculer($ctx);

        $this->assertSame(5.0, $result->tauxBrsApplique); // Contrat prime
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function calculer_commission_et_nets_corrects(): void
    {
        $ctx = new FiscalContext(
            loyerNu:                200_000,
            chargesAmount:          20_000,
            tomAmount:              0,
            typeBail:               'habitation',
            estMeuble:              false,
            locataireEstEntreprise: false,
            tauxCommission:         10.0,
            tauxTvaCommission:      18.0,
            tauxTvaLoyerOverride:   null,
            tauxBrsContrat:         null,
            tauxBrsLocataire:       null,
        );

        $result = FiscalService::calculer($ctx);

        // Commission = 10% × loyer_ht uniquement (pas sur charges)
        $this->assertSame(20_000.0, $result->commissionHt);
        $this->assertSame(3_600.0,  $result->tvaCommission); // 18% × 20 000
        $this->assertSame(23_600.0, $result->commissionTtc);

        // Montant encaissé = loyer_ttc + charges (habitation non meublée → tva_loyer=0)
        $this->assertSame(220_000.0, $result->montantEncaisse); // 200 000 + 20 000

        // Net proprio = montant encaissé - commission TTC
        $this->assertSame(196_400.0, $result->netProprietaire); // 220 000 - 23 600
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function calculer_override_tva_loyer_force_exempt(): void
    {
        // Override manuel : loyer commercial forcé exonéré
        $ctx = new FiscalContext(
            loyerNu:                200_000,
            chargesAmount:          0,
            tomAmount:              0,
            typeBail:               'commercial',
            estMeuble:              false,
            locataireEstEntreprise: false,
            tauxCommission:         10.0,
            tauxTvaCommission:      18.0,
            tauxTvaLoyerOverride:   0.0, // ← forcé exonéré
            tauxBrsContrat:         null,
            tauxBrsLocataire:       null,
        );

        $result = FiscalService::calculer($ctx);

        $this->assertFalse($result->loyerAssujetti);
        $this->assertSame(0.0, $result->tvaLoyer);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function calculer_retourne_un_fiscal_result(): void
    {
        $ctx = new FiscalContext(
            loyerNu: 100_000, chargesAmount: 0, tomAmount: 0,
            typeBail: 'habitation', estMeuble: false,
            locataireEstEntreprise: false,
            tauxCommission: 10.0, tauxTvaCommission: 18.0,
            tauxTvaLoyerOverride: null, tauxBrsContrat: null, tauxBrsLocataire: null,
        );

        $result = FiscalService::calculer($ctx);

        $this->assertInstanceOf(FiscalResult::class, $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function to_paiement_fields_contient_toutes_les_cles(): void
    {
        $ctx = new FiscalContext(
            loyerNu: 200_000, chargesAmount: 0, tomAmount: 0,
            typeBail: 'habitation', estMeuble: false,
            locataireEstEntreprise: false,
            tauxCommission: 10.0, tauxTvaCommission: 18.0,
            tauxTvaLoyerOverride: null, tauxBrsContrat: null, tauxBrsLocataire: null,
        );

        $fields = FiscalService::calculer($ctx)->toPaiementFields();

        foreach (['loyer_nu', 'loyer_ht', 'tva_loyer', 'loyer_ttc',
                  'charges_amount', 'tom_amount', 'montant_encaisse',
                  'commission_agence', 'tva_commission', 'commission_ttc',
                  'net_proprietaire', 'brs_amount', 'taux_brs_applique',
                  'net_a_verser_proprietaire', 'regime_fiscal_snapshot'] as $cle) {
            $this->assertArrayHasKey($cle, $fields, "Clé manquante : {$cle}");
        }
    }

    // ════════════════════════════════════════════════════════════════════════
    // FiscalService::loyerEstAssujetti()
    // ════════════════════════════════════════════════════════════════════════

    #[\PHPUnit\Framework\Attributes\Test]
    public function habitation_non_meublee_est_exoneree(): void
    {
        $this->assertFalse(FiscalService::loyerEstAssujetti('habitation', false));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function habitation_meublee_est_assujettie(): void
    {
        $this->assertTrue(FiscalService::loyerEstAssujetti('habitation', true));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function commercial_toujours_assujetti(): void
    {
        $this->assertTrue(FiscalService::loyerEstAssujetti('commercial', false));
        $this->assertTrue(FiscalService::loyerEstAssujetti('commercial', true));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function mixte_toujours_assujetti(): void
    {
        $this->assertTrue(FiscalService::loyerEstAssujetti('mixte', false));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function saisonnier_non_meuble_exonere(): void
    {
        $this->assertFalse(FiscalService::loyerEstAssujetti('saisonnier', false));
    }

    // ════════════════════════════════════════════════════════════════════════
    // FiscalService::calculerIRPP()
    // ════════════════════════════════════════════════════════════════════════

    #[\PHPUnit\Framework\Attributes\Test]
    public function irpp_zero_sous_le_seuil_exonere(): void
    {
        // Tranche 0% : revenus < 1 500 000 FCFA
        $this->assertSame(0.0, FiscalService::calculerIRPP(1_000_000));
        $this->assertSame(0.0, FiscalService::calculerIRPP(0));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function irpp_tranche_20_pourcent(): void
    {
        // Tranche 2 commence à 1 500 001 → (2 000 000 - 1 500 001) × 20% ≈ 99 999.8
        $this->assertEqualsWithDelta(100_000.0, FiscalService::calculerIRPP(2_000_000), 1.0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function irpp_calcul_progressif_multi_tranches(): void
    {
        // Base = 5 000 000
        // Tranche 1 : (1 500 000 - 0) × 0%    =       0
        // Tranche 2 : (4 000 000 - 1 500 001) × 20% = 499 999.8 ≈ 500 000
        // Tranche 3 : (5 000 000 - 4 000 001) × 30% = 299 999.7 ≈ 300 000
        // Total ≈ 800 000
        $irpp = FiscalService::calculerIRPP(5_000_000);
        $this->assertGreaterThan(700_000.0, $irpp);
        $this->assertLessThan(900_000.0,   $irpp);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function irpp_est_croissant_avec_les_revenus(): void
    {
        // assertGreaterThan(expected, actual) : actual > expected
        $this->assertGreaterThan(
            FiscalService::calculerIRPP(5_000_000),
            FiscalService::calculerIRPP(8_000_000)
        );
    }

    // ════════════════════════════════════════════════════════════════════════
    // FiscalService::verifierLoi8118()
    // ════════════════════════════════════════════════════════════════════════

    #[\PHPUnit\Framework\Attributes\Test]
    public function loi8118_loyer_conforme_50m2(): void
    {
        $result = FiscalService::verifierLoi8118(120_000, 50);

        $this->assertTrue($result['conforme']);
        $this->assertSame(150_000, $result['plafond']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function loi8118_loyer_non_conforme_50m2(): void
    {
        $result = FiscalService::verifierLoi8118(200_000, 50);

        $this->assertFalse($result['conforme']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function loi8118_grande_surface_loyer_libre(): void
    {
        $result = FiscalService::verifierLoi8118(1_500_000, 200);

        $this->assertTrue($result['conforme']);
        $this->assertNull($result['plafond']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function loi8118_surface_null_toujours_conforme(): void
    {
        $result = FiscalService::verifierLoi8118(999_999, null);

        $this->assertTrue($result['conforme']); // Pas de surface → vérification impossible
    }

    // ════════════════════════════════════════════════════════════════════════
    // calculerDecompositionLoyer() — méthode d'instance (projections)
    // ════════════════════════════════════════════════════════════════════════

    #[\PHPUnit\Framework\Attributes\Test]
    public function decomposition_loyer_simple_sans_charges(): void
    {
        $result = $this->service->calculerDecompositionLoyer(200_000);

        $this->assertSame(20_000.0,  $result['commission_ht']);  // 10% de 200 000
        $this->assertSame(3_600.0,   $result['tva_montant']);    // 18% de 20 000
        $this->assertSame(23_600.0,  $result['commission_ttc']);
        $this->assertSame(10_000.0,  $result['tom_montant']);    // 5% de 200 000
        $this->assertSame(170_000.0, $result['net_proprietaire']); // 200 000 - 20 000 - 10 000
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function decomposition_commission_calculee_sur_loyer_hc_uniquement(): void
    {
        $result = $this->service->calculerDecompositionLoyer(300_000, 25_000);

        $this->assertSame(325_000.0, $result['loyer_brut']);
        $this->assertSame(30_000.0,  $result['commission_ht']); // 10% de 300 000 seulement
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function decomposition_montant_negatif_leve_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->calculerDecompositionLoyer(-50_000);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function decomposition_taux_invalide_leve_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->calculerDecompositionLoyer(200_000, tauxCommission: 1.5);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function coherence_commission_tom_net_egale_loyer_hc(): void
    {
        // Règle d'or : commission_ht + tom + net_proprio ≈ loyer_hors_charges
        foreach ([100_000, 150_000, 200_000, 350_000, 500_000] as $loyer) {
            $r     = $this->service->calculerDecompositionLoyer($loyer);
            $somme = $r['commission_ht'] + $r['tom_montant'] + $r['net_proprietaire'];

            $this->assertEqualsWithDelta($loyer, $somme, 1.0,
                "Incohérence pour loyer {$loyer} FCFA");
        }
    }

    // ════════════════════════════════════════════════════════════════════════
    // calculerCaution()
    // ════════════════════════════════════════════════════════════════════════

    #[\PHPUnit\Framework\Attributes\Test]
    public function caution_1_mois(): void
    {
        $this->assertSame(200_000.0, $this->service->calculerCaution(200_000, 1));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function caution_3_mois_plafonnee_a_2(): void
    {
        // Loi 81-18 : max 2 mois
        $this->assertSame(400_000.0, $this->service->calculerCaution(200_000, 3));
    }

    // ════════════════════════════════════════════════════════════════════════
    // projeterBilanAnnuel()
    // ════════════════════════════════════════════════════════════════════════

    #[\PHPUnit\Framework\Attributes\Test]
    public function projection_bilan_12_mois(): void
    {
        $result = $this->service->projeterBilanAnnuel(200_000, 0, 12);

        $this->assertSame(100.0, $result['taux_occupation']);
        $this->assertSame(12,    $result['mois_occupes']);
        $this->assertSame(
            $result['mensuel']['commission_ht'] * 12,
            $result['commission_ht_annuel']
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function projection_bilan_mois_occupes_plafonne_a_12(): void
    {
        $result = $this->service->projeterBilanAnnuel(200_000, 0, 15);

        $this->assertSame(12, $result['mois_occupes']);
    }
}
