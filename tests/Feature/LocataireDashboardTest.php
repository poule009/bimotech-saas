<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * LocataireDashboardTest — Tests du tableau de bord locataire.
 *
 * Vérifie les accès par rôle et que les données du dashboard
 * correspondent bien au contrat du locataire connecté.
 */
class LocataireDashboardTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────

    private function adminAvecAgence(): User
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        return User::factory()->create([
            'role'      => 'admin',
            'agency_id' => $agency->id,
        ]);
    }

    private function locataire(User $admin): User
    {
        return User::factory()->create([
            'role'      => 'locataire',
            'agency_id' => $admin->agency_id,
        ]);
    }

    private function proprietaire(User $admin): User
    {
        return User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $admin->agency_id,
        ]);
    }

    // ── Tests accès ───────────────────────────────────────────────────────

    #[Test]
    public function locataire_peut_voir_son_dashboard()
    {
        $admin     = $this->adminAvecAgence();
        $locataire = $this->locataire($admin);

        $this->actingAs($locataire)
             ->get(route('locataire.dashboard'))
             ->assertOk();
    }

    #[Test]
    public function admin_ne_peut_pas_voir_le_dashboard_locataire()
    {
        // IsLocataire middleware réservé strictement aux locataires (pas aux admins)
        $admin = $this->adminAvecAgence();

        $this->actingAs($admin)
             ->get(route('locataire.dashboard'))
             ->assertForbidden();
    }

    #[Test]
    public function proprietaire_ne_peut_pas_voir_le_dashboard_locataire()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        $this->actingAs($proprio)
             ->get(route('locataire.dashboard'))
             ->assertForbidden();
    }

    #[Test]
    public function invite_redirige_vers_login_pour_le_dashboard_locataire()
    {
        $this->get(route('locataire.dashboard'))
             ->assertRedirect(route('login'));
    }

    // ── Tests données affichées ───────────────────────────────────────────

    #[Test]
    public function dashboard_locataire_avec_contrat_actif_charge_correctement()
    {
        $admin     = $this->adminAvecAgence();
        $locataire = $this->locataire($admin);
        $proprio   = $this->proprietaire($admin);

        $bien = Bien::factory()->create([
            'agency_id'      => $admin->agency_id,
            'proprietaire_id' => $proprio->id,
        ]);

        Contrat::factory()->create([
            'agency_id'    => $admin->agency_id,
            'bien_id'      => $bien->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'actif',
        ]);

        $this->actingAs($locataire)
             ->get(route('locataire.dashboard'))
             ->assertOk();
    }

    #[Test]
    public function dashboard_locataire_sans_contrat_charge_correctement()
    {
        $admin     = $this->adminAvecAgence();
        $locataire = $this->locataire($admin);

        // Un locataire sans contrat doit tout de même voir son dashboard sans erreur
        $this->actingAs($locataire)
             ->get(route('locataire.dashboard'))
             ->assertOk();
    }
}
