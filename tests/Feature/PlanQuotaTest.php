<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PlanQuotaTest — Audit palier : restriction par nombre de biens.
 *
 * Vérifie que la limite d'unités du plan est réellement appliquée sur TOUS
 * les chemins de création (bien unitaire, immeuble en masse, déclaration de
 * paiement manuel) et que les helpers centralisés d'Agency sont cohérents.
 */
class PlanQuotaTest extends TestCase
{
    use RefreshDatabase;

    private function adminAvecPlan(string $planNiveau): User
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->actif('mensuel', $planNiveau)->create([
            'agency_id' => $agency->id,
        ]);

        // Admin directeur (is_owner=true par défaut de la factory) → passe agency.can.
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

    /** Sème $n biens actifs dans l'agence de l'admin. */
    private function semerBiens(User $admin, int $n): void
    {
        $proprio = $this->proprietaire($admin);
        Bien::factory()->count($n)->create([
            'agency_id'       => $admin->agency_id,
            'proprietaire_id' => $proprio->id,
            'statut'          => 'disponible',
        ]);
    }

    private function payloadBien(User $admin): array
    {
        return [
            'proprietaire_id' => $this->proprietaire($admin)->id,
            'type'            => 'appartement',
            'adresse'         => '25 Rue de Thiong',
            'ville'           => 'Dakar',
            'quartier'        => 'Plateau',
            'surface_m2'      => 85,
            'nombre_pieces'   => 3,
            'loyer_mensuel'   => 250000,
            'taux_commission' => 10,
            'statut'          => 'disponible',
        ];
    }

    // ── Bien unitaire (BienController) ────────────────────────────────────

    #[Test]
    public function starter_bloque_la_creation_au_dela_de_15_biens()
    {
        $admin = $this->adminAvecPlan('starter'); // limite = 15
        $this->semerBiens($admin, 15);

        $this->actingAs($admin)
             ->post(route('admin.biens.store'), $this->payloadBien($admin))
             ->assertRedirect(route('admin.biens.create'))
             ->assertSessionHas('upgrade_required');

        // Aucun 16ᵉ bien créé.
        $this->assertSame(15, Bien::where('agency_id', $admin->agency_id)->count());
    }

    #[Test]
    public function sous_la_limite_la_creation_du_bien_passe()
    {
        $admin = $this->adminAvecPlan('starter');
        $this->semerBiens($admin, 14);

        $this->actingAs($admin)
             ->post(route('admin.biens.store'), $this->payloadBien($admin))
             ->assertSessionMissing('upgrade_required');

        $this->assertSame(15, Bien::where('agency_id', $admin->agency_id)->count());
    }

    // ── Immeuble en masse (ImmeubleController) ────────────────────────────

    #[Test]
    public function immeuble_bloque_si_le_lot_depasse_la_limite()
    {
        $admin = $this->adminAvecPlan('starter'); // limite = 15
        $this->semerBiens($admin, 13);
        $proprio = $this->proprietaire($admin);

        $this->actingAs($admin)
             ->post(route('admin.immeubles.store'), [
                 'proprietaire_id'   => $proprio->id,
                 'nom'               => 'Résidence Test',
                 'adresse'           => '10 Rue X',
                 'ville'             => 'Dakar',
                 'avec_unites'       => '1',
                 'mode_numerotation' => 'simple',
                 'nombre_unites'     => 5,   // 13 + 5 = 18 > 15
                 'type_unite'        => 'appartement',
                 'loyer_par_unite'   => 200000,
             ])
             ->assertSessionHas('upgrade_required');

        // Rollback : ni immeuble ni nouveaux biens créés.
        $this->assertDatabaseCount('immeubles', 0);
        $this->assertSame(13, Bien::where('agency_id', $admin->agency_id)->count());
    }

    // ── Déclaration de paiement manuel (F4) ───────────────────────────────

    #[Test]
    public function declaration_manuelle_refusee_si_sur_quota_du_plan_declare()
    {
        Storage::fake('public');
        $owner = $this->adminAvecPlan('agence'); // illimité
        $this->semerBiens($owner, 20);

        $this->actingAs($owner)
             ->post(route('subscription.store'), [
                 'plan_niveau'  => 'starter', // n'autorise que 15
                 'montant'      => 5000,
                 'methode'      => 'wave',
                 'reference'    => 'TX-123',
                 'justificatif' => UploadedFile::fake()->create('recu.pdf', 50, 'application/pdf'),
             ])
             ->assertSessionHasErrors('plan_niveau');

        $this->assertDatabaseCount('subscription_payments', 0);
    }

    // ── Helpers centralisés (F1 + F2) ─────────────────────────────────────

    #[Test]
    public function les_helpers_agency_reflquent_config_plans()
    {
        $this->assertSame(15, Agency::limiteUnitesPour('starter'));
        $this->assertSame(50, Agency::limiteUnitesPour('pro'));
        $this->assertSame(50, Agency::limiteUnitesPour('legacy'));
        $this->assertNull(Agency::limiteUnitesPour('agence'));
        // Fallback unifié : absence de plan = legacy → pro (F2).
        $this->assertSame(50, Agency::limiteUnitesPour(null));
    }

    #[Test]
    public function une_agence_sans_abonnement_est_traitee_comme_legacy()
    {
        $agency = Agency::factory()->create(['actif' => true]); // aucun abonnement

        $this->assertSame(50, $agency->limiteUnites());
        $this->assertSame(5, $agency->limiteAdmins());
    }
}
