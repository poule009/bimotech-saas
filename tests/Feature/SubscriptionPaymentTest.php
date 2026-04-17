<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * SubscriptionPaymentTest — Tests du flux de paiement d'abonnement (PayTech).
 *
 * Couvre (mode simulation — aucun appel API réel) :
 *  - Initiation du paiement : activation directe de l'abonnement
 *  - Callback IPN : traitement correct du payload PayTech
 *  - Idempotence : deux callbacks avec le même token n'activent qu'une fois
 *  - Sécurité : seul un admin authentifié peut initier un paiement
 *  - Accès anonyme bloqué
 */
class SubscriptionPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User   $admin;
    private Agency $agency;

    protected function setUp(): void
    {
        parent::setUp();

        // Mode simulation activé par défaut (config services.paytech.mode = simulation)
        config(['services.paytech.mode' => 'simulation']);

        $this->agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'        => $this->agency->id,
            'statut'           => 'essai',
            'date_debut_essai' => now(),
            'date_fin_essai'   => now()->addDays(14),
        ]);

        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'agency_id' => $this->agency->id,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Initiation paiement — mode simulation
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_peut_initier_un_paiement_mensuel(): void
    {
        $this->actingAs($this->admin)
             ->post(route('subscription.initier'), ['plan' => 'mensuel'])
             ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'actif',
            'plan'      => 'mensuel',
        ]);
    }

    #[Test]
    public function admin_peut_initier_un_paiement_annuel(): void
    {
        $this->actingAs($this->admin)
             ->post(route('subscription.initier'), ['plan' => 'annuel'])
             ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'actif',
            'plan'      => 'annuel',
        ]);
    }

    #[Test]
    public function abonnement_actif_apres_paiement_simulation(): void
    {
        $this->actingAs($this->admin)
             ->post(route('subscription.initier'), ['plan' => 'mensuel']);

        $subscription = $this->agency->fresh()->subscription;

        $this->assertEquals('actif', $subscription->statut);
        $this->assertNotNull($subscription->date_fin_abonnement);
        $this->assertTrue($subscription->estActif());
    }

    #[Test]
    public function plan_invalide_retourne_erreur(): void
    {
        $this->actingAs($this->admin)
             ->post(route('subscription.initier'), ['plan' => 'plan_inexistant'])
             ->assertRedirect()
             ->assertSessionHasErrors();

        // L'abonnement reste en essai
        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'essai',
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Sécurité — accès
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function invité_ne_peut_pas_initier_un_paiement(): void
    {
        $this->post(route('subscription.initier'), ['plan' => 'mensuel'])
             ->assertRedirect(route('login'));
    }

    #[Test]
    public function locataire_ne_peut_pas_initier_un_paiement(): void
    {
        $locataire = User::factory()->create([
            'role'      => 'locataire',
            'agency_id' => $this->agency->id,
        ]);

        $this->actingAs($locataire)
             ->post(route('subscription.initier'), ['plan' => 'mensuel'])
             ->assertForbidden();
    }

    #[Test]
    public function proprietaire_ne_peut_pas_initier_un_paiement(): void
    {
        $proprio = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $this->agency->id,
        ]);

        $this->actingAs($proprio)
             ->post(route('subscription.initier'), ['plan' => 'mensuel'])
             ->assertForbidden();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Callback IPN PayTech
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function callback_ipn_active_labonnement(): void
    {
        $payload = $this->buildIpnPayload('TOKEN-TEST-001', 'completed', 'mensuel');

        $this->postJson(route('subscription.callback'), $payload)
             ->assertOk()
             ->assertJson(['success' => true]);

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'actif',
            'plan'      => 'mensuel',
        ]);
    }

    #[Test]
    public function callback_ipn_avec_statut_non_completed_est_ignore(): void
    {
        $payload = $this->buildIpnPayload('TOKEN-TEST-002', 'pending', 'mensuel');

        $this->postJson(route('subscription.callback'), $payload)
             ->assertStatus(422)
             ->assertJson(['success' => false]);

        // L'abonnement reste en essai
        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'essai',
        ]);
    }

    #[Test]
    public function callback_ipn_est_idempotent(): void
    {
        $payload = $this->buildIpnPayload('TOKEN-IDEM-001', 'completed', 'annuel');

        // Premier appel
        $this->postJson(route('subscription.callback'), $payload)->assertOk();

        // Deuxième appel avec le même token
        $this->postJson(route('subscription.callback'), $payload)->assertOk();

        // L'abonnement n'est activé qu'une seule fois
        $this->assertEquals(
            1,
            Subscription::where('agency_id', $this->agency->id)
                ->where('statut', 'actif')
                ->count()
        );
    }

    #[Test]
    public function callback_ipn_sans_token_retourne_erreur(): void
    {
        $this->postJson(route('subscription.callback'), [])
             ->assertStatus(422)
             ->assertJson(['success' => false]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Helper
    // ════════════════════════════════════════════════════════════════════════

    private function buildIpnPayload(string $token, string $status, string $plan): array
    {
        return [
            'data' => [
                'invoice' => [
                    'token'  => $token,
                    'status' => $status,
                    'custom_data' => [
                        'agency_id' => $this->agency->id,
                        'plan'      => $plan,
                    ],
                ],
            ],
        ];
    }
}
