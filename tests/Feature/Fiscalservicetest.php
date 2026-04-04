<?php

namespace Tests\Unit\Services;

use App\Services\FiscalService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * FiscalServiceTest — Tests unitaires du moteur de calcul fiscal.
 *
 * Ces tests ne touchent PAS la base de données (pas de RefreshDatabase).
 * Le FiscalService est pur : entrées scalaires → sorties array.
 * Chaque test vérifie une règle métier précise.
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

    // ─── calculerDecompositionLoyer ─────────────────────────────────────────

    /** @test */
    public function decomposition_loyer_simple_sans_charges(): void
    {
        // Loyer de référence : 200 000 FCFA, agence standard Dakar
        $result = $this->service->calculerDecompositionLoyer(200_000);

        // Commission HT = 10% de 200 000 = 20 000
        $this->assertSame(20_000, $result['commission_ht']);

        // TVA sur commission = 18% de 20 000 = 3 600
        $this->assertSame(3_600, $result['tva_montant']);

        // Commission TTC = 20 000 + 3 600 = 23 600
        $this->assertSame(23_600, $result['commission_ttc']);

        // TOM = 5% de 200 000 = 10 000
        $this->assertSame(10_000, $result['tom_montant']);

        // Net proprio = 200 000 - 20 000 - 10 000 = 170 000
        $this->assertSame(170_000, $result['net_proprietaire']);

        // Locataire paie le loyer brut (sans charges ici)
        $this->assertSame(200_000, $result['total_locataire']);
    }

    /** @test */
    public function decomposition_loyer_avec_charges(): void
    {
        $result = $this->service->calculerDecompositionLoyer(
            loyerHorsCharges: 300_000,
            charges: 25_000
        );

        // Loyer brut = 300 000 + 25 000
        $this->assertSame(325_000, $result['loyer_brut']);

        // Le locataire paie le tout
        $this->assertSame(325_000, $result['total_locataire']);

        // La commission est calculée UNIQUEMENT sur le loyer HT (pas sur les charges)
        $this->assertSame(30_000, $result['commission_ht']); // 10% de 300 000
    }

    /** @test */
    public function decomposition_avec_taux_commission_personnalise(): void
    {
        // Agence négociant 8% au lieu de 10%
        $result = $this->service->calculerDecompositionLoyer(
            loyerHorsCharges: 200_000,
            tauxCommission: 0.08
        );

        $this->assertSame(16_000, $result['commission_ht']); // 8% de 200 000
        $this->assertSame(2_880, $result['tva_montant']);    // 18% de 16 000
    }

    /** @test */
    public function decomposition_avec_taux_tom_hors_dakar(): void
    {
        // Commune avec TOM à 3% au lieu de 5%
        $result = $this->service->calculerDecompositionLoyer(
            loyerHorsCharges: 200_000,
            tauxTom: 0.03
        );

        $this->assertSame(6_000, $result['tom_montant']); // 3% de 200 000
    }

    /** @test */
    public function tva_taux_est_bien_18_pourcent(): void
    {
        $result = $this->service->calculerDecompositionLoyer(100_000);

        $this->assertSame(FiscalService::TVA_TAUX, $result['tva_taux']);
        $this->assertEqualsWithDelta(
            $result['commission_ht'] * 0.18,
            $result['tva_montant'],
            1.0, // Tolérance d'arrondi de 1 FCFA
            'La TVA doit être exactement 18% de la commission HT'
        );
    }

    /** @test */
    public function loyer_zero_retourne_des_zeros(): void
    {
        $result = $this->service->calculerDecompositionLoyer(0);

        $this->assertSame(0, $result['commission_ht']);
        $this->assertSame(0, $result['tva_montant']);
        $this->assertSame(0, $result['tom_montant']);
        $this->assertSame(0, $result['net_proprietaire']);
    }

    /** @test */
    public function montant_negatif_leve_une_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/loyer_hors_charges/');

        $this->service->calculerDecompositionLoyer(-50_000);
    }

    /** @test */
    public function taux_superieur_a_1_leve_une_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/taux_commission/');

        $this->service->calculerDecompositionLoyer(200_000, tauxCommission: 1.5);
    }

    /** @test */
    public function taux_negatif_leve_une_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->calculerDecompositionLoyer(200_000, tauxTom: -0.05);
    }

    // ─── calculerDepotGarantie ──────────────────────────────────────────────

    /** @test */
    public function depot_garantie_standard_2_mois(): void
    {
        $result = $this->service->calculerDepotGarantie(200_000, 2);

        $this->assertSame(400_000, $result['montant']);
        $this->assertTrue($result['conforme_loi8118']);
        $this->assertNull($result['avertissement']);
    }

    /** @test */
    public function depot_garantie_1_mois(): void
    {
        $result = $this->service->calculerDepotGarantie(200_000, 1);

        $this->assertSame(200_000, $result['montant']);
        $this->assertSame(1, $result['mois_appliques']);
    }

    /** @test */
    public function depot_garantie_3_mois_plafonne_a_2(): void
    {
        // La loi 81-18 plafonne à 2 mois même si le proprio demande 3
        $result = $this->service->calculerDepotGarantie(200_000, 3);

        $this->assertSame(400_000, $result['montant']);       // 2 mois seulement
        $this->assertSame(2, $result['mois_appliques']);
        $this->assertFalse($result['conforme_loi8118']);
        $this->assertNotNull($result['avertissement']);
    }

    // ─── verifierConformiteLoi8118 ──────────────────────────────────────────

    /** @test */
    public function loyer_conforme_pour_appartement_50m2(): void
    {
        // Tranche 1 : surface ≤ 60 m², plafond 150 000 FCFA
        $result = $this->service->verifierConformiteLoi8118(
            loyerHorsCharges: 120_000,
            surface: 50,
            type: 'appartement'
        );

        $this->assertTrue($result['conforme']);
        $this->assertTrue($result['soumis_loi8118']);
        $this->assertSame(150_000, $result['loyer_max']);
    }

    /** @test */
    public function loyer_non_conforme_pour_appartement_50m2(): void
    {
        $result = $this->service->verifierConformiteLoi8118(
            loyerHorsCharges: 200_000, // Dépasse le plafond de 150 000
            surface: 50,
            type: 'appartement'
        );

        $this->assertFalse($result['conforme']);
        $this->assertSame(50_000, $result['ecart']); // 200 000 - 150 000
    }

    /** @test */
    public function bureau_non_soumis_a_loi_8118(): void
    {
        $result = $this->service->verifierConformiteLoi8118(
            loyerHorsCharges: 500_000,
            surface: 40,
            type: 'bureau'
        );

        $this->assertFalse($result['soumis_loi8118']);
        $this->assertTrue($result['conforme']); // Toujours conforme car hors périmètre
    }

    /** @test */
    public function villa_grande_surface_loyer_libre(): void
    {
        // Surface > 150 m² → pas de plafond
        $result = $this->service->verifierConformiteLoi8118(
            loyerHorsCharges: 1_500_000,
            surface: 200,
            type: 'villa'
        );

        $this->assertTrue($result['conforme']);
        $this->assertNull($result['loyer_max']);
    }

    // ─── calculerBilanAnnuel ────────────────────────────────────────────────

    /** @test */
    public function bilan_annuel_12_mois_occupation_complete(): void
    {
        $result = $this->service->calculerBilanAnnuel(
            loyerHorsCharges: 200_000,
            charges: 0,
            moisOccupes: 12
        );

        $this->assertSame(100.0, $result['taux_occupation']);
        $this->assertSame(12, $result['mois_occupes']);

        // Le bilan annuel doit être exactement 12 × les montants mensuels
        $mensuel = $result['mensuel'];
        $this->assertSame(
            $mensuel['commission_ht'] * 12,
            $result['commission_ht_annuel']
        );
    }

    /** @test */
    public function bilan_annuel_6_mois_taux_occupation_50_pourcent(): void
    {
        $result = $this->service->calculerBilanAnnuel(
            loyerHorsCharges: 200_000,
            moisOccupes: 6
        );

        $this->assertSame(50.0, $result['taux_occupation']);
        $this->assertSame(6, $result['mois_occupes']);
    }

    /** @test */
    public function bilan_annuel_mois_occupes_plafonne_a_12(): void
    {
        // Valeur invalide : > 12 mois → clampé à 12
        $result = $this->service->calculerBilanAnnuel(
            loyerHorsCharges: 200_000,
            moisOccupes: 15
        );

        $this->assertSame(12, $result['mois_occupes']);
    }

    // ─── formaterFCFA ───────────────────────────────────────────────────────

    /** @test */
    public function formatage_fcfa_avec_separateurs_milliers(): void
    {
        $this->assertSame('200 000 FCFA', $this->service->formaterFCFA(200_000));
        $this->assertSame('1 500 000 FCFA', $this->service->formaterFCFA(1_500_000));
        $this->assertSame('0 FCFA', $this->service->formaterFCFA(0));
    }

    /** @test */
    public function formatage_taux_en_pourcentage(): void
    {
        $this->assertSame('18%', $this->service->formaterTaux(0.18));
        $this->assertSame('5%', $this->service->formaterTaux(0.05));
        $this->assertSame('10%', $this->service->formaterTaux(0.10));
    }

    // ─── Cohérence globale ──────────────────────────────────────────────────

    /** @test */
    public function coherence_la_somme_commission_tom_net_egale_loyer_hc(): void
    {
        // Règle d'or : commission_ht + tom + net_proprio = loyer_hors_charges
        // (à 1 FCFA près à cause des arrondis)
        $loyersTest = [100_000, 150_000, 200_000, 350_000, 500_000];

        foreach ($loyersTest as $loyer) {
            $result = $this->service->calculerDecompositionLoyer($loyer);
            $somme  = $result['commission_ht'] + $result['tom_montant'] + $result['net_proprietaire'];

            $this->assertEqualsWithDelta(
                $loyer,
                $somme,
                1.0,
                "Pour un loyer de {$loyer} : commission_ht + tom + net_proprio doit ≈ loyer_hc"
            );
        }
    }
}