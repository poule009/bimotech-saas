<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ProprietaireDashboardTest — Tests du tableau de bord propriétaire.
 *
 * Vérifie les accès par rôle et que les données affichées sont
 * bien isolées à l'agence du propriétaire.
 */
class ProprietaireDashboardTest extends TestCase
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

    private function proprietaire(User $admin): User
    {
        return User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $admin->agency_id,
        ]);
    }

    // ── Tests accès ───────────────────────────────────────────────────────

    #[Test]
    public function proprietaire_peut_voir_son_dashboard()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        $this->actingAs($proprio)
             ->get(route('proprietaire.dashboard'))
             ->assertOk();
    }

    #[Test]
    public function admin_peut_voir_le_dashboard_proprietaire()
    {
        // La gate isProprietaire inclut le rôle admin
        $admin = $this->adminAvecAgence();

        $this->actingAs($admin)
             ->get(route('proprietaire.dashboard'))
             ->assertOk();
    }

    #[Test]
    public function locataire_ne_peut_pas_voir_le_dashboard_proprietaire()
    {
        $admin     = $this->adminAvecAgence();
        $locataire = User::factory()->create([
            'role'      => 'locataire',
            'agency_id' => $admin->agency_id,
        ]);

        $this->actingAs($locataire)
             ->get(route('proprietaire.dashboard'))
             ->assertForbidden();
    }

    #[Test]
    public function invite_redirige_vers_login_pour_le_dashboard_proprietaire()
    {
        $this->get(route('proprietaire.dashboard'))
             ->assertRedirect(route('login'));
    }

    // ── Tests données affichées ───────────────────────────────────────────

    #[Test]
    public function dashboard_proprietaire_affiche_ses_biens()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        Bien::factory()->create([
            'agency_id'      => $admin->agency_id,
            'proprietaire_id' => $proprio->id,
            'adresse'        => '12 Rue Kléber',
        ]);

        $this->actingAs($proprio)
             ->get(route('proprietaire.dashboard'))
             ->assertOk()
             ->assertSee('12 Rue Kléber');
    }

    #[Test]
    public function dashboard_proprietaire_naffiche_pas_les_biens_des_autres()
    {
        $admin   = $this->adminAvecAgence();
        $proprio1 = $this->proprietaire($admin);
        $proprio2 = $this->proprietaire($admin);

        Bien::factory()->create([
            'agency_id'      => $admin->agency_id,
            'proprietaire_id' => $proprio2->id,
            'adresse'        => 'Bien du voisin',
        ]);

        $this->actingAs($proprio1)
             ->get(route('proprietaire.dashboard'))
             ->assertOk()
             ->assertDontSee('Bien du voisin');
    }
}
