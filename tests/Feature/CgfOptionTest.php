<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Proprietaire;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * CgfOptionTest — connexion end-to-end de l'option CGF (Art. 75).
 *
 * Vérifie route → contrôleur → validation → persistance → exclusion mutuelle
 * sur la fiche propriétaire.
 */
class CgfOptionTest extends TestCase
{
    use RefreshDatabase;

    private User   $admin;
    private User   $proprio;
    private Proprietaire $profil;

    protected function setUp(): void
    {
        parent::setUp();

        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        $this->admin = User::factory()->create(['role' => 'admin', 'agency_id' => $agency->id]);

        $this->proprio = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $agency->id]);
        $this->profil  = Proprietaire::create([
            'user_id'                => $this->proprio->id,
            'est_personne_morale_is' => false, // Particulier
        ]);
    }

    #[Test]
    public function admin_peut_opter_pour_la_cgf_et_le_montant_est_calcule(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.users.cgf.option', $this->proprio), [
                'cgf_annee'             => 2026,
                'cgf_revenu_brut_prevu' => 15_000_000,
                'cgf_mode_paiement'     => 'trois_versements',
            ])
            ->assertRedirect();

        $this->profil->refresh();
        $this->assertTrue($this->profil->cgf_active);
        $this->assertEquals(2026, $this->profil->cgf_annee);
        $this->assertEquals(1_875_000, $this->profil->cgf_montant);        // 15M × 1,5/12
        $this->assertCount(3, $this->profil->cgf_echeances);
        $this->assertEquals(625_000, (int) $this->profil->cgf_echeances[0]['montant']);
    }

    #[Test]
    public function revenu_superieur_a_30m_bloque_option(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.users.cgf.option', $this->proprio), [
                'cgf_annee'             => 2026,
                'cgf_revenu_brut_prevu' => 35_000_000,
                'cgf_mode_paiement'     => 'unique',
            ])
            ->assertSessionHas('cgf_error');

        $this->profil->refresh();
        $this->assertFalse($this->profil->cgf_active);
        $this->assertNull($this->profil->cgf_montant);
    }

    #[Test]
    public function cgf_active_masque_lencart_irpp_sur_la_fiche(): void
    {
        // Option active pour l'année en cours → exclusion mutuelle
        $this->profil->update([
            'cgf_active'            => true,
            'cgf_annee'             => now()->year,
            'cgf_revenu_brut_prevu' => 10_000_000,
            'cgf_montant'           => 833_333,
            'cgf_mode_paiement'     => 'unique',
            'cgf_echeances'         => [['rang' => 1, 'libelle' => 'Versement unique — fin février', 'date' => now()->year.'-02-28', 'montant' => 833_333]],
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->proprio))
            ->assertOk()
            ->assertSee('Couvert par la CGF')
            ->assertDontSee('Estimation IRPP');
    }

    #[Test]
    public function personne_morale_ne_peut_pas_opter(): void
    {
        $this->profil->update(['est_personne_morale_is' => true]);

        $this->actingAs($this->admin)
            ->post(route('admin.users.cgf.option', $this->proprio), [
                'cgf_annee'             => 2026,
                'cgf_revenu_brut_prevu' => 10_000_000,
                'cgf_mode_paiement'     => 'unique',
            ])
            ->assertForbidden();

        $this->assertFalse($this->profil->fresh()->cgf_active);
    }

    #[Test]
    public function admin_peut_retirer_loption_cgf(): void
    {
        $this->profil->update([
            'cgf_active' => true, 'cgf_annee' => 2026, 'cgf_montant' => 833_333,
            'cgf_mode_paiement' => 'unique', 'cgf_revenu_brut_prevu' => 10_000_000,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.users.cgf.desactiver', $this->proprio))
            ->assertRedirect();

        $this->profil->refresh();
        $this->assertFalse($this->profil->cgf_active);
        $this->assertNull($this->profil->cgf_annee);
        $this->assertNull($this->profil->cgf_montant);
    }
}
