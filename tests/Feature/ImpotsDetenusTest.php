<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\TvaDeclaration;
use App\Models\User;
use App\Services\ComptabiliteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ImpotsDetenusTest — le contrôle de caisse doit compter le BRS retenu et la
 * TVA nette due comme argent de tiers détenu pour la DGID.
 */
class ImpotsDetenusTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private Contrat $contrat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agency = Agency::factory()->create(['actif' => true]);
        $proprio   = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $this->agency->id]);
        $locataire = User::factory()->create(['role' => 'locataire', 'agency_id' => $this->agency->id]);
        $bien = Bien::factory()->create([
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $proprio->id,
        ]);
        $this->contrat = Contrat::factory()->create([
            'agency_id'    => $this->agency->id,
            'bien_id'      => $bien->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'actif',
        ]);
    }

    #[Test]
    public function le_brs_et_la_tva_du_mois_sont_comptes_comme_argent_de_tiers(): void
    {
        $periode = now()->startOfMonth();

        // Paiement validé du mois avec BRS retenu
        Paiement::factory()->create([
            'agency_id'  => $this->agency->id,
            'contrat_id' => $this->contrat->id,
            'periode'    => $periode->toDateString(),
            'statut'     => 'valide',
            'brs_amount' => 10_000,
        ]);

        // TVA nette due du mois, déclaration non déposée → détenue
        TvaDeclaration::create([
            'agency_id'     => $this->agency->id,
            'mois'          => (int) $periode->month,
            'annee'         => (int) $periode->year,
            'tva_nette_due' => 7_000,
            'statut'        => 'brouillon',
        ]);

        $impots = app(ComptabiliteService::class)
            ->impotsDetenus($this->agency->id, (int) $periode->year, (int) $periode->month);

        $this->assertSame(10_000.0, $impots['brs']);
        $this->assertSame(7_000.0, $impots['tva']);
        $this->assertSame(17_000.0, $impots['total']);
    }

    #[Test]
    public function une_tva_deja_deposee_n_est_plus_detenue(): void
    {
        $periode = now()->startOfMonth();

        TvaDeclaration::create([
            'agency_id'     => $this->agency->id,
            'mois'          => (int) $periode->month,
            'annee'         => (int) $periode->year,
            'tva_nette_due' => 7_000,
            'statut'        => 'deposee',
        ]);

        $impots = app(ComptabiliteService::class)
            ->impotsDetenus($this->agency->id, (int) $periode->year, (int) $periode->month);

        $this->assertSame(0.0, $impots['tva']);
    }

    #[Test]
    public function un_paiement_non_valide_n_ajoute_pas_de_brs_detenu(): void
    {
        $periode = now()->startOfMonth();

        Paiement::factory()->create([
            'agency_id'  => $this->agency->id,
            'contrat_id' => $this->contrat->id,
            'periode'    => $periode->toDateString(),
            'statut'     => 'unpaid',
            'brs_amount' => 10_000,
        ]);

        $impots = app(ComptabiliteService::class)
            ->impotsDetenus($this->agency->id, (int) $periode->year, (int) $periode->month);

        $this->assertSame(0.0, $impots['brs']);
    }
}
