<?php

namespace Tests\Feature\Auth;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests du flow Google OAuth.
 *
 * Trois scénarios d'entrée :
 *  1. google_id connu → connexion directe
 *  2. email connu    → liaison google_id + connexion
 *  3. nouveau        → session → formulaire → création agence/user/subscription
 */
class GoogleAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── Helper : faux utilisateur Google ──────────────────────────────────

    private function mockGoogleUser(
        string $id    = 'google-id-123',
        string $name  = 'Ibrahima Fall',
        string $email = 'ibrahima@gmail.com'
    ): \Mockery\MockInterface {
        $mock = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $mock->shouldReceive('getId')->andReturn($id);
        $mock->shouldReceive('getName')->andReturn($name);
        $mock->shouldReceive('getEmail')->andReturn($email);

        return $mock;
    }

    private function fakeSocialiteUser(
        string $id    = 'google-id-123',
        string $name  = 'Ibrahima Fall',
        string $email = 'ibrahima@gmail.com'
    ): void {
        Socialite::shouldReceive('driver->user')
            ->andReturn($this->mockGoogleUser($id, $name, $email));
    }

    // ── Redirection vers Google ────────────────────────────────────────────

    #[Test]
    public function redirect_vers_google_redirige_linvite(): void
    {
        Socialite::shouldReceive('driver->redirect')
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth?fake'));

        $this->get(route('auth.google'))
             ->assertRedirect();
    }

    // ── Callback : utilisateur existant par google_id ─────────────────────

    #[Test]
    public function callback_connecte_utilisateur_existant_par_google_id(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create(['agency_id' => $agency->id, 'statut' => 'actif', 'plan' => 'annuel', 'date_debut_abonnement' => now()->subMonth(), 'date_fin_abonnement' => now()->addYear()]);
        $user = User::factory()->create([
            'agency_id' => $agency->id,
            'role'      => 'admin',
            'google_id' => 'google-id-123',
            'email'     => 'ibrahima@gmail.com',
        ]);

        $this->fakeSocialiteUser('google-id-123', 'Ibrahima Fall', 'ibrahima@gmail.com');

        $this->get(route('auth.google.callback'))
             ->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    // ── Callback : email connu sans google_id → liaison ──────────────────

    #[Test]
    public function callback_lie_google_id_a_un_compte_existant_par_email(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create(['agency_id' => $agency->id, 'statut' => 'actif', 'plan' => 'annuel', 'date_debut_abonnement' => now()->subMonth(), 'date_fin_abonnement' => now()->addYear()]);
        $user = User::factory()->create([
            'agency_id' => $agency->id,
            'role'      => 'admin',
            'google_id' => null,
            'email'     => 'ibrahima@gmail.com',
        ]);

        $this->fakeSocialiteUser('google-new-id', 'Ibrahima Fall', 'ibrahima@gmail.com');

        $this->get(route('auth.google.callback'));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', [
            'id'        => $user->id,
            'google_id' => 'google-new-id',
        ]);
    }

    #[Test]
    public function callback_connecte_apres_liaison_du_google_id(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create(['agency_id' => $agency->id, 'statut' => 'actif', 'plan' => 'annuel', 'date_debut_abonnement' => now()->subMonth(), 'date_fin_abonnement' => now()->addYear()]);
        User::factory()->create([
            'agency_id' => $agency->id,
            'role'      => 'admin',
            'google_id' => null,
            'email'     => 'ibrahima@gmail.com',
        ]);

        $this->fakeSocialiteUser('google-new-id', 'Ibrahima Fall', 'ibrahima@gmail.com');

        $this->get(route('auth.google.callback'))
             ->assertRedirect();

        $this->assertAuthenticated();
    }

    // ── Callback : nouvel utilisateur → session + redirect ───────────────

    #[Test]
    public function callback_nouvel_utilisateur_redirige_vers_formulaire_agence(): void
    {
        $this->fakeSocialiteUser('google-id-new', 'Nouveau User', 'nouveau@gmail.com');

        $this->get(route('auth.google.callback'))
             ->assertRedirect(route('agency.register.google.complete'));
    }

    #[Test]
    public function callback_nouvel_utilisateur_stocke_donnees_en_session(): void
    {
        $this->fakeSocialiteUser('google-id-new', 'Ibrahima Fall', 'ibrahima@gmail.com');

        $this->get(route('auth.google.callback'));

        $this->assertEquals('google-id-new',      session('google_registration.google_id'));
        $this->assertEquals('Ibrahima Fall',       session('google_registration.name'));
        $this->assertEquals('ibrahima@gmail.com',  session('google_registration.email'));
    }

    #[Test]
    public function callback_nouvel_utilisateur_ne_cree_pas_de_compte_directement(): void
    {
        $this->fakeSocialiteUser('google-id-new', 'Ibrahima Fall', 'ibrahima@gmail.com');

        $this->get(route('auth.google.callback'));

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('agencies', 0);
        $this->assertGuest();
    }

    // ── Callback : erreur Google ──────────────────────────────────────────

    #[Test]
    public function callback_echoue_redirige_avec_erreur(): void
    {
        Socialite::shouldReceive('driver->user')
            ->andThrow(new \Exception('OAuth error'));

        $this->get(route('auth.google.callback'))
             ->assertRedirect(route('agency.register'))
             ->assertSessionHasErrors('google');
    }

    // ── Formulaire de completion ──────────────────────────────────────────

    #[Test]
    public function formulaire_completion_accessible_avec_session_google(): void
    {
        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->get(route('agency.register.google.complete'))
          ->assertOk();
    }

    #[Test]
    public function formulaire_completion_inaccessible_sans_session(): void
    {
        $this->get(route('agency.register.google.complete'))
             ->assertRedirect(route('agency.register'));
    }

    #[Test]
    public function formulaire_completion_affiche_nom_google(): void
    {
        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->get(route('agency.register.google.complete'))
          ->assertSee('Ibrahima Fall');
    }

    // ── Store completion : création complète ─────────────────────────────

    #[Test]
    public function completion_cree_agence_avec_email_google(): void
    {
        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->post(route('agency.register.google.store'), [
            'agency_name' => 'Agence Beta Sénégal',
            'cgu'         => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('agencies', [
            'name'  => 'Agence Beta Sénégal',
            'email' => 'ibrahima@gmail.com',
            'actif' => true,
        ]);
    }

    #[Test]
    public function completion_cree_user_avec_google_id_et_email_verifie(): void
    {
        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->post(route('agency.register.google.store'), [
            'agency_name' => 'Agence Beta Sénégal',
            'cgu'         => '1',
        ]);

        $user = User::where('email', 'ibrahima@gmail.com')->first();

        $this->assertNotNull($user);
        $this->assertEquals('Ibrahima Fall',      $user->name);
        $this->assertEquals('google-id-123',      $user->google_id);
        $this->assertEquals('admin',              $user->role);
        $this->assertNotNull($user->email_verified_at);
    }

    #[Test]
    public function completion_cree_abonnement_essai_30_jours(): void
    {
        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->post(route('agency.register.google.store'), [
            'agency_name' => 'Agence Beta Sénégal',
            'cgu'         => '1',
        ]);

        $agency = Agency::where('email', 'ibrahima@gmail.com')->first();
        $sub    = Subscription::where('agency_id', $agency->id)->first();

        $this->assertNotNull($sub);
        $this->assertEquals('essai', $sub->statut);
        $this->assertEquals(
            now()->addDays(30)->toDateString(),
            $sub->date_fin_essai->toDateString()
        );
    }

    #[Test]
    public function completion_connecte_automatiquement_le_user(): void
    {
        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->post(route('agency.register.google.store'), [
            'agency_name' => 'Agence Beta Sénégal',
            'cgu'         => '1',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals('ibrahima@gmail.com', auth()->user()->email);
    }

    #[Test]
    public function completion_nettoie_la_session_google(): void
    {
        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->post(route('agency.register.google.store'), [
            'agency_name' => 'Agence Beta Sénégal',
            'cgu'         => '1',
        ]);

        $this->assertNull(session('google_registration'));
    }

    #[Test]
    public function completion_rattache_user_a_son_agence(): void
    {
        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->post(route('agency.register.google.store'), [
            'agency_name' => 'Agence Beta Sénégal',
            'cgu'         => '1',
        ]);

        $user   = User::where('email', 'ibrahima@gmail.com')->first();
        $agency = Agency::where('email', 'ibrahima@gmail.com')->first();

        $this->assertEquals($agency->id, $user->agency_id);
    }

    // ── Validation du formulaire de completion ────────────────────────────

    #[Test]
    public function completion_nom_agence_manquant_est_rejete(): void
    {
        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->post(route('agency.register.google.store'), ['cgu' => '1'])
          ->assertSessionHasErrors('agency_name');

        $this->assertDatabaseCount('agencies', 0);
    }

    #[Test]
    public function completion_nom_agence_trop_court_est_rejete(): void
    {
        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->post(route('agency.register.google.store'), [
            'agency_name' => 'A',
            'cgu'         => '1',
        ])->assertSessionHasErrors('agency_name');
    }

    #[Test]
    public function completion_cgu_non_cochee_est_rejetee(): void
    {
        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->post(route('agency.register.google.store'), [
            'agency_name' => 'Agence Beta Sénégal',
        ])->assertSessionHasErrors('cgu');
    }

    // ── Sécurité ──────────────────────────────────────────────────────────

    #[Test]
    public function completion_sans_session_redirige_vers_inscription(): void
    {
        $this->post(route('agency.register.google.store'), [
            'agency_name' => 'Agence Beta Sénégal',
            'cgu'         => '1',
        ])->assertRedirect(route('agency.register'));

        $this->assertDatabaseCount('agencies', 0);
    }

    #[Test]
    public function completion_email_deja_pris_redirige_vers_login(): void
    {
        // Simule une race condition : l'email est pris entre le callback et le store
        $agency = Agency::factory()->create();
        User::factory()->create(['agency_id' => $agency->id, 'email' => 'ibrahima@gmail.com']);

        $this->withSession([
            'google_registration' => [
                'name'      => 'Ibrahima Fall',
                'email'     => 'ibrahima@gmail.com',
                'google_id' => 'google-id-123',
            ],
        ])->post(route('agency.register.google.store'), [
            'agency_name' => 'Agence Beta Sénégal',
            'cgu'         => '1',
        ])->assertRedirect(route('login'))
          ->assertSessionHasErrors('email');
    }

    #[Test]
    public function utilisateur_connecte_ne_peut_pas_acceder_au_formulaire_completion(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create(['agency_id' => $agency->id, 'statut' => 'actif', 'plan' => 'annuel', 'date_debut_abonnement' => now()->subMonth(), 'date_fin_abonnement' => now()->addYear()]);
        $admin = User::factory()->create(['role' => 'admin', 'agency_id' => $agency->id]);

        $this->actingAs($admin)
             ->withSession([
                 'google_registration' => [
                     'name'      => 'Ibrahima Fall',
                     'email'     => 'ibrahima@gmail.com',
                     'google_id' => 'google-id-123',
                 ],
             ])->get(route('agency.register.google.complete'))
             ->assertRedirect();
    }
}
