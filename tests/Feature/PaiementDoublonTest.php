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
}