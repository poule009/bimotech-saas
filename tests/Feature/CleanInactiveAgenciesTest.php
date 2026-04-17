<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * CleanInactiveAgenciesTest — Tests de la commande agencies:clean-inactive.
 *
 * Critères de suppression :
 *  - subscription.statut = 'expiré'
 *  - subscription.plan IS NULL (jamais souscrit à un plan payant)
 *  - date_fin_essai < now() - 60 jours
 *  - Aucun paiement enregistré (subscription_payments vide)
 *
 * Couvre :
 *  - Agence éligible → supprimée
 *  - Agence expirée < 60 jours → ignorée
 *  - Agence expirée mais avec plan payant → ignorée
 *  - Agence expirée mais avec paiements → ignorée
 *  - Aucune agence éligible → rien à faire
 */
class CleanInactiveAgenciesTest extends TestCase
{
    use RefreshDatabase;

    // ════════════════════════════════════════════════════════════════════════
    // Agence éligible → supprimée
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function agence_essai_expiree_depuis_plus_60_jours_sans_paiement_est_supprimee(): void
    {
        $agency = Agency::factory()->create(['actif' => false]);

        Subscription::factory()->create([
            'agency_id'         => $agency->id,
            'statut'            => 'expiré',
            'plan'              => null,
            'date_debut_essai'  => now()->subDays(100),
            'date_fin_essai'    => now()->subDays(70), // expiré il y a 70 jours
        ]);

        $this->artisan('agencies:clean-inactive')->assertSuccessful();

        $this->assertDatabaseMissing('agencies', ['id' => $agency->id]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Essai expiré depuis moins de 60 jours → ignoré
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function agence_expiree_depuis_moins_de_60_jours_nest_pas_supprimee(): void
    {
        $agency = Agency::factory()->create(['actif' => false]);

        Subscription::factory()->create([
            'agency_id'        => $agency->id,
            'statut'           => 'expiré',
            'plan'             => null,
            'date_debut_essai' => now()->subDays(50),
            'date_fin_essai'   => now()->subDays(20), // seulement 20 jours
        ]);

        $this->artisan('agencies:clean-inactive')->assertSuccessful();

        $this->assertDatabaseHas('agencies', ['id' => $agency->id]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Agence avec plan payant → ignorée
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function agence_avec_plan_payant_nest_pas_supprimee(): void
    {
        $agency = Agency::factory()->create(['actif' => false]);

        Subscription::factory()->create([
            'agency_id'        => $agency->id,
            'statut'           => 'expiré',
            'plan'             => 'mensuel',    // a eu un plan payant
            'date_debut_essai' => now()->subDays(120),
            'date_fin_essai'   => now()->subDays(90),
        ]);

        $this->artisan('agencies:clean-inactive')->assertSuccessful();

        $this->assertDatabaseHas('agencies', ['id' => $agency->id]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Agence avec paiements → ignorée (sécurité supplémentaire)
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function agence_avec_paiements_subscription_nest_pas_supprimee(): void
    {
        $agency = Agency::factory()->create(['actif' => false]);

        $subscription = Subscription::factory()->create([
            'agency_id'        => $agency->id,
            'statut'           => 'expiré',
            'plan'             => null,
            'date_debut_essai' => now()->subDays(120),
            'date_fin_essai'   => now()->subDays(90),
        ]);

        // Paiement d'abonnement enregistré
        SubscriptionPayment::create([
            'subscription_id' => $subscription->id,
            'agency_id'       => $agency->id,
            'plan'            => 'mensuel',
            'montant'         => 25000,
            'statut'          => 'payé',
            'methode'         => 'paytech',
            'periode_debut'   => now()->subMonths(3),
            'periode_fin'     => now()->subMonths(2),
        ]);

        $this->artisan('agencies:clean-inactive')->assertSuccessful();

        $this->assertDatabaseHas('agencies', ['id' => $agency->id]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Aucune agence éligible → rien à faire
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function aucune_agence_eligible_retourne_succes(): void
    {
        $this->artisan('agencies:clean-inactive')->assertSuccessful();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Seules les agences éligibles sont supprimées, les autres restent
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function seule_lagence_eligible_est_supprimee(): void
    {
        $agenceEligible = Agency::factory()->create(['actif' => false]);
        $agenceProtegee = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'        => $agenceEligible->id,
            'statut'           => 'expiré',
            'plan'             => null,
            'date_fin_essai'   => now()->subDays(65),
        ]);

        Subscription::factory()->create([
            'agency_id'             => $agenceProtegee->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addMonths(11),
        ]);

        $this->artisan('agencies:clean-inactive')->assertSuccessful();

        $this->assertDatabaseMissing('agencies', ['id' => $agenceEligible->id]);
        $this->assertDatabaseHas('agencies',    ['id' => $agenceProtegee->id]);
    }
}
