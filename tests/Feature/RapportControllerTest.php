<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * RapportControllerTest — Tests du rapport financier.
 *
 * Couvre :
 *  - financier : affiche la vue avec les données du mois courant
 *  - financier : filtre par annee/mois via query string
 *  - financier : accès réservé aux admins
 *  - exportPdf : télécharge un PDF avec Content-Type application/pdf
 *  - exportPdf : accès réservé aux admins
 */
class RapportControllerTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User   $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $this->agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        $this->admin = User::factory()->createOne([
            'role'      => 'admin',
            'agency_id' => $this->agency->id,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Rapport financier — vue HTML
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_voit_le_rapport_financier(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.rapports.financier'))
            ->assertOk()
            ->assertViewIs('rapports.financier');
    }

    #[Test]
    public function rapport_accepte_filtre_par_annee_et_mois(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.rapports.financier', [
                'annee' => now()->year,
                'mois'  => now()->month,
            ]))
            ->assertOk()
            ->assertViewHasAll(['paiementsMois', 'kpiMois', 'statsGenerales']);
    }

    #[Test]
    public function locataire_ne_peut_pas_voir_le_rapport(): void
    {
        $locataire = User::factory()->createOne([
            'role'      => 'locataire',
            'agency_id' => $this->agency->id,
        ]);

        $this->actingAs($locataire)
            ->get(route('admin.rapports.financier'))
            ->assertForbidden();
    }

    #[Test]
    public function proprietaire_ne_peut_pas_voir_le_rapport(): void
    {
        $proprio = User::factory()->createOne([
            'role'      => 'proprietaire',
            'agency_id' => $this->agency->id,
        ]);

        $this->actingAs($proprio)
            ->get(route('admin.rapports.financier'))
            ->assertForbidden();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Export PDF
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_peut_exporter_le_rapport_en_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.rapports.financier.export-pdf'));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type', '')
        );
    }

    #[Test]
    public function export_pdf_accepte_filtre_mois(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.rapports.financier.export-pdf', [
                'annee' => now()->subMonth()->year,
                'mois'  => now()->subMonth()->month,
            ]));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type', '')
        );
    }

    #[Test]
    public function locataire_ne_peut_pas_exporter_le_pdf(): void
    {
        $locataire = User::factory()->createOne([
            'role'      => 'locataire',
            'agency_id' => $this->agency->id,
        ]);

        $this->actingAs($locataire)
            ->get(route('admin.rapports.financier.export-pdf'))
            ->assertForbidden();
    }
}
