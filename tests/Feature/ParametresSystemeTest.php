<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use App\Support\PlatformSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Module Super Admin « Paramètres système ».
 *
 * Couvre : accès réservé au principal, persistance des trois volets, blocage
 * du mode maintenance côté agences (Super Admin exempté) et filtrage du
 * journal d'activité critique.
 */
class ParametresSystemeTest extends TestCase
{
    use RefreshDatabase;

    private User $principal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->principal = User::factory()->createOne([
            'role'             => 'superadmin',
            'sa_est_principal' => true,
        ]);
    }

    private function agenceActive(): Agency
    {
        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'        => $agency->id,
            'statut'           => 'essai',
            'date_debut_essai' => now(),
            'date_fin_essai'   => now()->addDays(30),
        ]);

        return $agency;
    }

    // ── Accès ────────────────────────────────────────────────────────────────

    #[Test]
    public function le_principal_voit_les_parametres(): void
    {
        $this->actingAs($this->principal)
            ->get(route('superadmin.parametres.index'))
            ->assertOk()
            ->assertViewIs('superadmin.parametres.index')
            ->assertSee('Paramètres système');
    }

    #[Test]
    public function un_collaborateur_restreint_na_pas_acces(): void
    {
        $collab = User::factory()->createOne([
            'role'             => 'superadmin',
            'sa_est_principal' => false,
        ]);

        $this->actingAs($collab)
            ->get(route('superadmin.parametres.index'))
            ->assertForbidden();
    }

    #[Test]
    public function un_admin_agence_na_pas_acces(): void
    {
        $admin = User::factory()->createOne(['role' => 'admin']);

        // Le middleware isSuperAdmin redirige les non-superadmins (pas un 403).
        $this->actingAs($admin)
            ->get(route('superadmin.parametres.index'))
            ->assertRedirect();
    }

    // ── Persistance des volets ────────────────────────────────────────────────

    #[Test]
    public function le_volet_general_est_persiste_et_journalise(): void
    {
        $this->actingAs($this->principal)
            ->patch(route('superadmin.parametres.general'), [
                'plateforme_nom'    => 'Bimmo',
                'support_email'     => 'aide@bimo-tech.sn',
                'support_telephone' => '+221 33 000 00 00',
            ])
            ->assertRedirect();

        $settings = app(PlatformSettings::class);
        $settings->flush();
        $this->assertSame('aide@bimo-tech.sn', $settings->supportEmail());
        $this->assertSame('+221 33 000 00 00', $settings->supportTelephone());

        $this->assertDatabaseHas('activity_logs', ['action' => 'params_modifies']);
    }

    #[Test]
    public function le_volet_securite_est_persiste(): void
    {
        $this->actingAs($this->principal)
            ->patch(route('superadmin.parametres.securite'), [
                'securite_2fa_obligatoire'    => '1',
                'securite_session_expiration' => '0',
                'securite_session_minutes'    => '45',
                'securite_mdp_renforce'       => '1',
            ])
            ->assertRedirect();

        $settings = app(PlatformSettings::class);
        $settings->flush();
        $this->assertTrue($settings->deuxFacteursObligatoire());
        $this->assertFalse($settings->sessionExpiration());
        $this->assertSame(45, $settings->sessionMinutes());
        $this->assertTrue($settings->motDePasseRenforce());
    }

    // ── Mode maintenance ──────────────────────────────────────────────────────

    #[Test]
    public function la_maintenance_bloque_les_agences_mais_pas_le_super_admin(): void
    {
        $agency = $this->agenceActive();
        $admin  = User::factory()->createOne([
            'role'      => 'admin',
            'agency_id' => $agency->id,
        ]);

        // Activation via le volet maintenance.
        $this->actingAs($this->principal)
            ->patch(route('superadmin.parametres.maintenance'), [
                'maintenance_active'  => '1',
                'maintenance_message' => 'Intervention en cours.',
            ])
            ->assertRedirect();

        app(PlatformSettings::class)->flush();
        $this->assertTrue(app(PlatformSettings::class)->maintenanceActive());

        // L'agence est bloquée (page d'attente 503).
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertStatus(503);

        // Le Super Admin reste pleinement accessible.
        $this->actingAs($this->principal)
            ->get(route('superadmin.dashboard'))
            ->assertOk();

        $this->assertDatabaseHas('activity_logs', ['action' => 'params_modifies']);
    }

    // ── Journal critique ──────────────────────────────────────────────────────

    #[Test]
    public function le_journal_naffiche_que_les_evenements_critiques(): void
    {
        $agency = Agency::factory()->create();

        // Critique (suspension d'agence).
        ActivityLog::create([
            'user_id'      => $this->principal->id,
            'agency_id'    => $agency->id,
            'action'       => 'agence_suspendue',
            'is_sensitive' => true,
            'description'  => 'Agence suspendue POUR-TEST',
            'model_type'   => Agency::class,
            'model_id'     => $agency->id,
        ]);

        // Non critique (mise à jour banale d'un modèle).
        ActivityLog::create([
            'user_id'      => $this->principal->id,
            'agency_id'    => $agency->id,
            'action'       => 'updated',
            'is_sensitive' => false,
            'description'  => 'Bien modifié BANAL-TEST',
            'model_type'   => Agency::class,
            'model_id'     => $agency->id,
        ]);

        $this->actingAs($this->principal)
            ->get(route('superadmin.parametres.index'))
            ->assertOk()
            ->assertSee('Agence suspendue POUR-TEST')
            ->assertDontSee('Bien modifié BANAL-TEST');
    }

    #[Test]
    public function le_filtre_de_severite_restreint_le_journal(): void
    {
        $agency = Agency::factory()->create();

        ActivityLog::create([
            'user_id' => $this->principal->id, 'agency_id' => $agency->id,
            'action' => 'agence_suspendue', 'is_sensitive' => true,
            'description' => 'HAUTE-TEST', 'model_type' => Agency::class, 'model_id' => $agency->id,
        ]);
        ActivityLog::create([
            'user_id' => $this->principal->id, 'agency_id' => $agency->id,
            'action' => 'upgrade', 'is_sensitive' => false,
            'description' => 'BASSE-TEST', 'model_type' => Agency::class, 'model_id' => $agency->id,
        ]);

        $this->actingAs($this->principal)
            ->get(route('superadmin.parametres.index', ['severite' => 'haute']))
            ->assertOk()
            ->assertSee('HAUTE-TEST')
            ->assertDontSee('BASSE-TEST');
    }
}
