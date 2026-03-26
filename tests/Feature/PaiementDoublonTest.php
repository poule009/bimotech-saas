<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
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

        // Créer l'admin
        $this->admin = User::factory()->create([
            'role'  => 'admin',
            'email' => 'admin@bimotech.sn',
        ]);

        // Créer un propriétaire
        $proprio = User::factory()->create(['role' => 'proprietaire']);

        // Créer un locataire
        $locataire = User::factory()->create(['role' => 'locataire']);

        // Créer un bien
        $bien = Bien::factory()->create([
            'proprietaire_id' => $proprio->id,
            'loyer_mensuel'   => 250000,
            'taux_commission' => 10,
            'statut'          => 'loue',
        ]);

        // Créer un contrat
        $this->contrat = Contrat::factory()->create([
            'bien_id'           => $bien->id,
            'locataire_id'      => $locataire->id,
            'loyer_contractuel' => 250000,
            'statut'            => 'actif',
        ]);
    }

    public function test_premier_paiement_accepte(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.paiements.store'), [
            'contrat_id'       => $this->contrat->id,
            'periode'          => Carbon::now()->format('Y-m'),
            'montant_encaisse' => 250000,
            'mode_paiement'    => 'virement',
            'date_paiement'    => now()->format('Y-m-d'),
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
            'contrat_id'       => $this->contrat->id,
            'periode'          => Carbon::now()->format('Y-m'),
            'montant_encaisse' => 250000,
            'mode_paiement'    => 'virement',
            'date_paiement'    => now()->format('Y-m-d'),
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
            'contrat_id'       => $this->contrat->id,
            'periode'          => Carbon::now()->format('Y-m'),
            'montant_encaisse' => 250000,
            'mode_paiement'    => 'virement',
            'date_paiement'    => now()->format('Y-m-d'),
        ]);

        $this->assertEquals(
            1,
            Paiement::where('contrat_id', $this->contrat->id)
                ->where('statut', 'valide')
                ->count()
        );
    }
}