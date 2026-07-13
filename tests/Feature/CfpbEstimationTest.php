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
 * CfpbEstimationTest — CFPB (Art. 283-294), estimation structurelle au niveau du Bien.
 *
 * Vérifie : dérivation observer, affichage fiche Bien (badge permanent, même vacant),
 * exclusion mutuelle CGF, agrégation sur la fiche Propriétaire.
 */
class CfpbEstimationTest extends TestCase
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

    private function bien(int $loyer, string $statut = 'loue'): Bien
    {
        return Bien::factory()->create([
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $this->proprio->id,
            'loyer_mensuel'   => $loyer,
            'statut'          => $statut,
        ]);
    }

    #[Test]
    public function observer_derive_les_champs_cfpb_a_la_creation(): void
    {
        $bien = $this->bien(200_000);

        $this->assertEquals(2_400_000, $bien->cfpb_valeur_locative_estimee);
        $this->assertEquals(120_000, $bien->cfpb_montant_estime);
        $this->assertEquals('estimation_structurelle', $bien->cfpb_statut_calcul);
    }

    #[Test]
    public function fiche_bien_affiche_cfpb_avec_badge_permanent(): void
    {
        $bien = $this->bien(200_000);

        $this->actingAs($this->admin)
            ->get(route('admin.biens.show', $bien))
            ->assertOk()
            ->assertSee('CFPB estimée')
            ->assertSee('120 000')
            ->assertSee('peut différer significativement'); // badge estimation_structurelle
    }

    #[Test]
    public function bien_vacant_affiche_quand_meme_la_cfpb(): void
    {
        // CFPB due même si vacant (CFPB-06) — 150k → 90 000
        $bien = $this->bien(150_000, 'disponible');

        $this->actingAs($this->admin)
            ->get(route('admin.biens.show', $bien))
            ->assertOk()
            ->assertSee('90 000')
            ->assertSee('peut différer significativement');
    }

    #[Test]
    public function bien_couvert_par_cgf_masque_lestimation_cfpb(): void
    {
        $this->profil->update([
            'cgf_active' => true, 'cgf_annee' => now()->year,
            'cgf_revenu_brut_prevu' => 3_000_000, 'cgf_montant' => 250_000,
            'cgf_mode_paiement' => 'unique',
        ]);
        $bien = $this->bien(200_000);

        $this->actingAs($this->admin)
            ->get(route('admin.biens.show', $bien))
            ->assertOk()
            ->assertSee('opté pour la')
            ->assertDontSee('peut différer significativement');
    }

    #[Test]
    public function fiche_proprietaire_agrege_la_cfpb_sur_tous_les_biens(): void
    {
        // 200k + 350k + 500k → 120k + 210k + 300k = 630 000
        $this->bien(200_000);
        $this->bien(350_000);
        $this->bien(500_000);

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->proprio))
            ->assertOk()
            ->assertSee('CFPB estimée — total')
            ->assertSee('630 000');
    }

    #[Test]
    public function fiche_proprietaire_masque_cfpb_si_cgf_active(): void
    {
        $this->bien(200_000);
        $this->profil->update([
            'cgf_active' => true, 'cgf_annee' => now()->year,
            'cgf_revenu_brut_prevu' => 2_400_000, 'cgf_montant' => 200_000,
            'cgf_mode_paiement' => 'unique',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->proprio))
            ->assertOk()
            ->assertDontSee('CFPB estimée — total');
    }
}
