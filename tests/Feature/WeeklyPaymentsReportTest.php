<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\WeeklyPaymentsSummaryNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * WeeklyPaymentsReportTest — Tests de la commande app:weekly-payments-report.
 *
 * Couvre :
 *  - Rapport envoyé au superadmin avec les bonnes données
 *  - Paiements de la semaine précédente uniquement pris en compte
 *  - Pas de superadmin → commande réussit sans envoyer
 *  - Aucun paiement la semaine passée → rapport à zéro envoyé quand même
 */
class WeeklyPaymentsReportTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->superAdmin = User::factory()->createOne([
            'role'  => 'superadmin',
            'email' => 'superadmin@bimotech.sn',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helper : crée un paiement minimal
    // ────────────────────────────────────────────────────────────────────────

    private function creerPaiement(string $datePaiement, float $montant = 100_000): Paiement
    {
        $agency    = Agency::factory()->create(['actif' => true]);
        $proprio   = User::factory()->createOne(['role' => 'proprietaire', 'agency_id' => $agency->id]);
        $locataire = User::factory()->createOne(['role' => 'locataire',    'agency_id' => $agency->id]);

        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'mensuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addMonth(),
        ]);

        return Paiement::factory()->create([
            'agency_id'       => $agency->id,
            'statut'          => 'valide',
            'date_paiement'   => $datePaiement,
            'montant_encaisse' => $montant,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Rapport envoyé au superadmin
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function rapport_est_envoye_au_superadmin(): void
    {
        // On est lundi, semaine passée = lundi dernier → dimanche
        Carbon::setTestNow(Carbon::now()->startOfWeek(Carbon::MONDAY));

        // Paiement de la semaine passée
        $dateSemaineDerniere = Carbon::now()->subWeek()->addDays(2)->toDateString();
        $this->creerPaiement($dateSemaineDerniere, 150_000);

        $this->artisan('app:weekly-payments-report')->assertSuccessful();

        Notification::assertSentTo($this->superAdmin, WeeklyPaymentsSummaryNotification::class);
    }

    #[Test]
    public function paiements_de_cette_semaine_ne_sont_pas_inclus(): void
    {
        // On est mercredi de cette semaine
        Carbon::setTestNow(Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(2));

        // Paiement d'aujourd'hui (cette semaine) → ne doit pas être dans le rapport
        $this->creerPaiement(now()->toDateString(), 200_000);

        $this->artisan('app:weekly-payments-report')->assertSuccessful();

        // Le rapport est quand même envoyé, mais sans ce paiement
        Notification::assertSentTo(
            $this->superAdmin,
            WeeklyPaymentsSummaryNotification::class,
            function (WeeklyPaymentsSummaryNotification $notification) {
                return $notification->summary['total_paiements'] === 0;
            }
        );
    }

    // ════════════════════════════════════════════════════════════════════════
    // Pas de superadmin → commande réussit sans envoyer
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function sans_superadmin_commande_reussit_sans_envoyer(): void
    {
        // Supprimer le superadmin créé dans setUp
        $this->superAdmin->delete();

        $this->artisan('app:weekly-payments-report')->assertSuccessful();

        Notification::assertNothingSent();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Aucun paiement la semaine passée → rapport à zéro
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function sans_paiement_la_semaine_passee_rapport_est_a_zero(): void
    {
        Carbon::setTestNow(Carbon::now()->startOfWeek(Carbon::MONDAY));

        $this->artisan('app:weekly-payments-report')->assertSuccessful();

        Notification::assertSentTo(
            $this->superAdmin,
            WeeklyPaymentsSummaryNotification::class,
            function (WeeklyPaymentsSummaryNotification $notification) {
                return $notification->summary['total_paiements'] === 0
                    && $notification->summary['total_montant'] === 0.0;
            }
        );
    }
}
