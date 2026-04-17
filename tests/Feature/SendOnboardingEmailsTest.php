<?php

namespace Tests\Feature;

use App\Console\Commands\SendOnboardingEmails;
use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\OnboardingNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * SendOnboardingEmailsTest — Commande artisan `onboarding:emails`.
 *
 * Vérifie les trois jalons d'onboarding (J+1, J+7, J+25) :
 *  - Notification envoyée à la bonne date
 *  - Flag mis à true après envoi (idempotence : pas de doublon)
 *  - Pas d'envoi avant la date ou si flag déjà coché
 */
class SendOnboardingEmailsTest extends TestCase
{
    use RefreshDatabase;

    // ── Helper ────────────────────────────────────────────────────────────

    private function creerAgenceEnEssai(int $joursDepuis): array
    {
        $agency = Agency::factory()->create(['actif' => true]);

        $sub = Subscription::factory()->create([
            'agency_id'        => $agency->id,
            'statut'           => 'essai',
            'date_debut_essai' => now()->subDays($joursDepuis)->startOfDay(),
            'date_fin_essai'   => now()->addDays(30 - $joursDepuis)->startOfDay(),
            'onboarding_j1_envoye'  => false,
            'onboarding_j7_envoye'  => false,
            'onboarding_j25_envoye' => false,
        ]);

        $admin = User::factory()->create([
            'role'      => 'admin',
            'agency_id' => $agency->id,
        ]);

        return [$agency, $sub, $admin];
    }

    // ── Tests J+1 ─────────────────────────────────────────────────────────

    #[Test]
    public function envoie_notification_j1_apres_un_jour()
    {
        Notification::fake();
        [$agency, $sub, $admin] = $this->creerAgenceEnEssai(joursDepuis: 1);

        $this->artisan('onboarding:emails')->assertExitCode(0);

        Notification::assertSentTo($admin, OnboardingNotification::class, function ($n) {
            return $n->step === 1;
        });

        $this->assertTrue($sub->fresh()->onboarding_j1_envoye);
    }

    #[Test]
    public function ne_renvoie_pas_j1_si_deja_envoye()
    {
        Notification::fake();
        [$agency, $sub, $admin] = $this->creerAgenceEnEssai(joursDepuis: 3);
        $sub->update(['onboarding_j1_envoye' => true]);

        $this->artisan('onboarding:emails')->assertExitCode(0);

        Notification::assertNotSentTo($admin, function (OnboardingNotification $n) {
            return $n->step === 1;
        });
    }

    #[Test]
    public function ne_envoie_pas_j1_avant_un_jour()
    {
        Notification::fake();
        [$agency, $sub, $admin] = $this->creerAgenceEnEssai(joursDepuis: 0);

        $this->artisan('onboarding:emails')->assertExitCode(0);

        Notification::assertNotSentTo($admin, OnboardingNotification::class);
    }

    // ── Tests J+7 ─────────────────────────────────────────────────────────

    #[Test]
    public function envoie_notification_j7_apres_sept_jours()
    {
        Notification::fake();
        [$agency, $sub, $admin] = $this->creerAgenceEnEssai(joursDepuis: 7);
        $sub->update(['onboarding_j1_envoye' => true]); // J+1 déjà envoyé

        $this->artisan('onboarding:emails')->assertExitCode(0);

        Notification::assertSentTo($admin, OnboardingNotification::class, function ($n) {
            return $n->step === 7;
        });

        $this->assertTrue($sub->fresh()->onboarding_j7_envoye);
    }

    #[Test]
    public function ne_renvoie_pas_j7_si_deja_envoye()
    {
        Notification::fake();
        [$agency, $sub, $admin] = $this->creerAgenceEnEssai(joursDepuis: 10);
        $sub->update([
            'onboarding_j1_envoye' => true,
            'onboarding_j7_envoye' => true,
        ]);

        $this->artisan('onboarding:emails')->assertExitCode(0);

        Notification::assertNotSentTo($admin, function (OnboardingNotification $n) {
            return $n->step === 7;
        });
    }

    // ── Tests J+25 ────────────────────────────────────────────────────────

    #[Test]
    public function envoie_notification_j25_apres_vingt_cinq_jours()
    {
        Notification::fake();
        [$agency, $sub, $admin] = $this->creerAgenceEnEssai(joursDepuis: 25);
        $sub->update([
            'onboarding_j1_envoye' => true,
            'onboarding_j7_envoye' => true,
        ]);

        $this->artisan('onboarding:emails')->assertExitCode(0);

        Notification::assertSentTo($admin, OnboardingNotification::class, function ($n) {
            return $n->step === 25;
        });

        $this->assertTrue($sub->fresh()->onboarding_j25_envoye);
    }

    #[Test]
    public function ne_renvoie_pas_j25_si_deja_envoye()
    {
        Notification::fake();
        [$agency, $sub, $admin] = $this->creerAgenceEnEssai(joursDepuis: 28);
        $sub->update([
            'onboarding_j1_envoye'  => true,
            'onboarding_j7_envoye'  => true,
            'onboarding_j25_envoye' => true,
        ]);

        $this->artisan('onboarding:emails')->assertExitCode(0);

        Notification::assertNothingSent();
    }

    // ── Tests périmètre ───────────────────────────────────────────────────

    #[Test]
    public function commande_ignore_les_abonnements_hors_essai()
    {
        Notification::fake();

        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'mensuel',
            'date_debut_abonnement' => now()->subDays(10),
            'date_fin_abonnement'   => now()->addMonth(),
        ]);
        $admin = User::factory()->create(['role' => 'admin', 'agency_id' => $agency->id]);

        $this->artisan('onboarding:emails')->assertExitCode(0);

        Notification::assertNotSentTo($admin, OnboardingNotification::class);
    }

    #[Test]
    public function commande_ignore_essai_sans_admin()
    {
        Notification::fake();

        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'        => $agency->id,
            'statut'           => 'essai',
            'date_debut_essai' => now()->subDays(2),
            'date_fin_essai'   => now()->addDays(28),
        ]);
        // Aucun utilisateur admin créé

        $this->artisan('onboarding:emails')->assertExitCode(0);

        // Pas d'exception — la commande s'exécute sans crash
        Notification::assertNothingSent();
    }
}
