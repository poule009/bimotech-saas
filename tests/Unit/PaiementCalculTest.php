<?php

namespace Tests\Unit;

use App\Models\Paiement;
use Tests\TestCase;

class PaiementCalculTest extends TestCase
{
    public function test_calcul_commission_10_pourcent(): void
    {
        $calcul = Paiement::calculerMontants(250000, 10);

        $this->assertEquals(25000.0,  $calcul['commission_ht']);
        $this->assertEquals(4500.0,   $calcul['tva']);
        $this->assertEquals(29500.0,  $calcul['commission_ttc']);
        $this->assertEquals(220500.0, $calcul['net_proprietaire']);
    }

    public function test_calcul_commission_8_pourcent(): void
    {
        $calcul = Paiement::calculerMontants(600000, 8);

        $this->assertEquals(48000.0,  $calcul['commission_ht']);
        $this->assertEquals(8640.0,   $calcul['tva']);
        $this->assertEquals(56640.0,  $calcul['commission_ttc']);
        $this->assertEquals(543360.0, $calcul['net_proprietaire']);
    }

    public function test_calcul_commission_12_pourcent(): void
    {
        $calcul = Paiement::calculerMontants(120000, 12);

        $this->assertEquals(14400.0,  $calcul['commission_ht']);
        $this->assertEquals(2592.0,   $calcul['tva']);
        $this->assertEquals(16992.0,  $calcul['commission_ttc']);
        $this->assertEquals(103008.0, $calcul['net_proprietaire']);
    }

    public function test_tva_est_18_pourcent_de_commission_ht(): void
    {
        $calcul = Paiement::calculerMontants(250000, 10);

        $tvAttendue = round($calcul['commission_ht'] * 0.18, 2);
        $this->assertEquals($tvAttendue, $calcul['tva']);
    }

    public function test_commission_ttc_est_ht_plus_tva(): void
    {
        $calcul = Paiement::calculerMontants(250000, 10);

        $this->assertEquals(
            $calcul['commission_ht'] + $calcul['tva'],
            $calcul['commission_ttc']
        );
    }

    public function test_net_proprio_est_montant_moins_commission_ttc(): void
    {
        $montant = 250000;
        $calcul  = Paiement::calculerMontants($montant, 10);

        $this->assertEquals(
            $montant - $calcul['commission_ttc'],
            $calcul['net_proprietaire']
        );
    }

    public function test_retourne_4_cles(): void
    {
        $calcul = Paiement::calculerMontants(100000, 5);

        $this->assertArrayHasKey('commission_ht',    $calcul);
        $this->assertArrayHasKey('tva',              $calcul);
        $this->assertArrayHasKey('commission_ttc',   $calcul);
        $this->assertArrayHasKey('net_proprietaire', $calcul);
    }
}