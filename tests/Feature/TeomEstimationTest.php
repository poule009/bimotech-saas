<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Proprietaire;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * TeomEstimationTest — TEOM (Taxe d'Enlèvement des Ordures Ménagères).
 *
 * Même assiette que la CFPB, taux communal (3,6% Dakar / 3% ailleurs),
 * même badge estimation_structurelle, même exclusion CGF.
 */
class TeomEstimationTest extends TestCase
{
    use RefreshDatabase;

    private User    $admin;
    private User    $proprio;
    private Proprietaire $profil;
    private Agency  $agency;

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

        $this->admin   = User::factory()->create(['role' => 'admin', 'agency_id' => $this->agency->id]);
        $this->proprio = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $this->agency->id]);
        $this->profil  = Proprietaire::create([
            'user_id'                => $this->proprio->id,
            'est_personne_morale_is' => false,
        ]);
    }

    private function bien(string $ville): Bien
    {
        return Bien::factory()->create([
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $this->proprio->id,
            'ville'           => $ville,
            'loyer_mensuel'   => 200_000, // valeur locative 2 400 000
            'statut'          => 'loue',
        ]);
    }

    #[Test]
    public function observer_derive_teom_dakar_a_3_6_pourcent(): void
    {
        $bien = $this->bien('Dakar');

        $this->assertEquals('3.6', (string) $bien->teom_taux_applique);
        $this->assertEquals(86_400, $bien->teom_montant_estime); // 2 400 000 × 3,6%
    }

    #[Test]
    public function observer_derive_teom_hors_dakar_a_3_pourcent(): void
    {
        $bien = $this->bien('Thiès');

        $this->assertEquals('3.0', (string) $bien->teom_taux_applique);
        $this->assertEquals(72_000, $bien->teom_montant_estime); // 2 400 000 × 3%
    }

    #[Test]
    public function fiche_bien_dakar_affiche_teom_86400(): void
    {
        $bien = $this->bien('Dakar');

        $this->actingAs($this->admin)
            ->get(route('admin.biens.show', $bien))
            ->assertOk()
            ->assertSee('TEOM estimée')
            ->assertSee('86 400');
    }

    #[Test]
    public function fiche_bien_hors_dakar_affiche_teom_72000(): void
    {
        $bien = $this->bien('Thiès');

        $this->actingAs($this->admin)
            ->get(route('admin.biens.show', $bien))
            ->assertOk()
            ->assertSee('72 000');
    }

    #[Test]
    public function bien_couvert_par_cgf_masque_teom(): void
    {
        $this->profil->update([
            'cgf_active' => true, 'cgf_annee' => now()->year,
            'cgf_revenu_brut_prevu' => 2_400_000, 'cgf_montant' => 200_000,
            'cgf_mode_paiement' => 'unique',
        ]);
        $bien = $this->bien('Dakar');

        $this->actingAs($this->admin)
            ->get(route('admin.biens.show', $bien))
            ->assertOk()
            ->assertSee('opté pour la')
            ->assertDontSee('86 400');
    }
}
