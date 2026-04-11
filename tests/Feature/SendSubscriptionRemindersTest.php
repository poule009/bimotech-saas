<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionReminderNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * SendSubscriptionRemindersTest — Tests de la commande subscriptions:reminders.
 *
 * Couvre :
 *  - Rappel J-7 envoyé + flag rappel_7j_envoye mis à true
 *  - Rappel J-1 envoyé + flag rappel_1j_envoye mis à true
 *  - Abonnement expiré → marquerExpire() + SubscriptionExpiredNotification
 *  - Idempotence : rappel déjà envoyé non renvoyé
 *  - Sans admin dans l'agence → ignoré
 */
class SendSubscriptionRemindersTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User   $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->agency = Agency::factory()->create(['actif' => true]);

        $this->admin = User::factory()->createOne([
            'role'      => 'admin',
            'agency_id' => $this->agency->id,
            'email'     => 'admin@agence.sn',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Rappel J-7
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function rappel_j7_est_envoye_quand_il_reste_7_jours(): void
    {
        Carbon::setTestNow(Carbon::now()->startOfDay());

        Subscription::factory()->create([
            'agency_id'             => $this->agency->id,
            'statut'                => 'actif',
            'plan'                  => 'mensuel',
            'date_debut_abonnement' => now()->subDays(23),
            'date_fin_abonnement'   => now()->addDays(7)->startOfDay(),
            'rappel_7j_envoye'      => false,
            'rappel_1j_envoye'      => false,
        ]);

        $this->artisan('subscriptions:reminders')->assertSuccessful();

        Notification::assertSentTo($this->admin, SubscriptionReminderNotification::class);

        $this->assertDatabaseHas('subscriptions', [
            'agency_id'        => $this->agency->id,
            'rappel_7j_envoye' => true,
        ]);
    }

    #[Test]
    public function rappel_j7_nest_pas_renvoye_si_deja_envoye(): void
    {
        Carbon::setTestNow(Carbon::now()->startOfDay());

        Subscription::factory()->create([
            'agency_id'             => $this->agency->id,
            'statut'                => 'actif',
            'plan'                  => 'mensuel',
            'date_debut_abonnement' => now()->subDays(23),
            'date_fin_abonnement'   => now()->addDays(7)->startOfDay(),
            'rappel_7j_envoye'      => true,  // déjà envoyé
            'rappel_1j_envoye'      => false,
        ]);

        $this->artisan('subscriptions:reminders')->assertSuccessful();

        Notification::assertNotSentTo($this->admin, SubscriptionReminderNotification::class);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Rappel J-1
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function rappel_j1_est_envoye_quand_il_reste_1_jour(): void
    {
        Carbon::setTestNow(Carbon::now()->startOfDay());

        Subscription::factory()->create([
            'agency_id'             => $this->agency->id,
            'statut'                => 'actif',
            'plan'                  => 'mensuel',
            'date_debut_abonnement' => now()->subDays(29),
            'date_fin_abonnement'   => now()->addDay()->startOfDay(),
            'rappel_7j_envoye'      => true,
            'rappel_1j_envoye'      => false,
        ]);

        $this->artisan('subscriptions:reminders')->assertSuccessful();

        Notification::assertSentTo($this->admin, SubscriptionReminderNotification::class);

        $this->assertDatabaseHas('subscriptions', [
            'agency_id'        => $this->agency->id,
            'rappel_1j_envoye' => true,
        ]);
    }

    #[Test]
    public function rappel_j1_nest_pas_renvoye_si_deja_envoye(): void
    {
        Carbon::setTestNow(Carbon::now()->startOfDay());

        Subscription::factory()->create([
            'agency_id'             => $this->agency->id,
            'statut'                => 'actif',
            'plan'                  => 'mensuel',
            'date_debut_abonnement' => now()->subDays(29),
            'date_fin_abonnement'   => now()->addDay()->startOfDay(),
            'rappel_7j_envoye'      => true,
            'rappel_1j_envoye'      => true,  // déjà envoyé
        ]);

        $this->artisan('subscriptions:reminders')->assertSuccessful();

        Notification::assertNotSentTo($this->admin, SubscriptionReminderNotification::class);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Expiration → marquerExpire + SubscriptionExpiredNotification
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function abonnement_expire_declenche_notification_expiration(): void
    {
        Carbon::setTestNow(Carbon::now()->startOfDay());

        Subscription::factory()->create([
            'agency_id'             => $this->agency->id,
            'statut'                => 'actif',
            'plan'                  => 'mensuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->subDay()->startOfDay(), // expiré hier
            'rappel_7j_envoye'      => true,
            'rappel_1j_envoye'      => true,
        ]);

        $this->artisan('subscriptions:reminders')->assertSuccessful();

        Notification::assertSentTo($this->admin, SubscriptionExpiredNotification::class);

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'expiré',
        ]);
    }

    #[Test]
    public function essai_expire_declenche_notification_expiration(): void
    {
        Carbon::setTestNow(Carbon::now()->startOfDay());

        Subscription::factory()->create([
            'agency_id'        => $this->agency->id,
            'statut'           => 'essai',
            'date_debut_essai' => now()->subDays(31),
            'date_fin_essai'   => now()->subDay()->startOfDay(), // expiré hier
        ]);

        $this->artisan('subscriptions:reminders')->assertSuccessful();

        Notification::assertSentTo($this->admin, SubscriptionExpiredNotification::class);

        $this->assertDatabaseHas('subscriptions', [
            'agency_id' => $this->agency->id,
            'statut'    => 'expiré',
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Pas d'admin → ignoré sans plantage
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function agence_sans_admin_est_ignoree_sans_plantage(): void
    {
        Carbon::setTestNow(Carbon::now()->startOfDay());

        $agenceSansAdmin = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $agenceSansAdmin->id,
            'statut'                => 'actif',
            'plan'                  => 'mensuel',
            'date_fin_abonnement'   => now()->addDays(7)->startOfDay(),
            'rappel_7j_envoye'      => false,
        ]);

        // Aucun user admin dans $agenceSansAdmin
        $this->artisan('subscriptions:reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }
}
