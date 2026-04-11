<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * SuperAdminControllerTest — Tests du panneau SuperAdmin.
 *
 * Couvre :
 *  - dashboard : accessible au superadmin, interdit aux autres rôles
 *  - createAgency / storeAgency : création d'une agence + admin + subscription essai
 *  - storeAgency : validation (email unique, mot de passe, etc.)
 *  - toggleActif : active / désactive une agence
 *  - activerAbonnement : active un plan payant sur une agence
 *  - reinitialiserEssai : remet l'essai à 30 jours
 *  - subscriptions : liste des abonnements
 */
class SuperAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->createOne(['role' => 'superadmin']);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helper : créer une agence avec subscription
    // ────────────────────────────────────────────────────────────────────────

    private function creerAgenceAvecEssai(): Agency
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'        => $agency->id,
            'statut'           => 'essai',
            'date_debut_essai' => now(),
            'date_fin_essai'   => now()->addDays(30),
        ]);

        return $agency;
    }

    // ════════════════════════════════════════════════════════════════════════
    // Dashboard SuperAdmin
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function superadmin_voit_son_dashboard(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertViewIs('superadmin.dashboard');
    }

    #[Test]
    public function admin_ne_peut_pas_acceder_au_dashboard_superadmin(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);
        $admin = User::factory()->createOne(['role' => 'admin', 'agency_id' => $agency->id]);

        $this->actingAs($admin)
            ->get(route('superadmin.dashboard'))
            ->assertForbidden();
    }

    #[Test]
    public function superadmin_voit_la_liste_des_abonnements(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('superadmin.subscriptions'))
            ->assertOk()
            ->assertViewIs('superadmin.subscriptions');
    }

    // ════════════════════════════════════════════════════════════════════════
    // Création d'une agence
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function superadmin_voit_le_formulaire_creation_agence(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('superadmin.agencies.create'))
            ->assertOk()
            ->assertViewIs('superadmin.create-agency');
    }

    #[Test]
    public function superadmin_peut_creer_une_agence(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.agencies.store'), [
                'agency_name'              => 'Agence Nouvelle',
                'agency_email'             => 'nouvelle@agence.sn',
                'agency_telephone'         => '+221 33 800 00 00',
                'agency_adresse'           => 'Dakar, Sénégal',
                'admin_name'               => 'Admin Nouveau',
                'admin_email'              => 'admin@nouvelle.sn',
                'admin_password'           => 'Password123!',
                'admin_password_confirmation' => 'Password123!',
            ])
            ->assertRedirect(route('superadmin.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('agencies', ['name' => 'Agence Nouvelle']);
        $this->assertDatabaseHas('users',    ['email' => 'admin@nouvelle.sn', 'role' => 'admin']);
        $this->assertDatabaseHas('subscriptions', ['statut' => 'essai']);
    }

    #[Test]
    public function creation_agence_email_duplique_retourne_erreur(): void
    {
        Agency::factory()->create(['email' => 'existing@agence.sn']);

        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.agencies.store'), [
                'agency_name'              => 'Agence Dupliquée',
                'agency_email'             => 'existing@agence.sn',  // déjà pris
                'admin_name'               => 'Admin',
                'admin_email'              => 'newadmin@agence.sn',
                'admin_password'           => 'Password123!',
                'admin_password_confirmation' => 'Password123!',
            ])
            ->assertSessionHasErrors('agency_email');
    }

    #[Test]
    public function creation_agence_mot_de_passe_trop_court_retourne_erreur(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.agencies.store'), [
                'agency_name'              => 'Agence Test',
                'agency_email'             => 'test@agence.sn',
                'admin_name'               => 'Admin',
                'admin_email'              => 'admin@test.sn',
                'admin_password'           => '123',   // trop court
                'admin_password_confirmation' => '123',
            ])
            ->assertSessionHasErrors('admin_password');
    }

    // ════════════════════════════════════════════════════════════════════════
    // Toggle actif
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function superadmin_peut_desactiver_une_agence_active(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);

        $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.agencies.toggle', $agency))
            ->assertRedirect(route('superadmin.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('agencies', ['id' => $agency->id, 'actif' => false]);
    }

    #[Test]
    public function superadmin_peut_reactiver_une_agence_inactive(): void
    {
        $agency = Agency::factory()->create(['actif' => false]);

        $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.agencies.toggle', $agency))
            ->assertRedirect(route('superadmin.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('agencies', ['id' => $agency->id, 'actif' => true]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Activer un abonnement manuellement
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function superadmin_peut_activer_abonnement_mensuel(): void
    {
        $agency = $this->creerAgenceAvecEssai();

        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.agencies.abonnement.activer', $agency), [
                'plan' => 'mensuel',
            ])
            ->assertRedirect(route('superadmin.subscriptions'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'actif',
            'plan'      => 'mensuel',
        ]);
    }

    #[Test]
    public function plan_invalide_retourne_erreur_validation(): void
    {
        $agency = $this->creerAgenceAvecEssai();

        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.agencies.abonnement.activer', $agency), [
                'plan' => 'journalier',   // invalide
            ])
            ->assertSessionHasErrors('plan');
    }

    // ════════════════════════════════════════════════════════════════════════
    // Réinitialiser l'essai
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function superadmin_peut_reinitialiser_essai(): void
    {
        $agency       = $this->creerAgenceAvecEssai();
        $subscription = $agency->subscription;

        // Simule un essai expiré
        $subscription->update([
            'statut'        => 'expiré',
            'date_fin_essai' => now()->subDays(10),
        ]);

        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.agencies.essai.reinitialiser', $agency))
            ->assertRedirect(route('superadmin.subscriptions'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'essai',
        ]);

        // La nouvelle date de fin doit être dans le futur
        $this->assertTrue(
            $agency->subscription()->value('date_fin_essai') > now()->toDateTimeString()
        );
    }
}
