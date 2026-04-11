<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * DashboardControllerTest — Tests des tableaux de bord.
 *
 * Couvre :
 *  - Admin → vue admin.dashboard
 *  - Superadmin → redirigé vers superadmin.dashboard
 *  - Propriétaire → vue proprietaire.dashboard
 *  - Locataire → vue locataire.dashboard
 *  - Accès refusé si mauvais rôle
 */
class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    // ────────────────────────────────────────────────────────────────────────
    // Helper : agence + admin + subscription active
    // ────────────────────────────────────────────────────────────────────────

    private function creerAgenceActiveAvecAdmin(): array
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        $admin = User::factory()->createOne([
            'role'      => 'admin',
            'agency_id' => $agency->id,
        ]);

        return [$agency, $admin];
    }

    // ════════════════════════════════════════════════════════════════════════
    // Dashboard Admin
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_voit_son_dashboard(): void
    {
        [, $admin] = $this->creerAgenceActiveAvecAdmin();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewIs('admin.dashboard');
    }

    #[Test]
    public function superadmin_est_redirige_vers_superadmin_dashboard(): void
    {
        /** @var User $superAdmin */
        $superAdmin = User::factory()->createOne(['role' => 'superadmin']);

        $this->actingAs($superAdmin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('superadmin.dashboard'));
    }

    #[Test]
    public function locataire_ne_peut_pas_acceder_au_dashboard_admin(): void
    {
        [, $admin] = $this->creerAgenceActiveAvecAdmin();

        /** @var User $locataire */
        $locataire = User::factory()->createOne([
            'role'      => 'locataire',
            'agency_id' => $admin->agency_id,
        ]);

        $this->actingAs($locataire)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Dashboard Propriétaire
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function proprietaire_voit_son_dashboard(): void
    {
        [, $admin] = $this->creerAgenceActiveAvecAdmin();

        /** @var User $proprio */
        $proprio = User::factory()->createOne([
            'role'      => 'proprietaire',
            'agency_id' => $admin->agency_id,
        ]);

        $this->actingAs($proprio)
            ->get(route('proprietaire.dashboard'))
            ->assertOk()
            ->assertViewIs('proprietaire.dashboard');
    }

    #[Test]
    public function locataire_ne_peut_pas_acceder_au_dashboard_proprietaire(): void
    {
        // IsProprietaire autorise admin+superadmin mais PAS locataire
        [, $admin] = $this->creerAgenceActiveAvecAdmin();

        /** @var User $locataire */
        $locataire = User::factory()->createOne([
            'role'      => 'locataire',
            'agency_id' => $admin->agency_id,
        ]);

        $this->actingAs($locataire)
            ->get(route('proprietaire.dashboard'))
            ->assertForbidden();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Dashboard Locataire
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function locataire_voit_son_dashboard(): void
    {
        [, $admin] = $this->creerAgenceActiveAvecAdmin();

        /** @var User $locataire */
        $locataire = User::factory()->createOne([
            'role'      => 'locataire',
            'agency_id' => $admin->agency_id,
        ]);

        $this->actingAs($locataire)
            ->get(route('locataire.dashboard'))
            ->assertOk()
            ->assertViewIs('locataire.dashboard');
    }

    #[Test]
    public function proprietaire_ne_peut_pas_acceder_au_dashboard_locataire(): void
    {
        // IsLocataire autorise admin+superadmin mais PAS proprietaire
        [, $admin] = $this->creerAgenceActiveAvecAdmin();

        /** @var User $proprio */
        $proprio = User::factory()->createOne([
            'role'      => 'proprietaire',
            'agency_id' => $admin->agency_id,
        ]);

        $this->actingAs($proprio)
            ->get(route('locataire.dashboard'))
            ->assertForbidden();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Non authentifié → redirigé vers login
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function non_authentifie_est_redirige_vers_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }
}
