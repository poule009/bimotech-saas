<?php

namespace Tests\Unit;

use App\Services\FiscalService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * BilanFiscalCalculTest — Vérifie les calculs fiscaux annuels (sans DB).
 *
 * Références légales testées :
 *  - Abattement 30% (CGI art. 58)
 *  - Barème IRPP progressif (CGI art. 65)
 *  - CFPB ~5% (CGI art. 95-110)
 */
class BilanFiscalCalculTest extends TestCase
{
    // ════════════════════════════════════════════════════════════════════════
    // IRPP — Barème progressif (CGI art. 65)
    // Tranches :
    //   0 – 1 500 000 F   → 0%
    //   1 500 001 – 4 000 000 F → 20%
    //   4 000 001 – 8 000 000 F → 30%
    //   > 8 000 000 F          → 40%
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function irpp_est_zero_sous_le_seuil_exonere(): void
    {
        // Moins de 1 500 000 F → tranche 0%
        $irpp = FiscalService::calculerIRPP(1_000_000);

        $this->assertEquals(0.0, $irpp);
    }

    #[Test]
    public function irpp_tranche_20_pourcent(): void
    {
        // Base = 2 000 000 F
        // Excédent sur 1 500 000 F × 20% ≈ 100 000 F
        // Tolérance 1 F (précision des bornes IRPP_TRANCHES)
        $irpp = FiscalService::calculerIRPP(2_000_000);

        $this->assertEqualsWithDelta(100_000.0, $irpp, 1.0);
    }

    #[Test]
    public function irpp_tranche_30_pourcent(): void
    {
        // Base = 5 000 000 F
        // Tranche 20% sur [1,5M → 4M] + tranche 30% sur [4M → 5M] ≈ 800 000 F
        $irpp = FiscalService::calculerIRPP(5_000_000);

        $this->assertEqualsWithDelta(800_000.0, $irpp, 1.0);
    }

    #[Test]
    public function irpp_tranche_40_pourcent(): void
    {
        // Base = 10 000 000 F
        // Tranches 20% + 30% + 40% ≈ 2 500 000 F
        $irpp = FiscalService::calculerIRPP(10_000_000);

        $this->assertEqualsWithDelta(2_500_000.0, $irpp, 1.0);
    }

    #[Test]
    public function irpp_base_zero_est_zero(): void
    {
        $this->assertEquals(0.0, FiscalService::calculerIRPP(0));
    }

    // ════════════════════════════════════════════════════════════════════════
    // Abattement 30% + base imposable (CGI art. 58)
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function abattement_30_est_applique_sur_revenus_loyers(): void
    {
        // Revenus bruts loyers = 4 000 000 F
        // Abattement 30% = 1 200 000 F
        // Base imposable = 2 800 000 F
        $revenus    = 4_000_000.0;
        $abattement = round($revenus * FiscalService::ABATTEMENT_IRPP, 2);
        $base       = round($revenus - $abattement, 2);

        $this->assertEquals(1_200_000.0, $abattement);
        $this->assertEquals(2_800_000.0, $base);
    }

    #[Test]
    public function abattement_taux_est_30_pourcent(): void
    {
        $this->assertEquals(0.30, FiscalService::ABATTEMENT_IRPP);
    }

    // ════════════════════════════════════════════════════════════════════════
    // CFPB — Contribution Foncière des Propriétés Bâties (CGI art. 95-110)
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function cfpb_taux_est_5_pourcent(): void
    {
        $this->assertEquals(0.05, FiscalService::CFPB_TAUX);
    }

    #[Test]
    public function cfpb_calculee_sur_revenus_bruts(): void
    {
        // Revenus bruts = 3 600 000 F → CFPB = 3 600 000 × 5% = 180 000 F
        $revenus = 3_600_000.0;
        $cfpb    = round($revenus * FiscalService::CFPB_TAUX, 2);

        $this->assertEquals(180_000.0, $cfpb);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Scénario complet — propriétaire avec 12 mois de loyer
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function scenario_proprietaire_loyer_200k_mensuel_12_mois(): void
    {
        // 12 paiements × 200 000 F = 2 400 000 F de revenus bruts loyers
        $revenusBruts   = 12 * 200_000;         // 2 400 000
        $abattement     = round($revenusBruts * FiscalService::ABATTEMENT_IRPP, 2); // 720 000
        $baseImposable  = round($revenusBruts - $abattement, 2); // 1 680 000
        $irpp           = FiscalService::calculerIRPP($baseImposable);
        $cfpb           = round($revenusBruts * FiscalService::CFPB_TAUX, 2);

        // 1 680 000 F → tranche 20% : (1 680 000 - 1 500 000) × 20% = 36 000 F
        $this->assertEquals(2_400_000.0, $revenusBruts);
        $this->assertEquals(720_000.0,   $abattement);
        $this->assertEquals(1_680_000.0, $baseImposable);
        $this->assertEqualsWithDelta(36_000.0, $irpp, 1.0);
        $this->assertEquals(120_000.0,   $cfpb);
    }
}
