<?php

namespace Tests\Unit;

use App\Services\FiscalService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CgfCalculTest extends TestCase
{
    #[Test]
    public function test_cgf_tranche_1_sous_2m(): void
    {
        $r = FiscalService::calculerCGF(1_500_000);

        $this->assertTrue($r['applicable']);
        $this->assertEquals(60_000, $r['montant']);      // 1 500 000 × 4%
        $this->assertEquals(4.0, $r['taux_applique']);
        $this->assertStringContainsString('2 000 000', $r['tranche_label']);
    }

    #[Test]
    public function test_cgf_tranche_3_entre_5m_et_10m(): void
    {
        $r = FiscalService::calculerCGF(7_000_000);

        $this->assertTrue($r['applicable']);
        $this->assertEquals(630_000, $r['montant']);     // 7 000 000 × 9%
        $this->assertEquals(9.0, $r['taux_applique']);
        $this->assertStringContainsString('10 000 000', $r['tranche_label']);
    }

    #[Test]
    public function test_cgf_tranche_5_entre_20m_et_30m(): void
    {
        $r = FiscalService::calculerCGF(25_000_000);

        $this->assertTrue($r['applicable']);
        $this->assertEquals(5_000_000, $r['montant']);   // 25 000 000 × 20%
        $this->assertEquals(20.0, $r['taux_applique']);
        $this->assertStringContainsString('30 000 000', $r['tranche_label']);
    }

    #[Test]
    public function test_cgf_non_applicable_au_dessus_30m(): void
    {
        $r = FiscalService::calculerCGF(35_000_000);

        $this->assertFalse($r['applicable']);
        $this->assertEquals(0.0, $r['montant']);
        $this->assertEquals(0.0, $r['taux_applique']);
    }

    #[Test]
    public function test_regime_recommande_cgf(): void
    {
        // CGF = 15 000 000 × 14% = 2 100 000  <  IRPP = 2 500 000
        $r = FiscalService::comparerRegimes(15_000_000, 2_500_000);

        $this->assertEquals('cgf', $r['regime_recommande']);
        $this->assertEquals(2_100_000, $r['cgf_montant']);
        $this->assertEquals(2_500_000, $r['irpp_montant']);
        $this->assertEquals(400_000, $r['economie_potentielle']);
    }

    #[Test]
    public function test_regime_recommande_irpp(): void
    {
        // CGF = 28 000 000 × 20% = 5 600 000  >  IRPP = 4 800 000
        $r = FiscalService::comparerRegimes(28_000_000, 4_800_000);

        $this->assertEquals('irpp', $r['regime_recommande']);
        $this->assertEquals(5_600_000, $r['cgf_montant']);
        $this->assertEquals(4_800_000, $r['irpp_montant']);
        $this->assertEquals(800_000, $r['economie_potentielle']);
    }

    #[Test]
    public function test_cgf_exactement_au_seuil_30m(): void
    {
        // Exactement 30M → encore éligible CGF (seuil exclu = > 30M)
        $r = FiscalService::calculerCGF(30_000_000);

        $this->assertTrue($r['applicable']);
        $this->assertEquals(20.0, $r['taux_applique']);
        $this->assertEquals(6_000_000, $r['montant']);   // 30 000 000 × 20%
    }
}
