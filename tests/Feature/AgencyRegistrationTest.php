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
 * AgencyRegistrationTest — Inscription d'une nouvelle agence (onboarding SaaS).
 *
 * Vérifie que le formulaire crée en transaction atomique :
 *  - l'agence (Agency)
 *  - l'admin (User role='admin')
 *  - l'abonnement d'essai 30 jours (Subscription statut='essai')
 * Et que les validations bloquent les données invalides.
 */
class AgencyRegistrationTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────

    private function payloadValide(): array
    {
        return [
            'agency_name'              => 'Agence Alpha Dakar',
            'agency_email'             => 'alpha@agence.sn',
            'agency_telephone'         => '+221 77 000 00 00',
            'agency_adresse'           => '12 Rue Félix Faure, Dakar',
            'admin_name'               => 'Mamadou Diallo',
            'admin_email'              => 'mamadou@agence.sn',
            'admin_password'           => 'Secret@123',
            'admin_password_confirmation' => 'Secret@123',
            'cgu'                      => '1',
        ];
    }

    // ── Tests formulaire ──────────────────────────────────────────────────

    #[Test]
    public function formulaire_inscription_accessible_aux_invites()
    {
        $this->get(route('agency.register'))
             ->assertOk();
    }

    #[Test]
    public function utilisateur_connecte_ne_voit_pas_le_formulaire_inscription()
    {
        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create(['agency_id' => $agency->id, 'statut' => 'actif', 'plan' => 'annuel', 'date_debut_abonnement' => now()->subMonth(), 'date_fin_abonnement' => now()->addYear()]);
        $admin = User::factory()->create(['role' => 'admin', 'agency_id' => $agency->id]);

        $this->actingAs($admin)
             ->get(route('agency.register'))
             ->assertRedirect();
    }

    // ── Tests inscription complète ────────────────────────────────────────

    #[Test]
    public function inscription_complete_cree_agence_admin_et_abonnement_essai()
    {
        Event::fake([Registered::class]);

        $this->post(route('agency.register.store'), $this->payloadValide())
             ->assertRedirect();

        // Agence créée et active
        $this->assertDatabaseHas('agencies', [
            'name'  => 'Agence Alpha Dakar',
            'email' => 'alpha@agence.sn',
            'actif' => true,
        ]);

        // Admin créé avec bon rôle
        $this->assertDatabaseHas('users', [
            'name'  => 'Mamadou Diallo',
            'email' => 'mamadou@agence.sn',
            'role'  => 'admin',
        ]);

        // Abonnement d'essai créé
        $agency = Agency::where('email', 'alpha@agence.sn')->first();
        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'essai',
        ]);
    }

    #[Test]
    public function inscription_connecte_automatiquement_ladmin()
    {
        Event::fake([Registered::class]);

        $this->post(route('agency.register.store'), $this->payloadValide())
             ->assertRedirect();

        $this->assertAuthenticated();
    }

    #[Test]
    public function evenement_registered_est_dispatche()
    {
        Event::fake([Registered::class]);

        $this->post(route('agency.register.store'), $this->payloadValide());

        Event::assertDispatched(Registered::class);
    }

    // ── Tests validation ──────────────────────────────────────────────────

    #[Test]
    public function email_agence_deja_utilise_est_rejete()
    {
        Agency::factory()->create(['email' => 'alpha@agence.sn']);
        $payload = $this->payloadValide();

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('agency_email');
    }

    #[Test]
    public function email_admin_deja_utilise_est_rejete()
    {
        User::factory()->create(['email' => 'mamadou@agence.sn']);
        $payload = $this->payloadValide();

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_email');
    }

    #[Test]
    public function cgu_non_cochee_est_rejetee()
    {
        $payload = $this->payloadValide();
        unset($payload['cgu']);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('cgu');
    }

    #[Test]
    public function mot_de_passe_trop_court_est_rejete()
    {
        $payload = $this->payloadValide();
        $payload['admin_password']              = 'abc';
        $payload['admin_password_confirmation'] = 'abc';

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_password');
    }

    #[Test]
    public function mot_de_passe_sans_caractere_special_est_rejete()
    {
        $payload = $this->payloadValide();
        $payload['admin_password']              = 'Secret12345';
        $payload['admin_password_confirmation'] = 'Secret12345';

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_password');
    }

    #[Test]
    public function mots_de_passe_non_identiques_sont_rejetes()
    {
        $payload = $this->payloadValide();
        $payload['admin_password_confirmation'] = 'AutreMotDePasse@99';

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('admin_password');
    }

    #[Test]
    public function nom_agence_manquant_est_rejete()
    {
        $payload = $this->payloadValide();
        unset($payload['agency_name']);

        $this->post(route('agency.register.store'), $payload)
             ->assertSessionHasErrors('agency_name');
    }

    #[Test]
    public function champs_optionnels_telephone_et_adresse_non_requis()
    {
        Event::fake([Registered::class]);

        $payload = $this->payloadValide();
        unset($payload['agency_telephone'], $payload['agency_adresse']);

        $this->post(route('agency.register.store'), $payload)
             ->assertRedirect();

        $this->assertDatabaseHas('agencies', ['name' => 'Agence Alpha Dakar']);
    }
}
