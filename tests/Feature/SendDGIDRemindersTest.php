<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\DGIDReminderNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * SendDGIDRemindersTest — Tests de la commande dgid:reminders.
 *
 * Échéances DGID (Sénégal) :
 *  - BRS  : 31 janvier  (retenue à la source)
 *  - IRPP : 30 avril    (déclaration revenus locatifs)
 *  - CFPB : 30 septembre (contribution foncière)
 *
 * Rappels envoyés : J-30 et J-7 avant chaque échéance.
 */
class SendDGIDRemindersTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User   $proprio;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $this->agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        $this->proprio = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $this->agency->id,
            'email'     => 'proprio@agence.sn',
        ]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helper : gèle le temps à J-N avant une échéance
    // ────────────────────────────────────────────────────────────────────────

    private function gelerTemps(int $mois, int $jour, int $joursAvant): void
    {
        $echeance = Carbon::create(now()->year, $mois, $jour)->startOfDay();
        Carbon::setTestNow($echeance->copy()->subDays($joursAvant));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); // reset
        parent::tearDown();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Rappels envoyés aux bons moments
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function rappel_envoye_a_j30_avant_irpp(): void
    {
        // IRPP = 30 avril → rappel J-30 = 31 mars
        $this->gelerTemps(4, 30, 30);

        $this->artisan('dgid:reminders')->assertSuccessful();

        Notification::assertSentTo(
            $this->proprio,
            DGIDReminderNotification::class
        );
    }

    #[Test]
    public function rappel_envoye_a_j7_avant_cfpb(): void
    {
        // CFPB = 30 septembre → rappel J-7 = 23 septembre
        $this->gelerTemps(9, 30, 7);

        $this->artisan('dgid:reminders')->assertSuccessful();

        Notification::assertSentTo(
            $this->proprio,
            DGIDReminderNotification::class
        );
    }

    #[Test]
    public function rappel_envoye_a_j7_avant_brs(): void
    {
        // BRS = 31 janvier → rappel J-7 = 24 janvier
        $this->gelerTemps(1, 31, 7);

        $this->artisan('dgid:reminders')->assertSuccessful();

        Notification::assertSentTo(
            $this->proprio,
            DGIDReminderNotification::class
        );
    }

    // ════════════════════════════════════════════════════════════════════════
    // Pas de rappel hors des fenêtres J-30 / J-7
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function pas_de_rappel_a_j15_avant_echeance(): void
    {
        // J-15 avant IRPP — pas un jour de rappel
        $this->gelerTemps(4, 30, 15);

        $this->artisan('dgid:reminders')->assertSuccessful();

        Notification::assertNotSentTo(
            $this->proprio,
            DGIDReminderNotification::class
        );
    }

    // ════════════════════════════════════════════════════════════════════════
    // Plusieurs propriétaires
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function rappel_envoye_a_tous_les_proprietaires_de_agences_actives(): void
    {
        // Deuxième propriétaire dans la même agence
        $proprio2 = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $this->agency->id,
            'email'     => 'proprio2@agence.sn',
        ]);

        $this->gelerTemps(4, 30, 30);

        $this->artisan('dgid:reminders')->assertSuccessful();

        Notification::assertSentTo($this->proprio, DGIDReminderNotification::class);
        Notification::assertSentTo($proprio2, DGIDReminderNotification::class);
    }

    #[Test]
    public function rappel_non_envoye_aux_proprietaires_dagence_inactive(): void
    {
        $agenceInactive = Agency::factory()->create(['actif' => false]);
        Subscription::factory()->create([
            'agency_id'        => $agenceInactive->id,
            'statut'           => 'essai',
            'date_debut_essai' => now(),
            'date_fin_essai'   => now()->addDays(30),
        ]);

        $proprioInactif = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $agenceInactive->id,
            'email'     => 'inactif@agence.sn',
        ]);

        $this->gelerTemps(4, 30, 7);

        $this->artisan('dgid:reminders')->assertSuccessful();

        // Le proprio de l'agence active reçoit le rappel
        Notification::assertSentTo($this->proprio, DGIDReminderNotification::class);

        // Le proprio de l'agence inactive ne le reçoit pas
        Notification::assertNotSentTo($proprioInactif, DGIDReminderNotification::class);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Échéances passées ignorées
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function echeance_brs_passee_est_ignoree_apres_le_31_janvier(): void
    {
        // On est le 15 février → BRS du 31 janvier est passé → pas de rappel BRS
        Carbon::setTestNow(Carbon::create(now()->year, 2, 15)->startOfDay());

        $this->artisan('dgid:reminders')->assertSuccessful();

        // Aucune notification BRS (IRPP et CFPB sont dans le futur mais pas J-30/J-7)
        // Vérifie qu'aucune notification n'est envoyée pour BRS
        Notification::assertNothingSent();
    }
}
