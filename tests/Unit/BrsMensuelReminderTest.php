<?php

namespace Tests\Unit;

use App\Models\Agency;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use App\Notifications\BrsMensuelNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BrsMensuelReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Si on est le 10 juin 2026, le mois concerné doit être mai 2026 (moisConcerne = 5).
     */
    public function test_calcule_correctement_le_mois_precedent(): void
    {
        Carbon::setTestNow('2026-06-10 09:00:00');
        Notification::fake();

        [$agency, $admin] = $this->creerAgenceAdmin();
        $this->creerPaiementBRS($agency->id, '2026-05-01', 10000);

        $this->artisan('brs:mensuel-reminder')->assertSuccessful();

        Notification::assertSentTo(
            $admin,
            BrsMensuelNotification::class,
            fn(BrsMensuelNotification $n) => $n->moisConcerne === 5
                                          && $n->anneeConcerne === 2026,
        );
    }

    /**
     * Si on est le 16 juin 2026 (après le 15), aucun rappel ne doit être envoyé.
     */
    public function test_ne_senvoie_pas_apres_le_15_du_mois(): void
    {
        Carbon::setTestNow('2026-06-16 09:00:00');
        Notification::fake();

        [$agency] = $this->creerAgenceAdmin();
        $this->creerPaiementBRS($agency->id, '2026-05-01', 10000);

        $this->artisan('brs:mensuel-reminder')->assertSuccessful();

        Notification::assertNothingSent();
    }

    /**
     * Aucune notification si tous les paiements du mois précédent ont brs_amount = 0.
     */
    public function test_ne_senvoie_pas_si_aucun_brs_le_mois_precedent(): void
    {
        Carbon::setTestNow('2026-06-10 09:00:00');
        Notification::fake();

        [$agency] = $this->creerAgenceAdmin();

        $contrat = Contrat::factory()->create(['agency_id' => $agency->id]);
        Paiement::factory()->create([
            'agency_id'  => $agency->id,
            'contrat_id' => $contrat->id,
            'brs_amount' => 0,
            'statut'     => 'valide',
            'periode'    => '2026-05-01',
        ]);

        $this->artisan('brs:mensuel-reminder')->assertSuccessful();

        Notification::assertNothingSent();
    }

    /**
     * Trois paiements de 5 000, 8 000 et 12 000 FCFA pour la même agence
     * → totalBrsDu = 25 000 FCFA dans la notification.
     */
    public function test_agrege_correctement_le_total_brs_par_agence(): void
    {
        Carbon::setTestNow('2026-06-10 09:00:00');
        Notification::fake();

        [$agency, $admin] = $this->creerAgenceAdmin();
        $this->creerPaiementBRS($agency->id, '2026-05-01', 5000);
        $this->creerPaiementBRS($agency->id, '2026-05-01', 8000);
        $this->creerPaiementBRS($agency->id, '2026-05-01', 12000);

        $this->artisan('brs:mensuel-reminder')->assertSuccessful();

        Notification::assertSentTo(
            $admin,
            BrsMensuelNotification::class,
            fn(BrsMensuelNotification $n) => $n->totalBrsDu == 25000.0,
        );
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Crée une agence active et son utilisateur admin. */
    private function creerAgenceAdmin(): array
    {
        $agency = Agency::factory()->create(['actif' => true]);
        $admin  = User::factory()->create([
            'agency_id' => $agency->id,
            'role'      => 'admin',
            'email'     => "admin-{$agency->id}@test.com",
        ]);
        return [$agency, $admin];
    }

    /** Crée un paiement validé avec BRS retenu pour l'agence et la période données. */
    private function creerPaiementBRS(int $agencyId, string $periode, float $brsAmount): Paiement
    {
        $contrat = Contrat::factory()->create(['agency_id' => $agencyId]);

        return Paiement::factory()->create([
            'agency_id'  => $agencyId,
            'contrat_id' => $contrat->id,
            'brs_amount' => $brsAmount,
            'statut'     => 'valide',
            'periode'    => $periode,
        ]);
    }
}
