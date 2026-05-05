<?php

namespace Tests\Unit;

use App\Services\FiscalService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * BilanFiscalCalculTest — Vérifie les calculs fiscaux annuels (sans DB).
 *
 * Références légales testées :
 *  - Abattement 30% (CGI art. 68 §c)
 *  - Barème IRPP progressif (CGI art. 173)
 *  - CFPB 5% (CGI art. 283-294) — assiette légale = valeur locative cadastrale (Art. 290-291)
 */
class BilanFiscalCalculTest extends TestCase
{
    // ════════════════════════════════════════════════════════════════════════
    // IRPP — Barème progressif 7 tranches (CGI art. 173 — Loi 2022-19)
    // Texte officiel lu intégralement (kof-experts.sn CGI annoté janv. 2023) :
    //   0          – 630 000 F    → 0%
    //   630 001    – 1 500 000 F  → 20%
    //   1 500 001  – 4 000 000 F  → 30%
    //   4 000 001  – 8 000 000 F  → 35%
    //   8 000 001  – 13 500 000 F → 37%
    //   13 500 001 – 50 000 000 F → 40%
    //   > 50 000 000 F            → 43%
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function irpp_est_zero_sous_le_seuil_exonere(): void
    {
        // Moins de 630 000 F → tranche 0% (seuil exonéré)
        $irpp = FiscalService::calculerIRPP(500_000);

        $this->assertEquals(0.0, $irpp);
    }

    #[Test]
    public function irpp_tranche_20_pourcent(): void
    {
        // Base = 1 200 000 F (dans la tranche 20% : 630 001–1 500 000 F)
        // Imposable : (1 200 000 – 630 000) × 20% = 570 000 × 20% = 114 000 F
        $irpp = FiscalService::calculerIRPP(1_200_000);

        $this->assertEqualsWithDelta(114_000.0, $irpp, 1.0);
    }

    #[Test]
    public function irpp_tranche_30_pourcent(): void
    {
        // Base = 5 000 000 F
        // Tranche 0%  [0 → 630k]       = 0
        // Tranche 20% [630k → 1,5M]    = 870 000 × 20% = 174 000
        // Tranche 30% [1,5M → 4M]      = 2 500 000 × 30% = 750 000
        // Tranche 35% [4M → 5M]        = 1 000 000 × 35% = 350 000
        // Total ≈ 1 274 000 F
        $irpp = FiscalService::calculerIRPP(5_000_000);

        $this->assertEqualsWithDelta(1_274_000.0, $irpp, 1.0);
    }

    #[Test]
    public function irpp_tranche_37_pourcent(): void
    {
        // Base = 10 000 000 F (dans la tranche 37% : 8M → 13.5M)
        // Tranche 0%  [0 → 630k]       =           0
        // Tranche 20% [630k → 1,5M]    = 870 000 × 20% =   174 000
        // Tranche 30% [1,5M → 4M]      = 2 500 000 × 30% =   750 000
        // Tranche 35% [4M → 8M]        = 4 000 000 × 35% = 1 400 000
        // Tranche 37% [8M → 10M]       = 2 000 000 × 37% =   740 000
        // Total = 3 064 000 F
        $irpp = FiscalService::calculerIRPP(10_000_000);

        $this->assertEqualsWithDelta(3_064_000.0, $irpp, 1.0);
    }

    #[Test]
    public function irpp_base_zero_est_zero(): void
    {
        $this->assertEquals(0.0, FiscalService::calculerIRPP(0));
    }

    // ════════════════════════════════════════════════════════════════════════
    // Abattement 30% + base imposable (CGI art. 68 §c)
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
    // CFPB — Contribution Foncière des Propriétés Bâties (CGI art. 283-294)
    // Art. 294 : taux = 5% | Art. 290-291 : assiette = valeur locative CADASTRALE
    // NB : le moteur approxime sur les loyers réels faute de données cadastrales.
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function cfpb_taux_est_5_pourcent(): void
    {
        $this->assertEquals(0.05, FiscalService::CFPB_TAUX);
    }

    #[Test]
    public function cfpb_estimee_sur_revenus_bruts_approximation(): void
    {
        // Approximation : 3 600 000 × 5% = 180 000 F
        // (assiette légale réelle = valeur locative cadastrale Art. 290-291)
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

        // 1 680 000 F :
        //   Tranche 0%  [0 → 630k]      = 0
        //   Tranche 20% [630k → 1,5M]   = 870 000 × 20% = 174 000
        //   Tranche 30% [1,5M → 1,68M]  = 180 000 × 30% = 54 000
        //   Total IRPP ≈ 228 000 F
        $this->assertEquals(2_400_000.0, $revenusBruts);
        $this->assertEquals(720_000.0,   $abattement);
        $this->assertEquals(1_680_000.0, $baseImposable);
        $this->assertEqualsWithDelta(228_000.0, $irpp, 1.0);
        $this->assertEquals(120_000.0,   $cfpb);
    }
}
