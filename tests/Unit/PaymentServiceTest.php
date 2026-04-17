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
 * PaymentServiceTest — Tests unitaires du service de paiement PayDunya.
 *
 * Trois modes à tester :
 *  - simulation : paiement activé directement, pas d'appel HTTP
 *  - test/live  : appels HTTP mockés via Http::fake()
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

    // ═══════════════════════════════════════════════════════════════════════
    // Mode simulation
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function simulation_active_abonnement_directement_sans_appel_http()
    {
        Http::fake(); // Aucun appel ne doit passer
        Config::set('services.paydunya.mode', 'simulation');

        $agency  = $this->agenceAvecAbonnementEssai();
        $service = new PaymentService();

        $result = $service->initierPaiement($agency, 'mensuel');

        $this->assertTrue($result['success']);
        $this->assertEquals('simulation', $result['mode']);

        // L'abonnement est passé en 'actif'
        $this->assertEquals('actif', $agency->fresh()->subscription->statut);

        Http::assertNothingSent();
    }

    #[Test]
    public function simulation_cree_abonnement_si_inexistant()
    {
        Config::set('services.paydunya.mode', 'simulation');

        $agency = Agency::factory()->create(['actif' => true]);
        // Pas de subscription existante

        $service = new PaymentService();
        $result  = $service->initierPaiement($agency, 'annuel');

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'actif',
        ]);
    }

    #[Test]
    public function plan_invalide_retourne_erreur()
    {
        Config::set('services.paydunya.mode', 'simulation');
        $agency  = $this->agenceAvecAbonnementEssai();
        $service = new PaymentService();

        $result = $service->initierPaiement($agency, 'plan_inconnu');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('invalide', strtolower($result['message']));
    }

    #[Test]
    public function tous_les_plans_valides_sont_acceptes_en_simulation()
    {
        Config::set('services.paydunya.mode', 'simulation');
        $service = new PaymentService();

        foreach (array_keys(Subscription::TARIFS) as $plan) {
            $agency = $this->agenceAvecAbonnementEssai();
            $result = $service->initierPaiement($agency, $plan);
            $this->assertTrue($result['success'], "Le plan '{$plan}' devrait être accepté");
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // verifierStatutFacture()
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function verifier_statut_retourne_completed_en_simulation()
    {
        Config::set('services.paydunya.mode', 'simulation');
        $service = new PaymentService();

        $statut = $service->verifierStatutFacture('token-bidon');

        $this->assertNotNull($statut);
        $this->assertEquals('completed', $statut['status']);
    }

    #[Test]
    public function verifier_statut_appelle_api_paydunya_en_mode_test()
    {
        Config::set('services.paydunya.mode', 'test');

        Http::fake([
            'app.paydunya.com/sandbox-api/*' => Http::response([
                'status'      => 'completed',
                'custom_data' => [],
            ], 200),
        ]);

        $service = new PaymentService();
        $statut  = $service->verifierStatutFacture('token-test-123');

        $this->assertNotNull($statut);
        $this->assertEquals('completed', $statut['status']);
        Http::assertSentCount(1);
    }

    #[Test]
    public function verifier_statut_retourne_null_si_api_echoue()
    {
        Config::set('services.paydunya.mode', 'test');

        Http::fake([
            'app.paydunya.com/*' => Http::response([], 500),
        ]);

        $service = new PaymentService();
        $statut  = $service->verifierStatutFacture('token-invalide');

        $this->assertNull($statut);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // traiterCallbackIPN()
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function callback_ipn_sans_token_retourne_echec()
    {
        $service = new PaymentService();

        $result = $service->traiterCallbackIPN([
            'data' => ['invoice' => ['status' => 'completed']],
        ]);

        $this->assertFalse($result['success']);
    }

    #[Test]
    public function callback_ipn_avec_status_non_completed_retourne_echec()
    {
        $service = new PaymentService();

        $result = $service->traiterCallbackIPN([
            'data' => ['invoice' => [
                'token'  => 'abc123',
                'status' => 'pending',
            ]],
        ]);

        $this->assertFalse($result['success']);
    }

    #[Test]
    public function callback_ipn_sans_agency_id_retourne_echec()
    {
        $service = new PaymentService();

        $result = $service->traiterCallbackIPN([
            'data' => ['invoice' => [
                'token'       => 'abc123',
                'status'      => 'completed',
                'custom_data' => ['plan' => 'mensuel'],
                // agency_id manquant
            ]],
        ]);

        $this->assertFalse($result['success']);
    }

    #[Test]
    public function callback_ipn_valide_active_labonnement()
    {
        $agency = $this->agenceAvecAbonnementEssai();
        $service = new PaymentService();

        $result = $service->traiterCallbackIPN([
            'data' => ['invoice' => [
                'token'       => 'token-unique-xyz',
                'status'      => 'completed',
                'custom_data' => [
                    'agency_id' => $agency->id,
                    'plan'      => 'mensuel',
                ],
            ]],
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('actif', $agency->fresh()->subscription->statut);
    }

    #[Test]
    public function callback_ipn_meme_token_deux_fois_est_idempotent()
    {
        $agency  = $this->agenceAvecAbonnementEssai();
        $service = new PaymentService();

        $payload = [
            'data' => ['invoice' => [
                'token'       => 'token-doublon-789',
                'status'      => 'completed',
                'custom_data' => [
                    'agency_id' => $agency->id,
                    'plan'      => 'mensuel',
                ],
            ]],
        ];

        $service->traiterCallbackIPN($payload);
        $result = $service->traiterCallbackIPN($payload); // Deuxième appel

        $this->assertTrue($result['success']);

        // Un seul SubscriptionPayment créé
        $this->assertEquals(1, SubscriptionPayment::where('reference', 'token-doublon-789')->count());
    }
}
