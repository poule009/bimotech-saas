<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\EtatTrimestrielController;
use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EtatTrimestrielTest extends TestCase
{
    use RefreshDatabase;

    // ── 1. Trimestre correct pour mois donné ─────────────────────────────────

    public function test_trimestre_correct_pour_mois_donne(): void
    {
        $this->assertSame(1, EtatTrimestrielController::trimestreFromMois(1));  // Janvier  → T1
        $this->assertSame(1, EtatTrimestrielController::trimestreFromMois(2));  // Février  → T1
        $this->assertSame(1, EtatTrimestrielController::trimestreFromMois(3));  // Mars     → T1
        $this->assertSame(2, EtatTrimestrielController::trimestreFromMois(4));  // Avril    → T2
        $this->assertSame(2, EtatTrimestrielController::trimestreFromMois(5));  // Mai      → T2
        $this->assertSame(2, EtatTrimestrielController::trimestreFromMois(6));  // Juin     → T2
        $this->assertSame(3, EtatTrimestrielController::trimestreFromMois(7));  // Juillet  → T3
        $this->assertSame(3, EtatTrimestrielController::trimestreFromMois(8));  // Août     → T3
        $this->assertSame(3, EtatTrimestrielController::trimestreFromMois(9));  // Septembre→ T3
        $this->assertSame(4, EtatTrimestrielController::trimestreFromMois(10)); // Octobre  → T4
        $this->assertSame(4, EtatTrimestrielController::trimestreFromMois(11)); // Novembre → T4
        $this->assertSame(4, EtatTrimestrielController::trimestreFromMois(12)); // Décembre → T4
    }

    // ── 2. Date limite du trimestre ───────────────────────────────────────────

    public function test_date_limite_trimestre(): void
    {
        // T1/2026 → 15 avril 2026
        $this->assertEquals(
            Carbon::create(2026, 4, 15)->startOfDay(),
            EtatTrimestrielController::dateLimiteTrimestre(1, 2026)
        );

        // T2/2026 → 15 juillet 2026
        $this->assertEquals(
            Carbon::create(2026, 7, 15)->startOfDay(),
            EtatTrimestrielController::dateLimiteTrimestre(2, 2026)
        );

        // T3/2026 → 15 octobre 2026
        $this->assertEquals(
            Carbon::create(2026, 10, 15)->startOfDay(),
            EtatTrimestrielController::dateLimiteTrimestre(3, 2026)
        );

        // T4/2025 → 15 janvier 2026 (N+1)
        $this->assertEquals(
            Carbon::create(2026, 1, 15)->startOfDay(),
            EtatTrimestrielController::dateLimiteTrimestre(4, 2025)
        );

        // T4/2026 → 15 janvier 2027 (N+1)
        $this->assertEquals(
            Carbon::create(2027, 1, 15)->startOfDay(),
            EtatTrimestrielController::dateLimiteTrimestre(4, 2026)
        );
    }

    // ── 3. Exclusion des bailleurs personnes morales ──────────────────────────

    public function test_exclut_bailleurs_personnes_morales(): void
    {
        $agency = Agency::factory()->create();
        $admin  = $this->creerAdmin($agency);
        $this->actingAs($admin);

        // Bailleur personne morale (IS) — doit être exclu
        $userMorale = User::factory()->create(['agency_id' => $agency->id]);
        $userMorale->forceFill(['role' => 'proprietaire'])->save();

        // Bailleur personne morale (IS) → exclu de l'état trimestriel (source unique : _is)
        $profilMorale = Proprietaire::create([
            'user_id'                => $userMorale->id,
            'est_personne_morale_is' => true,
        ]);

        $bien = Bien::factory()->create([
            'agency_id'       => $agency->id,
            'proprietaire_id' => $userMorale->id,
        ]);

        $contrat = Contrat::factory()->create([
            'agency_id'      => $agency->id,
            'bien_id'        => $bien->id,
            'brs_applicable' => true,
        ]);

        Paiement::factory()->create([
            'agency_id'  => $agency->id,
            'contrat_id' => $contrat->id,
            'brs_amount' => 20000,
            'periode'    => '2026-01-01',
        ]);

        $paiements = $this->appelPaiementsQuery($agency->id, 1, 2026)
            ->with('contrat.bien.proprietaire.proprietaire')
            ->get();

        $this->assertCount(
            0,
            $paiements,
            'Un bailleur personne morale ne doit pas apparaître dans l\'état trimestriel.'
        );
    }

    // ── 4. Alerte NINEA manquant ──────────────────────────────────────────────

    public function test_alerte_ninea_manquant(): void
    {
        $agency = Agency::factory()->create();
        $admin  = $this->creerAdmin($agency);
        $this->actingAs($admin);

        $user = User::factory()->create(['agency_id' => $agency->id]);
        $user->forceFill(['role' => 'proprietaire'])->save();

        // Profil sans NINEA
        Proprietaire::create([
            'user_id'             => $user->id,
            'ninea'               => null,
        ]);

        $bien = Bien::factory()->create([
            'agency_id'       => $agency->id,
            'proprietaire_id' => $user->id,
        ]);

        $contrat = Contrat::factory()->create([
            'agency_id'      => $agency->id,
            'bien_id'        => $bien->id,
            'brs_applicable' => true,
        ]);

        $paiement = Paiement::factory()->create([
            'agency_id'  => $agency->id,
            'contrat_id' => $contrat->id,
            'brs_amount' => 15000,
            'periode'    => '2026-02-01',
        ]);

        $collection = collect([$paiement->load('contrat.bien.proprietaire.proprietaire')]);
        $lignes     = EtatTrimestrielController::regroupParBailleur($collection, 1, 2026);

        $this->assertNotEmpty($lignes, 'La collection de lignes ne doit pas être vide.');
        $this->assertTrue(
            $lignes->first()['has_warning_ninea'],
            'Un bailleur sans NINEA doit déclencher has_warning_ninea = true.'
        );
    }

    // ── 5. Total BRS correct pour un trimestre ────────────────────────────────

    public function test_total_brs_par_trimestre(): void
    {
        $agency = Agency::factory()->create();
        $admin  = $this->creerAdmin($agency);
        $this->actingAs($admin);

        $user = User::factory()->create(['agency_id' => $agency->id]);
        $user->forceFill(['role' => 'proprietaire'])->save();

        Proprietaire::create([
            'user_id'             => $user->id,
            'ninea'               => '123456789',
        ]);

        $bien = Bien::factory()->create([
            'agency_id'       => $agency->id,
            'proprietaire_id' => $user->id,
        ]);

        $contrat = Contrat::factory()->create([
            'agency_id'      => $agency->id,
            'bien_id'        => $bien->id,
            'brs_applicable' => true,
        ]);

        // 3 paiements sur T1 2026 : 10 000 + 15 000 + 20 000 = 45 000 FCFA
        foreach (['2026-01-01' => 10000, '2026-02-01' => 15000, '2026-03-01' => 20000] as $periode => $brs) {
            Paiement::factory()->create([
                'agency_id'  => $agency->id,
                'contrat_id' => $contrat->id,
                'brs_amount' => $brs,
                'periode'    => $periode,
            ]);
        }

        $paiements = $this->appelPaiementsQuery($agency->id, 1, 2026)
            ->with('contrat.bien.proprietaire.proprietaire')
            ->get();

        $lignes   = EtatTrimestrielController::regroupParBailleur($paiements, 1, 2026);
        $totalBrs = $lignes->sum('brs_retenu');

        $this->assertEquals(45000, $totalBrs, 'T1 2026 : BRS jan(10k) + fév(15k) + mar(20k) = 45 000 FCFA');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function creerAdmin(Agency $agency): User
    {
        $admin = User::factory()->create(['agency_id' => $agency->id]);
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    private function appelPaiementsQuery(int $agencyId, int $trimestre, int $annee)
    {
        $controller = new EtatTrimestrielController();
        $reflection = new \ReflectionMethod($controller, 'paiementsQueryBuilder');
        return $reflection->invoke($controller, $agencyId, $trimestre, $annee);
    }
}
