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
 * IMPORTANT — la commande marque `statut='expiré'` mais NE touche JAMAIS à
 * `agency.actif` : la suspension d'accès est calculée par Subscription::etatEffectif()
 * (dates) et appliquée par le middleware CheckSubscription (grâce = lecture seule,
 * suspendu = bloqué). `agency.actif=false` est réservé à une désactivation MANUELLE
 * du SuperAdmin. Le brief exige que les données d'une agence suspendue soient conservées.
 *
 * Couvre :
 *  - Essai expiré → statut='expiré', agency.actif INCHANGÉ (true)
 *  - Abonnement actif expiré → statut='expiré', agency.actif INCHANGÉ (true)
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
    public function essai_expire_est_marque_expire_sans_desactiver_lagence(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'         => $agency->id,
            'statut'            => 'essai',
            'date_debut_essai'  => now()->subDays(40),
            'date_fin_essai'    => now()->subDays(10), // expiré il y a 10 jours (hors grâce)
        ]);

        $this->artisan('app:check-subscriptions')->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'expiré',
        ]);

        // L'agence reste ACTIVE : la suspension d'accès est calculée (etatEffectif +
        // middleware), pas via actif=false. Les données sont conservées.
        $this->assertDatabaseHas('agencies', [
            'id'    => $agency->id,
            'actif' => true,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Abonnement actif expiré
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function abonnement_actif_expire_est_marque_expire_sans_desactiver_lagence(): void
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'mensuel',
            'date_debut_abonnement' => now()->subMonths(2),
            'date_fin_abonnement'   => now()->subDays(10), // expiré il y a 10 jours (hors grâce)
        ]);

        $this->artisan('app:check-subscriptions')->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $agency->id,
            'statut'    => 'expiré',
        ]);

        // L'agence reste ACTIVE (données conservées ; accès géré par etatEffectif).
        $this->assertDatabaseHas('agencies', [
            'id'    => $agency->id,
            'actif' => true,
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
    public function seules_les_subscriptions_expirees_sont_marquees(): void
    {
        $agenceExpiree = Agency::factory()->create(['actif' => true]);
        $agenceValide  = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'         => $agenceExpiree->id,
            'statut'            => 'essai',
            'date_fin_essai'    => now()->subDays(10), // hors grâce (5j)
        ]);

        Subscription::factory()->create([
            'agency_id'         => $agenceValide->id,
            'statut'            => 'essai',
            'date_fin_essai'    => now()->addDays(15),
        ]);

        $this->artisan('app:check-subscriptions')->assertSuccessful();

        // Seule l'expirée passe à 'expiré' ; la valide reste 'essai'.
        $this->assertDatabaseHas('subscriptions', ['agency_id' => $agenceExpiree->id, 'statut' => 'expiré']);
        $this->assertDatabaseHas('subscriptions', ['agency_id' => $agenceValide->id,  'statut' => 'essai']);

        // Aucune agence n'est désactivée par la commande (données conservées).
        $this->assertDatabaseHas('agencies', ['id' => $agenceExpiree->id, 'actif' => true]);
        $this->assertDatabaseHas('agencies', ['id' => $agenceValide->id,  'actif' => true]);
    }
}
