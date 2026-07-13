<?php

namespace Tests\Unit;

use App\Services\FiscalService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * CGF — Contribution Globale Foncière (Art. 75 CGI SN).
 *
 * Barème confirmé (brief CGF §3 / regles_fiscales CGF-03) :
 *   ≤ 12 000 000 → 1/12   | 12 000 001–18 000 000 → 1,5/12 | 18 000 001–30 000 000 → 2/12
 * Plancher absolu 30 000 F. Inéligible au-delà de 30 000 000 F.
 */
class CgfCalculTest extends TestCase
{
    #[Test]
    public function test_cgf_tranche_1_un_douzieme(): void
    {
        // Brief cas 1 : 10 000 000 / 12 = 833 333 F
        $r = FiscalService::calculerCGF(10_000_000);

        $this->assertTrue($r['applicable']);
        $this->assertEquals(833_333.0, $r['montant']);
        $this->assertEquals(1.0, $r['fraction']);
        $this->assertFalse($r['plancher_applique']);
    }

    #[Test]
    public function test_cgf_tranche_2_un_et_demi_douzieme(): void
    {
        // Brief cas 2 : 15 000 000 × 1,5 / 12 = 1 875 000 F
        $r = FiscalService::calculerCGF(15_000_000);

        $this->assertTrue($r['applicable']);
        $this->assertEquals(1_875_000.0, $r['montant']);
        $this->assertEquals(1.5, $r['fraction']);
    }

    #[Test]
    public function test_cgf_tranche_3_deux_douziemes(): void
    {
        // Brief cas 3 : 25 000 000 × 2 / 12 = 4 166 667 F (arrondi au F près)
        $r = FiscalService::calculerCGF(25_000_000);

        $this->assertTrue($r['applicable']);
        $this->assertEquals(4_166_667.0, $r['montant']);
        $this->assertEquals(2.0, $r['fraction']);
    }

    #[Test]
    public function test_cgf_plancher_30000(): void
    {
        // Brief cas 4 : 300 000 / 12 = 25 000 → plancher 30 000 F
        $r = FiscalService::calculerCGF(300_000);

        $this->assertTrue($r['applicable']);
        $this->assertEquals(30_000.0, $r['montant']);
        $this->assertTrue($r['plancher_applique']);
        $this->assertEquals(25_000.0, $r['montant_avant_plancher']);
    }

    #[Test]
    public function test_cgf_non_applicable_au_dessus_30m(): void
    {
        // Brief cas 5 : 35 000 000 → inéligible
        $r = FiscalService::calculerCGF(35_000_000);

        $this->assertFalse($r['applicable']);
        $this->assertEquals(0.0, $r['montant']);
    }

    #[Test]
    public function test_cgf_exactement_au_seuil_30m(): void
    {
        // Exactement 30M → encore éligible (seuil exclu = > 30M) — 30M × 2/12
        $r = FiscalService::calculerCGF(30_000_000);

        $this->assertTrue($r['applicable']);
        $this->assertEquals(5_000_000.0, $r['montant']);
        $this->assertEquals(2.0, $r['fraction']);
    }

    #[Test]
    public function test_echeancier_trois_versements_egaux(): void
    {
        // Brief cas 7 : 1 875 000 → 3 × 625 000, fin fév / avr / juin
        $e = FiscalService::calculerEcheancierCgf(1_875_000, 'trois_versements', 2026);

        $this->assertCount(3, $e);
        $this->assertEquals(625_000.0, $e[0]['montant']);
        $this->assertEquals(625_000.0, $e[1]['montant']);
        $this->assertEquals(625_000.0, $e[2]['montant']);
        $this->assertEquals('2026-02-28', $e[0]['date']); // fin février
        $this->assertEquals('2026-04-30', $e[1]['date']); // fin avril
        $this->assertEquals('2026-06-30', $e[2]['date']); // fin juin
    }

    #[Test]
    public function test_echeancier_versement_unique(): void
    {
        $e = FiscalService::calculerEcheancierCgf(833_333, 'unique', 2026);

        $this->assertCount(1, $e);
        $this->assertEquals(833_333.0, $e[0]['montant']);
        $this->assertEquals('2026-02-28', $e[0]['date']);
    }

    #[Test]
    public function test_echeancier_trois_versements_absorbe_arrondi(): void
    {
        // 100 000 / 3 = 33 333,33 → 33 333 + 33 333 + 33 334 = 100 000 exact
        $e = FiscalService::calculerEcheancierCgf(100_000, 'trois_versements', 2026);
        $somme = array_sum(array_column($e, 'montant'));

        $this->assertEquals(100_000.0, $somme);
        $this->assertEquals(33_334.0, $e[2]['montant']); // le reste sur le dernier
    }

    #[Test]
    public function test_regime_recommande_cgf(): void
    {
        // CGF(15M) = 1 875 000 < IRPP 2 500 000 → CGF plus avantageux
        $r = FiscalService::comparerRegimes(15_000_000, 2_500_000);

        $this->assertEquals('cgf', $r['regime_recommande']);
        $this->assertEquals(1_875_000.0, $r['cgf_montant']);
        $this->assertEquals(2_500_000.0, $r['irpp_montant']);
        $this->assertEquals(625_000.0, $r['economie_potentielle']);
    }

    #[Test]
    public function test_regime_recommande_irpp(): void
    {
        // CGF(28M) = 28M × 2/12 = 4 666 667 > IRPP 4 000 000 → IRPP plus avantageux
        $r = FiscalService::comparerRegimes(28_000_000, 4_000_000);

        $this->assertEquals('irpp', $r['regime_recommande']);
        $this->assertEquals(4_666_667.0, $r['cgf_montant']);
        $this->assertEquals(4_000_000.0, $r['irpp_montant']);
        $this->assertEquals(666_667.0, $r['economie_potentielle']);
    }
}
