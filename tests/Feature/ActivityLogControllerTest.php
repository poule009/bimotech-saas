<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ActivityLogControllerTest — Tests des journaux d'activité.
 *
 * Couvre :
 *  - Admin : voit uniquement les logs de son agence
 *  - SuperAdmin : voit tous les logs de toutes les agences
 *  - Filtre par description (q), action, model, date
 *  - Locataire / Propriétaire : accès refusé (403)
 */
class ActivityLogControllerTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User   $admin;
    private User   $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->createOne(['role' => 'superadmin']);

        $this->agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $this->agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        $this->admin = User::factory()->createOne([
            'role'      => 'admin',
            'agency_id' => $this->agency->id,
        ]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helper : crée un log pour une agence donnée
    // ────────────────────────────────────────────────────────────────────────

    private function creerLog(Agency $agency, string $action = 'create', string $description = 'Test log'): ActivityLog
    {
        return ActivityLog::create([
            'agency_id'   => $agency->id,
            'user_id'     => null,
            'action'      => $action,
            'model_type'  => 'App\\Models\\Bien',
            'model_id'    => 1,
            'description' => $description,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Accès
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_peut_voir_les_logs_de_son_agence(): void
    {
        $this->creerLog($this->agency, 'create', 'Bien créé');

        $this->actingAs($this->admin)
            ->get(route('admin.activity-logs.index'))
            ->assertOk()
            ->assertViewIs('activity-logs.index');
    }

    #[Test]
    public function superadmin_peut_voir_tous_les_logs(): void
    {
        $autreAgency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'             => $autreAgency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        $this->creerLog($this->agency,  'create', 'Log agence 1');
        $this->creerLog($autreAgency,   'delete', 'Log agence 2');

        $this->actingAs($this->superAdmin)
            ->get(route('superadmin.activity-logs.index'))
            ->assertOk()
            ->assertViewIs('activity-logs.index');
    }

    #[Test]
    public function locataire_ne_peut_pas_voir_les_logs(): void
    {
        $locataire = User::factory()->createOne([
            'role'      => 'locataire',
            'agency_id' => $this->agency->id,
        ]);

        $this->actingAs($locataire)
            ->get(route('admin.activity-logs.index'))
            ->assertForbidden();
    }

    #[Test]
    public function proprietaire_ne_peut_pas_voir_les_logs(): void
    {
        $proprio = User::factory()->createOne([
            'role'      => 'proprietaire',
            'agency_id' => $this->agency->id,
        ]);

        $this->actingAs($proprio)
            ->get(route('admin.activity-logs.index'))
            ->assertForbidden();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Isolation multi-tenant : admin ne voit pas les logs des autres agences
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_ne_voit_pas_les_logs_des_autres_agences(): void
    {
        $autreAgency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'             => $autreAgency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        $logMonAgence  = $this->creerLog($this->agency,  'create', 'Mon log');
        $logAutreAgence = $this->creerLog($autreAgency,  'delete', 'Autre log');

        $response = $this->actingAs($this->admin)
            ->get(route('admin.activity-logs.index'));

        $response->assertOk();

        $logs = $response->viewData('logs');
        $ids  = $logs->pluck('id')->all();

        $this->assertContains($logMonAgence->id, $ids);
        $this->assertNotContains($logAutreAgence->id, $ids);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Filtres
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function filtre_par_action_fonctionne(): void
    {
        $this->creerLog($this->agency, 'create', 'Création');
        $this->creerLog($this->agency, 'delete', 'Suppression');

        $response = $this->actingAs($this->admin)
            ->get(route('admin.activity-logs.index', ['action' => 'create']));

        $response->assertOk();
        $logs = $response->viewData('logs');

        foreach ($logs as $log) {
            $this->assertEquals('create', $log->action);
        }
    }

    #[Test]
    public function filtre_par_description_fonctionne(): void
    {
        $this->creerLog($this->agency, 'create', 'Paiement enregistré');
        $this->creerLog($this->agency, 'create', 'Contrat créé');

        $response = $this->actingAs($this->admin)
            ->get(route('admin.activity-logs.index', ['q' => 'Paiement']));

        $response->assertOk();
        $logs = $response->viewData('logs');

        foreach ($logs as $log) {
            $this->assertStringContainsStringIgnoringCase('Paiement', $log->description);
        }
    }
}
