<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * CheckSubscriptionsTest — Tests de la commande app:check-subscriptions.
 *
 * Couvre :
 *  - Essai expiré → statut='expiré', agency.actif=false
 *  - Abonnement actif expiré → statut='expiré', agency.actif=false
 *  - Essai encore valide → ignoré
 *  - Abonnement actif encore valide → ignoré
 *  - Aucune subscription expirée → rien à faire
 */
class CheckSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    // ════════════════════════════════════════════════════════════════════════
    // Essai expiré
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function essai_expire_est_marque_expire_et_agence_desactivee(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'         => $agency->id,
            'statut'            => 'essai',
            'date_debut_essai'  => now()->subDays(40),
            'date_fin_essai'    => now()->subDays(10), // expiré il y a 10 jours
        ]);

        $this->artisan('app:check-subscriptions')->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'expiré',
        ]);

        $this->assertDatabaseHas('agencies', [
            'id'    => $agency->id,
            'actif' => false,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Abonnement actif expiré
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function abonnement_actif_expire_est_marque_expire_et_agence_desactivee(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'mensuel',
            'date_debut_abonnement' => now()->subMonths(2),
            'date_fin_abonnement'   => now()->subDays(5), // expiré il y a 5 jours
        ]);

        $this->artisan('app:check-subscriptions')->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'expiré',
        ]);

        $this->assertDatabaseHas('agencies', [
            'id'    => $agency->id,
            'actif' => false,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Essai encore valide — ignoré
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function essai_encore_valide_nest_pas_traite(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'         => $agency->id,
            'statut'            => 'essai',
            'date_debut_essai'  => now()->subDays(5),
            'date_fin_essai'    => now()->addDays(25), // expire dans 25 jours
        ]);

        $this->artisan('app:check-subscriptions')->assertSuccessful();

        // Statut doit rester 'essai'
        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'essai',
        ]);

        // Agence doit rester active
        $this->assertDatabaseHas('agencies', [
            'id'    => $agency->id,
            'actif' => true,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Abonnement actif encore valide — ignoré
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function abonnement_actif_encore_valide_nest_pas_traite(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addMonths(11), // encore 11 mois
        ]);

        $this->artisan('app:check-subscriptions')->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'actif',
        ]);

        $this->assertDatabaseHas('agencies', [
            'id'    => $agency->id,
            'actif' => true,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Aucune subscription expirée
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function aucune_subscription_expiree_retourne_succes(): void
    {
        $this->artisan('app:check-subscriptions')->assertSuccessful();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Plusieurs agences — seules les expirées sont traitées
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function seules_les_agences_expirees_sont_desactivees(): void
    {
        $agenceExpiree = Agency::factory()->create(['actif' => true]);
        $agenceValide  = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'         => $agenceExpiree->id,
            'statut'            => 'essai',
            'date_fin_essai'    => now()->subDay(),
        ]);

        Subscription::factory()->create([
            'agency_id'         => $agenceValide->id,
            'statut'            => 'essai',
            'date_fin_essai'    => now()->addDays(15),
        ]);

        $this->artisan('app:check-subscriptions')->assertSuccessful();

        $this->assertDatabaseHas('agencies', ['id' => $agenceExpiree->id, 'actif' => false]);
        $this->assertDatabaseHas('agencies', ['id' => $agenceValide->id,  'actif' => true]);
    }
}
