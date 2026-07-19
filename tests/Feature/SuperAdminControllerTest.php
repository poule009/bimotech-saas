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
 *  - facturation : liste des transactions (a absorbé l'ancienne liste d'abonnements
 *    et l'écran « paiements en attente »)
 */
class SuperAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin PRINCIPAL : ces scénarios couvrent l'accès complet à la plateforme
        // (facturation, agences, impersonation), gouverné par sa.section depuis le
        // module « Équipe interne ». Sans ce drapeau, les gardes de section renvoient 403.
        $this->superAdmin = User::factory()->createOne([
            'role'             => 'superadmin',
            'sa_est_principal' => true,
        ]);
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
    public function superadmin_voit_la_facturation(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('superadmin.facturation'))
            ->assertOk()
            ->assertViewIs('superadmin.facturation');
    }

    /** L'ancien écran « paiements en attente » a été absorbé par la facturation. */
    #[Test]
    public function paiements_attente_redirige_vers_le_filtre_facturation(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('superadmin.paiements.attente'))
            ->assertRedirect(route('superadmin.facturation', ['statut' => 'en_attente', 'periode' => 'tout']));
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
            ->assertRedirect(route('superadmin.agencies.show', $agency))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('agencies', ['id' => $agency->id, 'actif' => false]);

        // Couper l'accès d'une agence doit laisser une trace nominative.
        $this->assertDatabaseHas('activity_logs', [
            'agency_id' => $agency->id,
            'user_id'   => $this->superAdmin->id,
            'action'    => 'agence_suspendue',
        ]);
    }

    #[Test]
    public function superadmin_peut_reactiver_une_agence_inactive(): void
    {
        $agency = Agency::factory()->create(['actif' => false]);

        $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.agencies.toggle', $agency))
            ->assertRedirect(route('superadmin.agencies.show', $agency))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('agencies', ['id' => $agency->id, 'actif' => true]);

        $this->assertDatabaseHas('activity_logs', [
            'agency_id' => $agency->id,
            'action'    => 'agence_reactivee',
        ]);
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
            ->assertRedirect(route('superadmin.agencies.show', $agency))
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
            ->assertRedirect(route('superadmin.agencies.show', $agency))
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

    // ════════════════════════════════════════════════════════════════════════
    // Support / Debug — impersonation
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function superadmin_voit_la_page_support(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('superadmin.support'))
            ->assertOk()
            ->assertViewIs('superadmin.support');
    }

    #[Test]
    public function la_page_support_affiche_une_session_active(): void
    {
        $agency = $this->creerAgenceAvecEssai();
        $admin  = User::factory()->createOne(['role' => 'admin', 'agency_id' => $agency->id]);

        \App\Models\ImpersonationSession::create([
            'admin_id'    => $this->superAdmin->id,
            'user_id'     => $admin->id,
            'agency_id'   => $agency->id,
            'admin_name'  => $this->superAdmin->name,
            'agency_name' => $agency->name,
            'started_at'  => now()->subMinutes(3),
        ]);

        $this->actingAs($this->superAdmin)
            ->get(route('superadmin.support'))
            ->assertOk()
            ->assertSee($this->superAdmin->name)
            ->assertSee($agency->name)
            ->assertSee('Terminer la session');
    }

    #[Test]
    public function recherche_avec_un_seul_resultat_redirige_vers_la_fiche(): void
    {
        $agency = Agency::factory()->create(['name' => 'Baobab Gestion', 'actif' => true]);

        $this->actingAs($this->superAdmin)
            ->get(route('superadmin.support', ['q' => 'Baobab']))
            ->assertRedirect(route('superadmin.agencies.show', $agency));
    }

    #[Test]
    public function impersonate_cree_une_session_active(): void
    {
        $agency = $this->creerAgenceAvecEssai();
        $admin  = User::factory()->createOne(['role' => 'admin', 'agency_id' => $agency->id]);

        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.impersonate', $admin))
            ->assertRedirect();

        $this->assertDatabaseHas('impersonation_sessions', [
            'admin_id'   => $this->superAdmin->id,
            'user_id'    => $admin->id,
            'agency_id'  => $agency->id,
            'ended_at'   => null,
        ]);
    }

    #[Test]
    public function un_superadmin_peut_couper_la_session_d_un_collegue(): void
    {
        $autreAdmin = User::factory()->createOne(['role' => 'superadmin']);
        $agency     = $this->creerAgenceAvecEssai();
        $cible      = User::factory()->createOne(['role' => 'admin', 'agency_id' => $agency->id]);

        $session = \App\Models\ImpersonationSession::create([
            'admin_id'   => $autreAdmin->id,
            'user_id'    => $cible->id,
            'agency_id'  => $agency->id,
            'started_at' => now()->subMinutes(5),
        ]);

        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.support.impersonations.terminate', $session))
            ->assertRedirect()
            ->assertSessionHas('success');

        $session->refresh();
        $this->assertNotNull($session->ended_at);
        $this->assertSame('revoked', $session->end_reason);
        $this->assertSame($this->superAdmin->id, $session->ended_by);
    }

    #[Test]
    public function couper_une_session_deja_terminee_ne_fait_rien(): void
    {
        $agency  = $this->creerAgenceAvecEssai();
        $cible   = User::factory()->createOne(['role' => 'admin', 'agency_id' => $agency->id]);
        $session = \App\Models\ImpersonationSession::create([
            'admin_id'   => $this->superAdmin->id,
            'user_id'    => $cible->id,
            'agency_id'  => $agency->id,
            'started_at' => now()->subMinutes(10),
            'ended_at'   => now()->subMinutes(2),
            'end_reason' => 'normal',
        ]);

        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.support.impersonations.terminate', $session))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    #[Test]
    public function une_session_revoquee_deconnecte_l_admin_a_sa_prochaine_action(): void
    {
        $agency = $this->creerAgenceAvecEssai();
        $admin  = User::factory()->createOne(['role' => 'admin', 'agency_id' => $agency->id]);

        // Le super-admin démarre l'impersonation : session tracée + clés de session posées.
        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.impersonate', $admin));

        $session = \App\Models\ImpersonationSession::firstOrFail();

        // Un autre super-admin coupe la session à distance (pose seulement ended_at).
        $session->update([
            'ended_at'   => now(),
            'ended_by'   => $this->superAdmin->id,
            'end_reason' => 'revoked',
        ]);

        // Prochaine action de l'admin impersonné → le middleware le renvoie au
        // Super Admin réel avec un bandeau de notification.
        $this->get(route('superadmin.dashboard'))
            ->assertRedirect(route('superadmin.dashboard'))
            ->assertSessionHas('warning');

        // Le compte super-admin réel est bien restauré.
        $this->assertSame($this->superAdmin->id, auth()->id());
    }

    #[Test]
    public function la_deconnexion_classique_cloture_la_session_tracee(): void
    {
        $agency = $this->creerAgenceAvecEssai();
        $admin  = User::factory()->createOne(['role' => 'admin', 'agency_id' => $agency->id]);

        // Démarre l'impersonation (auth = admin impersonné, clés de session posées).
        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.impersonate', $admin));

        $session = \App\Models\ImpersonationSession::firstOrFail();
        $this->assertNull($session->ended_at);

        // L'admin se déconnecte via le bouton classique (route logout), pas via
        // « retour Super Admin » → le listener doit clôturer la session.
        $this->post(route('logout'));

        $session->refresh();
        $this->assertNotNull($session->ended_at);
        $this->assertSame('logout', $session->end_reason);
    }

    #[Test]
    public function le_balayage_ferme_les_sessions_abandonnees_et_epargne_les_recentes(): void
    {
        $agency = $this->creerAgenceAvecEssai();
        $admin  = User::factory()->createOne(['role' => 'admin', 'agency_id' => $agency->id]);

        $abandonnee = \App\Models\ImpersonationSession::create([
            'admin_id'   => $this->superAdmin->id,
            'user_id'    => $admin->id,
            'agency_id'  => $agency->id,
            'started_at' => now()->subHours(\App\Models\ImpersonationSession::STALE_AFTER_HOURS + 1),
        ]);
        $recente = \App\Models\ImpersonationSession::create([
            'admin_id'   => $this->superAdmin->id,
            'user_id'    => $admin->id,
            'agency_id'  => $agency->id,
            'started_at' => now()->subHour(),
        ]);

        $this->artisan('impersonation:close-stale')->assertSuccessful();

        $this->assertNotNull($abandonnee->refresh()->ended_at);
        $this->assertSame('expired', $abandonnee->end_reason);
        $this->assertNull($recente->refresh()->ended_at);
    }

    #[Test]
    public function impersonate_enregistre_les_snapshots_de_noms(): void
    {
        $agency = Agency::factory()->create(['name' => 'Baobab Gestion', 'actif' => true]);
        Subscription::factory()->create([
            'agency_id' => $agency->id, 'statut' => 'essai',
            'date_debut_essai' => now(), 'date_fin_essai' => now()->addDays(30),
        ]);
        $admin = User::factory()->createOne(['role' => 'admin', 'agency_id' => $agency->id]);

        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.impersonate', $admin));

        $this->assertDatabaseHas('impersonation_sessions', [
            'admin_name'  => $this->superAdmin->name,
            'agency_name' => 'Baobab Gestion',
        ]);
    }

    #[Test]
    public function la_sortie_normale_cloture_la_session_tracee(): void
    {
        $agency = $this->creerAgenceAvecEssai();
        $admin  = User::factory()->createOne(['role' => 'admin', 'agency_id' => $agency->id]);

        // Démarre l'impersonation (crée la session + pose les clés de session).
        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.impersonate', $admin));

        $session = \App\Models\ImpersonationSession::firstOrFail();
        $this->assertNull($session->ended_at);

        // L'utilisateur courant est maintenant l'admin impersonné : il sort.
        $this->get(route('superadmin.impersonate.stop'))
            ->assertRedirect(route('superadmin.dashboard'));

        $session->refresh();
        $this->assertNotNull($session->ended_at);
        $this->assertSame('normal', $session->end_reason);
    }
}
