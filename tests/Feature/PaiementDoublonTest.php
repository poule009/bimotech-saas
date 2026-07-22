<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaiementDoublonTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Contrat $contrat;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer une agence active avec abonnement (requis par CheckSubscription)
        $agency = \App\Models\Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'mensuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addMonth(),
        ]);

        // Créer l'admin rattaché à cette agence
        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'agency_id' => $agency->id,
            'email'     => 'admin@bimotech.sn',
        ]);

        // Créer un propriétaire et un locataire dans la même agence
        $proprio   = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $agency->id]);
        $locataire = User::factory()->create(['role' => 'locataire',    'agency_id' => $agency->id]);

        // Créer un bien dans cette agence
        $bien = Bien::factory()->create([
            'agency_id'       => $agency->id,
            'proprietaire_id' => $proprio->id,
            'loyer_mensuel'   => 250000,
            'taux_commission' => 10,
            'statut'          => 'loue',
        ]);

        // Créer un contrat actif dans cette agence
        $this->contrat = Contrat::factory()->create([
            'agency_id'         => $agency->id,
            'bien_id'           => $bien->id,
            'locataire_id'      => $locataire->id,
            'loyer_nu'          => 250000,
            'loyer_contractuel' => 250000,
            'statut'            => 'actif',
            'type_bail'         => 'habitation',
        ]);
    }

    public function test_premier_paiement_accepte(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.paiements.store'), [
            'contrat_id'    => $this->contrat->id,
            'periode'       => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'mode_paiement' => 'virement',
            'date_paiement' => now()->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('paiements', [
            'contrat_id' => $this->contrat->id,
            'statut'     => 'valide',
        ]);
    }

    public function test_doublon_rejete(): void
    {
        $this->actingAs($this->admin);

        // Paiement existant pour ce mois
        Paiement::factory()->create([
            'contrat_id'       => $this->contrat->id,
            'periode'          => Carbon::now()->startOfMonth()->toDateString(),
            'montant_encaisse' => 250000,
            'statut'           => 'valide',
        ]);

        // Tentative de doublon
        $response = $this->post(route('admin.paiements.store'), [
            'contrat_id'    => $this->contrat->id,
            'periode'       => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'mode_paiement' => 'virement',
            'date_paiement' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('periode');
        $this->assertEquals(1, Paiement::where('contrat_id', $this->contrat->id)->count());
    }

    public function test_paiement_annule_ne_bloque_pas(): void
    {
        $this->actingAs($this->admin);

        // Paiement annulé existant
        Paiement::factory()->create([
            'contrat_id'       => $this->contrat->id,
            'periode'          => Carbon::now()->startOfMonth()->toDateString(),
            'montant_encaisse' => 250000,
            'statut'           => 'annule',
        ]);

        // Nouveau paiement pour le même mois doit passer
        $response = $this->post(route('admin.paiements.store'), [
            'contrat_id'    => $this->contrat->id,
            'periode'       => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'mode_paiement' => 'virement',
            'date_paiement' => now()->format('Y-m-d'),
        ]);

        $this->assertEquals(
            1,
            Paiement::where('contrat_id', $this->contrat->id)
                ->where('statut', 'valide')
                ->count()
        );
    }

    /**
     * #3 — Un 1er paiement annulé puis recréé doit REDEVENIR « premier »
     * (frais d'agence + caution + DGID re-portés), car ils avaient été voidés
     * avec l'annulation. est_premier ignore désormais les annulés.
     */
    public function test_paiement_recree_apres_annulation_est_premier(): void
    {
        $this->actingAs($this->admin);

        // 1er paiement (premier) puis annulé
        Paiement::factory()->create([
            'contrat_id'           => $this->contrat->id,
            'periode'              => Carbon::now()->subMonth()->startOfMonth()->toDateString(),
            'statut'               => 'annule',
            'est_premier_paiement' => true,
        ]);

        // Recréation d'un paiement sur le contrat (mois courant, aucun paiement non-annulé)
        $this->post(route('admin.paiements.store'), [
            'contrat_id'    => $this->contrat->id,
            'periode'       => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'mode_paiement' => 'virement',
            'date_paiement' => now()->format('Y-m-d'),
        ]);

        $nouveau = Paiement::where('contrat_id', $this->contrat->id)
            ->where('statut', 'valide')
            ->first();

        $this->assertNotNull($nouveau);
        $this->assertTrue(
            (bool) $nouveau->est_premier_paiement,
            'Le paiement recréé après annulation du 1er doit être marqué « premier ».'
        );
    }
}