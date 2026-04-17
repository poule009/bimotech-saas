<?php

namespace Tests\Unit;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PaymentServiceTest — Tests unitaires du service de paiement PayTech.
 *
 * Documentation API PayTech : https://docs.intech.sn/doc_paytech.php
 *
 * Trois modes à tester :
 *  - simulation : paiement activé directement, pas d'appel HTTP
 *  - test/prod  : appels HTTP mockés via Http::fake()
 */
class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    // ── Helper ────────────────────────────────────────────────────────────

    private function agenceAvecAbonnementEssai(): Agency
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

    /**
     * Construit un payload IPN PayTech.
     * custom_field = base64(JSON) comme l'envoie PayTech.
     */
    private function ipnPayload(Agency $agency, string $refCommand, string $plan = 'mensuel', string $typeEvent = 'sale_complete'): array
    {
        return [
            'type_event'   => $typeEvent,
            'ref_command'  => $refCommand,
            'item_price'   => Subscription::TARIFS[$plan] ?? 5000,
            'custom_field' => base64_encode(json_encode([
                'agency_id' => $agency->id,
                'plan'      => $plan,
            ])),
        ];
    }

    // ═══════════════════════════════════════════════════════════════════════
    // Mode simulation
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function simulation_active_abonnement_directement_sans_appel_http(): void
    {
        Http::fake();
        Config::set('services.paytech.mode', 'simulation');

        $agency  = $this->agenceAvecAbonnementEssai();
        $service = new PaymentService();

        $result = $service->initierPaiement($agency, 'mensuel');

        $this->assertTrue($result['success']);
        $this->assertEquals('simulation', $result['mode']);
        $this->assertEquals('actif', $agency->fresh()->subscription->statut);

        Http::assertNothingSent();
    }

    #[Test]
    public function simulation_cree_abonnement_si_inexistant(): void
    {
        Config::set('services.paytech.mode', 'simulation');

        $agency  = Agency::factory()->create(['actif' => true]);
        $service = new PaymentService();

        $result = $service->initierPaiement($agency, 'annuel');

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'actif',
        ]);
    }

    #[Test]
    public function plan_invalide_retourne_erreur(): void
    {
        Config::set('services.paytech.mode', 'simulation');
        $agency  = $this->agenceAvecAbonnementEssai();
        $service = new PaymentService();

        $result = $service->initierPaiement($agency, 'plan_inconnu');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('invalide', strtolower($result['message']));
    }

    #[Test]
    public function tous_les_plans_valides_sont_acceptes_en_simulation(): void
    {
        Config::set('services.paytech.mode', 'simulation');
        $service = new PaymentService();

        foreach (array_keys(Subscription::TARIFS) as $plan) {
            $agency = $this->agenceAvecAbonnementEssai();
            $result = $service->initierPaiement($agency, $plan);
            $this->assertTrue($result['success'], "Le plan '{$plan}' devrait être accepté");
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // Mode sandbox — appels API mockés
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function mode_test_cree_facture_et_retourne_redirect_url(): void
    {
        Config::set('services.paytech.mode', 'test');

        Http::fake([
            'paytech.sn/api/payment/request-payment' => Http::response([
                'success'      => 1,
                'token'        => 'TEST-TOKEN-ABC',
                'redirect_url' => 'https://paytech.sn/payment/checkout/TEST-TOKEN-ABC',
            ], 200),
        ]);

        $agency  = $this->agenceAvecAbonnementEssai();
        $service = new PaymentService();

        $result = $service->initierPaiement($agency, 'mensuel');

        $this->assertTrue($result['success']);
        $this->assertEquals('https://paytech.sn/payment/checkout/TEST-TOKEN-ABC', $result['redirect_url']);
        Http::assertSentCount(1);
    }

    #[Test]
    public function mode_test_retourne_erreur_si_api_refuse(): void
    {
        Config::set('services.paytech.mode', 'test');

        Http::fake([
            'paytech.sn/api/payment/request-payment' => Http::response([
                'success' => 0,
                'message' => 'Clés API invalides',
            ], 200),
        ]);

        $agency  = $this->agenceAvecAbonnementEssai();
        $service = new PaymentService();

        $result = $service->initierPaiement($agency, 'mensuel');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('PayTech', $result['message']);
    }

    #[Test]
    public function mode_test_retourne_erreur_si_api_inaccessible(): void
    {
        Config::set('services.paytech.mode', 'test');

        Http::fake([
            'paytech.sn/*' => Http::response([], 500),
        ]);

        $agency  = $this->agenceAvecAbonnementEssai();
        $service = new PaymentService();

        $result = $service->initierPaiement($agency, 'mensuel');

        $this->assertFalse($result['success']);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // verifierStatutFacture()
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function verifier_statut_retourne_completed_en_simulation(): void
    {
        Config::set('services.paytech.mode', 'simulation');
        $service = new PaymentService();

        $statut = $service->verifierStatutFacture('token-bidon');

        $this->assertNotNull($statut);
        $this->assertEquals('completed', $statut['status']);
    }

    #[Test]
    public function verifier_statut_appelle_api_paytech_en_mode_test(): void
    {
        Config::set('services.paytech.mode', 'test');

        Http::fake([
            'paytech.sn/api/payment/get-status*' => Http::response([
                'success' => 1,
            ], 200),
        ]);

        $service = new PaymentService();
        $statut  = $service->verifierStatutFacture('TEST-TOKEN-123');

        $this->assertNotNull($statut);
        $this->assertEquals('completed', $statut['status']);
        Http::assertSentCount(1);
    }

    #[Test]
    public function verifier_statut_retourne_null_si_api_echoue(): void
    {
        Config::set('services.paytech.mode', 'test');

        Http::fake([
            'paytech.sn/*' => Http::response([], 500),
        ]);

        $service = new PaymentService();
        $statut  = $service->verifierStatutFacture('token-invalide');

        $this->assertNull($statut);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // traiterCallbackIPN()
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function callback_ipn_sans_ref_command_retourne_echec(): void
    {
        $service = new PaymentService();

        $result = $service->traiterCallbackIPN([
            'type_event' => 'sale_complete',
            // ref_command absent
        ]);

        $this->assertFalse($result['success']);
    }

    #[Test]
    public function callback_ipn_avec_type_event_non_sale_complete_retourne_echec(): void
    {
        $service = new PaymentService();
        $agency  = $this->agenceAvecAbonnementEssai();

        $result = $service->traiterCallbackIPN(
            $this->ipnPayload($agency, 'REF-CANCEL', typeEvent: 'sale_canceled')
        );

        $this->assertFalse($result['success']);
    }

    #[Test]
    public function callback_ipn_sans_agency_id_retourne_echec(): void
    {
        $service = new PaymentService();

        $result = $service->traiterCallbackIPN([
            'type_event'   => 'sale_complete',
            'ref_command'  => 'REF-NO-AGENCY',
            'custom_field' => base64_encode(json_encode(['plan' => 'mensuel'])),
            // agency_id manquant dans custom_field
        ]);

        $this->assertFalse($result['success']);
    }

    #[Test]
    public function callback_ipn_valide_active_labonnement(): void
    {
        $agency  = $this->agenceAvecAbonnementEssai();
        $service = new PaymentService();

        $result = $service->traiterCallbackIPN(
            $this->ipnPayload($agency, 'REF-UNIQUE-XYZ', 'mensuel')
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('actif', $agency->fresh()->subscription->statut);
    }

    #[Test]
    public function callback_ipn_meme_ref_deux_fois_est_idempotent(): void
    {
        $agency  = $this->agenceAvecAbonnementEssai();
        $service = new PaymentService();

        $payload = $this->ipnPayload($agency, 'REF-DOUBLON-789', 'mensuel');

        $service->traiterCallbackIPN($payload);
        $result = $service->traiterCallbackIPN($payload); // Deuxième appel

        $this->assertTrue($result['success']);

        $this->assertEquals(
            1,
            SubscriptionPayment::where('reference', 'REF-DOUBLON-789')->count()
        );
    }

    // ═══════════════════════════════════════════════════════════════════════
    // verifierSignatureIPN()
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function verification_signature_hmac_valide(): void
    {
        Config::set('services.paytech.api_key',    'test-api-key');
        Config::set('services.paytech.api_secret', 'test-api-secret');

        $service    = new PaymentService();
        $amount     = 5000;
        $refCommand = 'REF-HMAC-TEST';
        $message    = "{$amount}|{$refCommand}|test-api-key";
        $hmac       = hash_hmac('sha256', $message, 'test-api-secret');

        $payload = [
            'item_price'   => $amount,
            'ref_command'  => $refCommand,
            'hmac_compute' => $hmac,
        ];

        $this->assertTrue($service->verifierSignatureIPN($payload));
    }

    #[Test]
    public function verification_signature_hmac_invalide_retourne_false(): void
    {
        Config::set('services.paytech.api_key',    'test-api-key');
        Config::set('services.paytech.api_secret', 'test-api-secret');

        $service = new PaymentService();

        $payload = [
            'item_price'   => 5000,
            'ref_command'  => 'REF-HMAC-TEST',
            'hmac_compute' => 'signature-falsifiee',
        ];

        $this->assertFalse($service->verifierSignatureIPN($payload));
    }

    #[Test]
    public function verification_signature_sha256_valide(): void
    {
        Config::set('services.paytech.api_key',    'test-api-key');
        Config::set('services.paytech.api_secret', 'test-api-secret');

        $service = new PaymentService();

        $payload = [
            'api_key_sha256'    => hash('sha256', 'test-api-key'),
            'api_secret_sha256' => hash('sha256', 'test-api-secret'),
        ];

        $this->assertTrue($service->verifierSignatureIPN($payload));
    }
}
