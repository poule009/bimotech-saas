<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ComptabiliteDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function adminAgence(): User
    {
        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'plan_niveau'           => 'agence',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        return User::factory()->create(['role' => 'admin', 'agency_id' => $agency->id]);
    }

    #[Test]
    public function le_module_comptabilite_se_charge_sans_erreur()
    {
        $admin = $this->adminAgence();

        $this->actingAs($admin)
            ->get(route('admin.comptabilite.index'))
            ->assertOk()
            ->assertSee('Comptabilité', false)
            ->assertSee('Propriétaires', false)
            ->assertSee('Agence', false)
            ->assertSee('Vérification', false);
    }
}
