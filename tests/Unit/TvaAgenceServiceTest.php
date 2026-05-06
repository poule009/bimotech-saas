<?php

namespace Tests\Unit;

use App\Models\Agency;
use App\Models\Paiement;
use App\Models\TvaDeclaration;
use App\Services\TvaAgenceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TvaAgenceServiceTest extends TestCase
{
    use RefreshDatabase;

    private TvaAgenceService $service;
    private Agency $agency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TvaAgenceService();
        $this->agency  = Agency::factory()->create();
    }

    #[Test]
    public function test_agregation_tva_collectee_depuis_paiements(): void
    {
        foreach ([9000, 18000, 27000] as $tva) {
            Paiement::factory()->create([
                'agency_id'        => $this->agency->id,
                'date_paiement'    => '2026-03-10',
                'tva_commission'   => $tva,
                'tva_loyer'        => 0,
                'tva_charges'      => 0,
                'tva_frais_agence' => 0,
                'statut'           => 'valide',
            ]);
        }

        // Paiement annulé — ne doit pas être inclus dans l'agrégation
        Paiement::factory()->create([
            'agency_id'      => $this->agency->id,
            'date_paiement'  => '2026-03-15',
            'tva_commission' => 50000,
            'statut'         => 'annule',
        ]);

        $result = $this->service->calculerTvaCollectee($this->agency->id, 3, 2026);

        $this->assertEquals(54000, $result['total_tva_collectee']);
        $this->assertEquals(3, $result['nombre_paiements']);
        $this->assertEquals(54000, $result['tva_commissions']);
        $this->assertEquals(0, $result['tva_loyers_commerciaux']);
    }

    #[Test]
    public function test_credit_reporte_du_mois_precedent(): void
    {
        // Déclaration mars déposée avec un crédit sortant de 15 000 FCFA
        TvaDeclaration::create([
            'agency_id'              => $this->agency->id,
            'mois'                   => 3,
            'annee'                  => 2026,
            'total_tva_collectee'    => 20000,
            'total_tva_deductible'   => 35000,
            'credit_reporte_entrant' => 0,
            'tva_nette_due'          => 0,
            'credit_reporte_sortant' => 15000,
            'statut'                 => 'deposee',
        ]);

        // creerOuMettreAJour pour avril (aucun paiement → TVA collectée = 0)
        $declAvril = $this->service->creerOuMettreAJour($this->agency->id, 4, 2026);

        $this->assertEquals(15000, (float) $declAvril->credit_reporte_entrant);
    }

    #[Test]
    public function test_tva_nette_due_positive(): void
    {
        $decl = TvaDeclaration::create([
            'agency_id'              => $this->agency->id,
            'mois'                   => 4,
            'annee'                  => 2026,
            'total_tva_collectee'    => 100000,
            'total_tva_deductible'   => 30000,
            'credit_reporte_entrant' => 0,
            'tva_nette_due'          => 0,
            'credit_reporte_sortant' => 0,
            'statut'                 => 'brouillon',
        ]);

        $decl->calculerTvaNette();

        $this->assertEquals(70000, (float) $decl->tva_nette_due);
        $this->assertEquals(0, (float) $decl->credit_reporte_sortant);
    }

    #[Test]
    public function test_tva_nette_credit_reportable(): void
    {
        $decl = TvaDeclaration::create([
            'agency_id'              => $this->agency->id,
            'mois'                   => 4,
            'annee'                  => 2026,
            'total_tva_collectee'    => 20000,
            'total_tva_deductible'   => 50000,
            'credit_reporte_entrant' => 0,
            'tva_nette_due'          => 0,
            'credit_reporte_sortant' => 0,
            'statut'                 => 'brouillon',
        ]);

        $decl->calculerTvaNette();

        $this->assertEquals(0, (float) $decl->tva_nette_due);
        $this->assertEquals(30000, (float) $decl->credit_reporte_sortant);
    }

    #[Test]
    public function test_credit_entrant_absorbe_tva_due(): void
    {
        // collectée=80k, déductible=10k, crédit entrant=100k
        // → nette = 80k - 10k - 100k = -30k → crédit sortant = 30k, nette due = 0
        $decl = TvaDeclaration::create([
            'agency_id'              => $this->agency->id,
            'mois'                   => 5,
            'annee'                  => 2026,
            'total_tva_collectee'    => 80000,
            'total_tva_deductible'   => 10000,
            'credit_reporte_entrant' => 100000,
            'tva_nette_due'          => 0,
            'credit_reporte_sortant' => 0,
            'statut'                 => 'brouillon',
        ]);

        $decl->calculerTvaNette();

        $this->assertEquals(0, (float) $decl->tva_nette_due);
        $this->assertEquals(30000, (float) $decl->credit_reporte_sortant);
    }

    #[Test]
    public function test_statut_en_retard(): void
    {
        // Mai 2026 — échéance légale = 15 juin 2026
        $decl = TvaDeclaration::create([
            'agency_id'              => $this->agency->id,
            'mois'                   => 5,
            'annee'                  => 2026,
            'total_tva_collectee'    => 50000,
            'total_tva_deductible'   => 0,
            'credit_reporte_entrant' => 0,
            'tva_nette_due'          => 50000,
            'credit_reporte_sortant' => 0,
            'statut'                 => 'brouillon',
        ]);

        Carbon::setTestNow(Carbon::parse('2026-06-16 10:00:00', 'Africa/Dakar'));

        $this->assertTrue($decl->fresh()->est_en_retard);

        Carbon::setTestNow();
    }

    #[Test]
    public function test_statut_pas_retard_avant_echeance(): void
    {
        // Mai 2026 — échéance légale = 15 juin 2026
        $decl = TvaDeclaration::create([
            'agency_id'              => $this->agency->id,
            'mois'                   => 5,
            'annee'                  => 2026,
            'total_tva_collectee'    => 50000,
            'total_tva_deductible'   => 0,
            'credit_reporte_entrant' => 0,
            'tva_nette_due'          => 50000,
            'credit_reporte_sortant' => 0,
            'statut'                 => 'brouillon',
        ]);

        Carbon::setTestNow(Carbon::parse('2026-06-14 10:00:00', 'Africa/Dakar'));

        $this->assertFalse($decl->fresh()->est_en_retard);

        Carbon::setTestNow();
    }
}
