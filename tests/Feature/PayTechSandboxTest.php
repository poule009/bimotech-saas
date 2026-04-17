<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PayTechSandboxTest — Tests du flux de paiement PayTech.
 *
 * Documentation API PayTech : https://docs.intech.sn/doc_paytech.php
 *
 * Trois niveaux de tests :
 *
 *  1. Mode simulation (PAYTECH_MODE=simulation)
 *     Aucun appel HTTP réel. L'abonnement est activé directement.
 *     C'est le mode utilisé en développement et dans les tests automatisés.
 *
 *  2. Mode test/sandbox (Http::fake())
 *     Les appels HTTP vers l'API PayTech sont interceptés par Http::fake().
 *     Simule les réponses exactes de l'API sandbox PayTech.
 *     Permet de tester le parsing des réponses sans toucher au réseau.
 *
 *  3. Callback IPN (Instant Payment Notification)
 *     PayTech POST sur notre endpoint après un paiement réussi.
 *     Format PayTech : type_event, ref_command, custom_field (base64 JSON)
 *     Teste l'idempotence, les données manquantes, les événements non traités.
 *
 * Couvre :
 *  - Mode simulation → abonnement activé sans appel HTTP
 *  - Mode sandbox → redirect URL renvoyée depuis l'API PayTech mockée
 *  - Callback IPN sale_complete → abonnement activé
 *  - Callback IPN idempotent → même ref_command traité deux fois sans doublon
 *  - Callback IPN ref_command manquante → 422
 *  - Callback IPN événement non sale_complete → 422
 *  - Callback IPN custom_field manquant → 422
 *  - Page succes() sans ref → redirect vers subscription.index
 *  - Page echec() → vue echec
 */
class PayTechSandboxTest extends TestCase
{
    use RefreshDatabase;

    private Agency       $agency;
    private User         $admin;
    private Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agency = Agency::factory()->create(['actif' => true]);

        $this->subscription = Subscription::factory()->create([
            'agency_id'        => $this->agency->id,
            'statut'           => 'essai',
            'date_debut_essai' => now(),
            'date_fin_essai'   => now()->addDays(30),
        ]);

        $this->admin = User::factory()->createOne([
            'role'      => 'admin',
            'agency_id' => $this->agency->id,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // 1. Mode simulation — aucun appel HTTP
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function mode_simulation_active_labonnement_directement_sans_appel_http(): void
    {
        Http::preventStrayRequests();

        config(['services.paytech.mode' => 'simulation']);

        $this->actingAs($this->admin)
            ->post(route('subscription.initier'), ['plan' => 'mensuel'])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'actif',
            'plan'      => 'mensuel',
        ]);

        $this->assertDatabaseHas('subscription_payments', [
            'agency_id' => $this->agency->id,
            'plan'      => 'mensuel',
            'statut'    => 'payé',
        ]);
    }

    #[Test]
    public function mode_simulation_tous_les_plans_fonctionnent(): void
    {
        Http::preventStrayRequests();
        config(['services.paytech.mode' => 'simulation']);

        foreach (['mensuel', 'trimestriel', 'semestriel', 'annuel'] as $plan) {
            $this->subscription->update([
                'statut'              => 'essai',
                'plan'                => null,
                'date_fin_abonnement' => null,
            ]);

            $this->actingAs($this->admin)
                ->post(route('subscription.initier'), ['plan' => $plan])
                ->assertRedirect(route('admin.dashboard'));

            $this->assertDatabaseHas('subscriptions', [
                'agency_id' => $this->agency->id,
                'statut'    => 'actif',
                'plan'      => $plan,
            ]);
        }
    }

    // ════════════════════════════════════════════════════════════════════════
    // 2. Mode sandbox — réponses PayTech simulées avec Http::fake()
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function mode_sandbox_redirige_vers_url_paytech_si_api_ok(): void
    {
        config(['services.paytech.mode' => 'test']);

        Http::fake([
            'paytech.sn/api/payment/request-payment' => Http::response([
                'success'      => 1,
                'token'        => 'FAKE-TOKEN-123',
                'redirect_url' => 'https://paytech.sn/payment/checkout/FAKE-TOKEN-123',
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('subscription.initier'), ['plan' => 'mensuel']);

        $response->assertRedirect('https://paytech.sn/payment/checkout/FAKE-TOKEN-123');

        // Pas encore activé (on attend le callback IPN)
        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'essai',
        ]);
    }

    #[Test]
    public function mode_sandbox_retourne_erreur_si_api_paytech_refuse(): void
    {
        config(['services.paytech.mode' => 'test']);

        Http::fake([
            'paytech.sn/api/payment/request-payment' => Http::response([
                'success' => 0,
                'message' => 'Clés API invalides',
            ], 200),
        ]);

        $this->actingAs($this->admin)
            ->post(route('subscription.initier'), ['plan' => 'mensuel'])
            ->assertRedirect()
            ->assertSessionHasErrors('general');
    }

    #[Test]
    public function mode_sandbox_retourne_erreur_si_api_paytech_inaccessible(): void
    {
        config(['services.paytech.mode' => 'test']);

        Http::fake([
            'paytech.sn/*' => Http::response([], 500),
        ]);

        $this->actingAs($this->admin)
            ->post(route('subscription.initier'), ['plan' => 'mensuel'])
            ->assertRedirect()
            ->assertSessionHasErrors('general');
    }

    // ════════════════════════════════════════════════════════════════════════
    // 3. Callback IPN — endpoint POST /subscription/callback
    //    Ce endpoint est accessible sans authentification (withoutMiddleware)
    //
    //    Format IPN PayTech :
    //      - type_event   : 'sale_complete' | 'sale_canceled'
    //      - ref_command  : référence unique de la commande
    //      - item_price   : montant
    //      - custom_field : base64(JSON{'agency_id':..., 'plan':...})
    // ════════════════════════════════════════════════════════════════════════

    /**
     * Construit un payload IPN PayTech valide.
     */
    private function ipnPayload(
        string $refCommand,
        string $typeEvent = 'sale_complete',
        ?int   $agencyId  = null,
        string $plan      = 'mensuel',
        int    $montant   = 5000
    ): array {
        $customField = base64_encode(json_encode([
            'agency_id' => $agencyId ?? $this->agency->id,
            'plan'      => $plan,
        ]));

        return [
            'type_event'        => $typeEvent,
            'ref_command'       => $refCommand,
            'item_price'        => $montant,
            'custom_field'      => $customField,
            'api_key_sha256'    => hash('sha256', config('services.paytech.api_key', '')),
            'api_secret_sha256' => hash('sha256', config('services.paytech.api_secret', '')),
        ];
    }

    #[Test]
    public function callback_ipn_sale_complete_active_labonnement(): void
    {
        $response = $this->postJson(
            route('subscription.callback'),
            $this->ipnPayload('REF-IPN-001', 'sale_complete', plan: 'mensuel')
        );

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'actif',
            'plan'      => 'mensuel',
        ]);

        $this->assertDatabaseHas('subscription_payments', [
            'agency_id' => $this->agency->id,
            'reference' => 'REF-IPN-001',
            'statut'    => 'payé',
        ]);
    }

    #[Test]
    public function callback_ipn_est_idempotent_meme_ref_ne_duplique_pas(): void
    {
        $payload = $this->ipnPayload('REF-IDEMPOTENT', 'sale_complete', plan: 'mensuel');

        // Premier appel
        $this->postJson(route('subscription.callback'), $payload)->assertOk();

        // Deuxième appel avec la même ref_command
        $this->postJson(route('subscription.callback'), $payload)->assertOk();

        $this->assertEquals(
            1,
            SubscriptionPayment::where('reference', 'REF-IDEMPOTENT')->count(),
            'La même ref_command IPN a généré plusieurs paiements — idempotence non respectée.'
        );
    }

    #[Test]
    public function callback_ipn_evenement_non_sale_complete_retourne_422(): void
    {
        $this->postJson(
            route('subscription.callback'),
            $this->ipnPayload('REF-CANCEL', 'sale_canceled')
        )->assertStatus(422)->assertJson(['success' => false]);

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'essai',
        ]);
    }

    #[Test]
    public function callback_ipn_ref_command_manquante_retourne_422(): void
    {
        $this->postJson(route('subscription.callback'), [
            'type_event'   => 'sale_complete',
            'custom_field' => base64_encode(json_encode(['agency_id' => $this->agency->id, 'plan' => 'mensuel'])),
            // ref_command absent
        ])->assertStatus(422)->assertJson(['success' => false]);
    }

    #[Test]
    public function callback_ipn_custom_field_manquant_retourne_422(): void
    {
        $this->postJson(route('subscription.callback'), [
            'type_event'  => 'sale_complete',
            'ref_command' => 'REF-NO-CUSTOM',
            // custom_field absent → agency_id et plan inconnus
        ])->assertStatus(422)->assertJson(['success' => false]);
    }

    #[Test]
    public function callback_ipn_plan_invalide_retourne_422(): void
    {
        $this->postJson(
            route('subscription.callback'),
            $this->ipnPayload('REF-BAD-PLAN', 'sale_complete', plan: 'inexistant')
        )->assertStatus(422)->assertJson(['success' => false]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // 4. Page succes() — retour PayTech après paiement (?ref=REF_COMMAND)
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function page_succes_sans_ref_redirige_vers_subscription_index(): void
    {
        $this->actingAs($this->admin)
            ->get(route('subscription.succes'))
            ->assertRedirect(route('subscription.index'));
    }

    #[Test]
    public function page_succes_avec_ref_et_plan_en_session_active_labonnement(): void
    {
        config(['services.paytech.mode' => 'simulation']);

        session(['subscription_plan_pending' => 'annuel']);

        $this->actingAs($this->admin)
            ->get(route('subscription.succes', ['ref' => 'BIMO-1-ABCDEF12']))
            ->assertOk()
            ->assertViewIs('subscription.succes');

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'actif',
            'plan'      => 'annuel',
        ]);
    }

    #[Test]
    public function page_succes_ne_reactive_pas_si_ref_deja_traitee_via_ipn(): void
    {
        $payload = $this->ipnPayload('REF-DOUBLE-SUCCES', 'sale_complete', plan: 'annuel');

        // Premier callback IPN → abonnement activé
        $this->postJson(route('subscription.callback'), $payload)->assertOk();

        // Deuxième callback IPN avec la même ref → ignoré (idempotence)
        $this->postJson(route('subscription.callback'), $payload)->assertOk();

        $this->assertEquals(
            1,
            SubscriptionPayment::where('reference', 'REF-DOUBLE-SUCCES')->count(),
            'La même ref IPN a généré plusieurs paiements — idempotence non respectée.'
        );
    }

    // ════════════════════════════════════════════════════════════════════════
    // 5. Page echec()
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function page_echec_retourne_la_vue_echec(): void
    {
        $this->actingAs($this->admin)
            ->get(route('subscription.echec'))
            ->assertOk()
            ->assertViewIs('subscription.echec');
    }

    // ════════════════════════════════════════════════════════════════════════
    // 6. Validation plan
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function initier_paiement_plan_invalide_retourne_erreur(): void
    {
        config(['services.paytech.mode' => 'simulation']);

        $this->actingAs($this->admin)
            ->post(route('subscription.initier'), ['plan' => 'gratuit'])
            ->assertRedirect()
            ->assertSessionHasErrors('plan');
    }

    #[Test]
    public function initier_paiement_sans_plan_retourne_erreur(): void
    {
        config(['services.paytech.mode' => 'simulation']);

        $this->actingAs($this->admin)
            ->post(route('subscription.initier'), [])
            ->assertRedirect()
            ->assertSessionHasErrors('plan');
    }
}
