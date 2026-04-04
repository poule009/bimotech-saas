<?php

namespace Tests\Unit\Services;

use App\Models\Contrat;
use App\Models\Paiement;
use App\Services\FiscalService;
use App\Services\PaiementService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PaiementServiceTest — Tests du service de gestion des paiements.
 *
 * Ces tests utilisent RefreshDatabase car le PaiementService interagit
 * avec la base de données (création d'échéances, lecture d'impayés...).
 *
 * Les factories Contrat et Paiement doivent exister.
 *
 * Lancer : php artisan test --filter=PaiementServiceTest
 */
class PaiementServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaiementService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaiementService(new FiscalService());
    }

    // ─── genererEcheances ───────────────────────────────────────────────────

    /** @test */
    public function genere_le_bon_nombre_d_echeances_pour_un_bail_12_mois(): void
    {
        $contrat = Contrat::factory()->create([
            'date_debut'         => '2025-01-01',
            'date_fin'           => '2025-12-31',
            'loyer_hors_charges' => 200_000,
            'charges'            => 0,
        ]);

        $echeances = $this->service->genererEcheances($contrat);

        $this->assertCount(12, $echeances);
    }

    /** @test */
    public function genere_le_bon_nombre_d_echeances_pour_un_bail_6_mois(): void
    {
        $contrat = Contrat::factory()->create([
            'date_debut'         => '2025-01-01',
            'date_fin'           => '2025-06-30',
            'loyer_hors_charges' => 150_000,
            'charges'            => 0,
        ]);

        $echeances = $this->service->genererEcheances($contrat);

        $this->assertCount(6, $echeances);
    }

    /** @test */
    public function toutes_les_echeances_sont_en_statut_en_attente(): void
    {
        $contrat = Contrat::factory()->create([
            'date_debut' => '2025-01-01',
            'date_fin'   => '2025-03-31',
            'loyer_hors_charges' => 200_000,
        ]);

        $echeances = $this->service->genererEcheances($contrat);

        foreach ($echeances as $echeance) {
            $this->assertSame('en_attente', $echeance->statut);
        }
    }

    /** @test */
    public function les_montants_fiscaux_sont_correctement_calcules_dans_les_echeances(): void
    {
        $contrat = Contrat::factory()->create([
            'date_debut'         => '2025-01-01',
            'date_fin'           => '2025-01-31',
            'loyer_hors_charges' => 200_000,
            'charges'            => 25_000,
        ]);

        $echeances = $this->service->genererEcheances($contrat);
        $echeance  = $echeances->first();

        $this->assertSame(20_000, (int) $echeance->commission_ht);  // 10% de 200 000
        $this->assertSame(3_600, (int) $echeance->tva_montant);     // 18% de 20 000
        $this->assertSame(10_000, (int) $echeance->tom_montant);    // 5% de 200 000
        $this->assertSame(225_000, (int) $echeance->montant_total); // 200 000 + 25 000
    }

    // ─── enregistrerReglement ───────────────────────────────────────────────



    /** @test */
    public function regler_un_paiement_deja_valide_leve_une_exception(): void
    {
        $paiement = Paiement::factory()->create(['statut' => 'valide']);

        $this->expectException(\LogicException::class);

        $this->service->enregistrerReglement($paiement, 200_000, '2025-06-05');
    }

    // ─── getImpayes ─────────────────────────────────────────────────────────

    /** @test */
    public function get_impayes_retourne_uniquement_les_echeances_depassees(): void
    {
        Carbon::setTestNow('2025-07-01');

        $agencyId = 1;

        // Paiement en retard
        $impaye = Paiement::factory()->create([
            'agency_id'     => $agencyId,
            'statut'        => 'en_attente',
            'date_echeance' => '2025-06-01', // Dépassée
        ]);

        // Paiement futur (pas encore en retard)
        Paiement::factory()->create([
            'agency_id'     => $agencyId,
            'statut'        => 'en_attente',
            'date_echeance' => '2025-07-05',
        ]);

        // Paiement validé (ne compte pas)
        Paiement::factory()->create([
            'agency_id'     => $agencyId,
            'statut'        => 'valide',
            'date_echeance' => '2025-06-01',
        ]);

        $impayes = $this->service->getImpayes($agencyId);

        $this->assertCount(1, $impayes);
        $this->assertSame($impaye->id, $impayes->first()->id);

        Carbon::setTestNow(); // Reset
    }

    // ─── getTableauDeBordMensuel ────────────────────────────────────────────

    /** @test */
    public function tableau_de_bord_mensuel_calcule_taux_recouvrement(): void
    {
        $agencyId = 1;
        $periode  = '2025-06';

        // 3 payés, 1 impayé → taux = 75%
        Paiement::factory()->count(3)->create([
            'agency_id'      => $agencyId,
            'mois_concerne'  => $periode,
            'statut'         => 'valide',
            'montant_total'  => 200_000,
            'montant_recu'   => 200_000,
        ]);

        Paiement::factory()->create([
            'agency_id'     => $agencyId,
            'mois_concerne' => $periode,
            'statut'        => 'en_attente',
            'montant_total' => 200_000,
        ]);

        $bord = $this->service->getTableauDeBordMensuel($agencyId, 2025, 6);

        $this->assertSame(75.0, $bord['taux_recouvrement']);
        $this->assertSame(3, $bord['nb_payes']);
        $this->assertSame(1, $bord['nb_impayes']);
    }
}