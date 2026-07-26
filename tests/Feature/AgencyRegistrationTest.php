<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests du formulaire d'inscription agence simplifié.
 *
 * Le formulaire ne demande plus que 4 champs :
 * agency_name, admin_name, admin_email, admin_password (+ cgu).
 * L'email admin sert aussi d'email de contact pour l'agence.
 */
class AgencyRegistrationTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────

    private function payloadValide(): array
    {
        return [
            'agency_name'                 => 'Agence Alpha Dakar',
            'admin_name'                  => 'Mamadou Diallo',
            'admin_email'                 => 'mamadou@agence.sn',
            'admin_password'              => 'Secret@123',
            'admin_password_confirmation' => 'Secret@123',
            'pays'                        => 'SN',
            'cgu'                         => '1',
        ];
    }

    // ── Accès au formulaire ───────────────────────────────────────────────

    #[Test]
    public function formulaire_inscription_accessible_aux_invites(): void
    {
        $this->get(route('agency.register'))
             ->assertOk();
    }

    /**
     * Le garde-fou doit être visible dans le HTML, pas seulement côté validation :
     * un pays fermé ne doit même pas apparaître dans la liste déroulante.
     */
    #[Test]
    public function le_formulaire_ne_propose_que_les_pays_ouverts(): void
    {
        $response = $this->get(route('agency.register'))->assertOk();

        $response->assertSee('name="pays"', false)
                 ->assertSee('value="SN"', false)
                 ->assertDontSee('value="CI"', false)
                 ->assertDontSee('value="ML"', false);
    }

    #[Test]
    public function utilisateur_connecte_ne_voit_pas_le_formulaire_inscription(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);
        $admin = User::factory()->create(['role' => 'admin', 'agency_id' => $agency->id]);

        $this->actingAs($admin)
             ->get(route('agency.register'))
             ->assertRedirect();
    }

    // ── Inscription complète ──────────────────────────────────────────────

    #[Test]
    public function inscription_complete_cree_agence_admin_et_abonnement_essai(): void
    {
        Event::fake([Registered::class]);

        $this->post(route('agency.register.store'), $this->payloadValide())
             ->assertRedirect();

        // Agence créée avec l'email admin comme email de contact
        $this->assertDatabaseHas('agencies', [
            'name'  => 'Agence Alpha Dakar',
            'email' => 'mamadou@agence.sn',
            'actif' => true,
        ]);

        // Admin créé avec bon rôle
        $this->assertDatabaseHas('users', [
            'name'  => 'Mamadou Diallo',
            'email' => 'mamadou@agence.sn',
            'role'  => 'admin',
        ]);

        // Abonnement d'essai créé
        $agency = Agency::where('email', 'mamadou@agence.sn')->first();
        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'essai',
        ]);
    }

    #[Test]
    public function inscription_connecte_automatiquement_ladmin(): void
    {
        Event::fake([Registered::class]);

        $this->post(route('agency.register.store'), $this->payloadValide())
             ->assertRedirect();

        $this->assertAuthenticated();
    }

    #[Test]
    public function evenement_registered_est_dispatche(): void
    {
        Event::fake([Registered::class]);

        $this->post(route('agency.register.store'), $this->payloadValide());

        Event::assertDispatched(Registered::class);
    }

    #[Test]
    public function admin_est_rattache_a_lagence_creee(): void
    {
        Event::fake([Registered::class]);

        $this->post(route('agency.register.store'), $this->payloadValide());

        $user   = User::where('email', 'mamadou@agence.sn')->first();
        $agency = Agency::where('email', 'mamadou@agence.sn')->first();

        $this->assertNotNull($user);
        $this->assertNotNull($agency);
        $this->assertEquals($agency->id, $user->agency_id);
    }

    #[Test]
    public function agence_a_un_slug_unique(): void
    {
        Event::fake([Registered::class]);

        $this->post(route('agency.register.store'), $this->payloadValide());

        $agency = Agency::where('email', 'mamadou@agence.sn')->first();
        $this->assertNotNull($agency->slug);
        $this->assertStringContainsString('agence-alpha-dakar', $agency->slug);
    }

    #[Test]
    public function essai_dure_30_jours(): void
    {
        Event::fake([Registered::class]);

        $this->post(route('agency.register.store'), $this->payloadValide());

        $agency = Agency::where('email', 'mamadou@agence.sn')->first();
        $sub    = Subscription::where('agency_id', $agency->id)->first();

        $this->assertNotNull($sub->date_fin_essai);
        $this->assertEquals(
            now()->addDays(30)->toDateString(),
            $sub->date_fin_essai->toDateString()
        );
    }

    // ── Pays déclaré (internationalisation — étape 1) ─────────────────────

    /**
     * Le pays est une donnée DÉCLARATIVE : il est enregistré tel qu'il a été
     * choisi, et il pré-remplit la devise depuis config/pays.php.
     */
    #[Test]
    public function le_pays_choisi_et_sa_devise_sont_enregistres(): void
    {
        $this->post(route('agency.register.store'), $this->payloadValide());

        $this->assertDatabaseHas('agencies', [
            'name'   => 'Agence Alpha Dakar',
            'pays'   => 'SN',
            'devise' => 'XOF',
        ]);
    }

    #[Test]
    public function pays_manquant_est_rejete(): void
    {
        $payload = $this->payloadValide();
        unset($payload['pays']);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('pays');

        $this->assertDatabaseMissing('agencies', ['name' => 'Agence Alpha Dakar']);
    }

    /**
     * GARDE-FOU : un pays qui existe au catalogue mais n'est pas encore OUVERT
     * (config/pays.ouverts) doit être refusé. C'est ce qui empêche la création
     * d'une agence dont les documents fiscaux et la devise ne sont pas maîtrisés.
     */
    #[Test]
    public function pays_hors_liste_ouverte_est_rejete(): void
    {
        $payload = array_merge($this->payloadValide(), ['pays' => 'CI']);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('pays');

        $this->assertDatabaseMissing('agencies', ['pays' => 'CI']);
    }

    // ── Validation des champs ─────────────────────────────────────────────

    #[Test]
    public function nom_agence_manquant_est_rejete(): void
    {
        $payload = $this->payloadValide();
        unset($payload['agency_name']);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('agency_name');
    }

    #[Test]
    public function nom_agence_trop_court_est_rejete(): void
    {
        $payload = array_merge($this->payloadValide(), ['agency_name' => 'A']);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('agency_name');
    }

    #[Test]
    public function nom_admin_manquant_est_rejete(): void
    {
        $payload = $this->payloadValide();
        unset($payload['admin_name']);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_name');
    }

    #[Test]
    public function email_admin_manquant_est_rejete(): void
    {
        $payload = $this->payloadValide();
        unset($payload['admin_email']);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_email');
    }

    #[Test]
    public function email_admin_invalide_est_rejete(): void
    {
        $payload = array_merge($this->payloadValide(), ['admin_email' => 'pas-un-email']);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_email');
    }

    #[Test]
    public function email_admin_deja_utilise_est_rejete(): void
    {
        User::factory()->create(['email' => 'mamadou@agence.sn']);

        $this->post(route('agency.register.store'), $this->payloadValide())
             ->assertSessionHasErrors('admin_email');
    }

    #[Test]
    public function mot_de_passe_trop_court_est_rejete(): void
    {
        $payload = array_merge($this->payloadValide(), [
            'admin_password'              => 'abc',
            'admin_password_confirmation' => 'abc',
        ]);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_password');
    }

    #[Test]
    public function mot_de_passe_sans_majuscule_est_rejete(): void
    {
        $payload = array_merge($this->payloadValide(), [
            'admin_password'              => 'secret@123',
            'admin_password_confirmation' => 'secret@123',
        ]);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_password');
    }

    #[Test]
    public function mot_de_passe_sans_chiffre_est_rejete(): void
    {
        $payload = array_merge($this->payloadValide(), [
            'admin_password'              => 'Secret@abc',
            'admin_password_confirmation' => 'Secret@abc',
        ]);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_password');
    }

    #[Test]
    public function mot_de_passe_sans_caractere_special_est_rejete(): void
    {
        $payload = array_merge($this->payloadValide(), [
            'admin_password'              => 'Secret12345',
            'admin_password_confirmation' => 'Secret12345',
        ]);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_password');
    }

    #[Test]
    public function mots_de_passe_non_identiques_sont_rejetes(): void
    {
        $payload = array_merge($this->payloadValide(), [
            'admin_password_confirmation' => 'AutreMotDePasse@99',
        ]);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_password');
    }

    #[Test]
    public function cgu_non_cochee_est_rejetee(): void
    {
        $payload = $this->payloadValide();
        unset($payload['cgu']);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('cgu');
    }

    #[Test]
    public function payload_vide_genere_toutes_les_erreurs_obligatoires(): void
    {
        $this->post(route('agency.register.store'), [])
             ->assertSessionHasErrors(['agency_name', 'admin_name', 'admin_email', 'admin_password', 'cgu']);
    }

    // ── Isolation (pas de création partielle si erreur) ───────────────────

    #[Test]
    public function aucune_agence_creee_si_validation_echoue(): void
    {
        $this->post(route('agency.register.store'), []);

        $this->assertDatabaseCount('agencies', 0);
        $this->assertDatabaseCount('subscriptions', 0);
    }
}
