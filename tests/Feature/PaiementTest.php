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

class PaiementTest extends TestCase
{
    use RefreshDatabase;

    private User    $admin;
    private Agency  $agency;
    private Contrat $contrat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agency = Agency::factory()->create([
            'name'  => 'Agence Test',
            'email' => 'agence@test.com',
        ]);

        Subscription::create([
            'agency_id'        => $this->agency->id,
            'statut'           => 'essai',
            'date_debut_essai' => now(),
            'date_fin_essai'   => now()->addDays(30),
        ]);

        $this->admin = User::factory()->create([
            'role'              => 'admin',
            'agency_id'         => $this->agency->id,
            'email_verified_at' => now(),
        ]);

        $proprio = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $this->agency->id,
        ]);

        $locataire = User::factory()->create([
            'role'      => 'locataire',
            'agency_id' => $this->agency->id,
        ]);

        $bien = Bien::create([
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $proprio->id,
            'reference'       => 'BIEN-001',
            'type'            => 'appartement',
            'adresse'         => 'Rue Test',
            'ville'           => 'Dakar',
            'loyer_mensuel'   => 200000,
            'taux_commission' => 10.00,
            'statut'          => 'loue',
        ]);

        $this->contrat = Contrat::create([
            'agency_id'          => $this->agency->id,
            'bien_id'            => $bien->id,
            'locataire_id'       => $locataire->id,
            'date_debut'         => now()->subMonths(3),
            'loyer_nu'           => 200000,
            'loyer_contractuel'  => 200000,
            'charges_mensuelles' => 0,
            'tom_amount'         => 0,
            'caution'            => 200000,
            'statut'             => 'actif',
            'type_bail'          => 'habitation',
        ]);
    }

    #[Test]
    public function calcul_commission_est_correct(): void
    {
        // Loyer nu = 200 000 F, taux = 10%
        // Commission HT  = 200 000 × 10%       = 20 000 F
        // TVA (18%)       = 20 000 × 18%        =  3 600 F
        // Commission TTC  = 20 000 + 3 600       = 23 600 F
        // Net propriétaire = 200 000 − 23 600   = 176 400 F
        $calcul = Paiement::calculerMontants(
            loyerNu:        200000,
            tauxCommission: 10,
            chargesAmount:  0,
            tomAmount:      0,
        );

        $this->assertEquals(200000, $calcul['montant_encaisse'], 'Montant encaissé incorrect');
        $this->assertEquals(20000,  $calcul['commission_ht'],    'Commission HT incorrecte');
        $this->assertEquals(3600,   $calcul['tva'],              'TVA incorrecte');
        $this->assertEquals(23600,  $calcul['commission_ttc'],   'Commission TTC incorrecte');
        $this->assertEquals(176400, $calcul['net_proprietaire'], 'Net propriétaire incorrect');
    }

    #[Test]
    public function doublon_paiement_est_rejete(): void
    {
        $this->actingAs($this->admin);

        $data = [
            'contrat_id'    => $this->contrat->id,
            'periode'       => now()->startOfMonth()->format('Y-m-d'),
            'mode_paiement' => 'especes',
            'date_paiement' => now()->toDateString(),
        ];

        // Premier envoi — doit passer
        $this->post(route('admin.paiements.store'), $data)
             ->assertSessionHasNoErrors();

        // Deuxième envoi — même période → doit être rejeté
        $this->post(route('admin.paiements.store'), $data)
             ->assertSessionHasErrors('periode');
    }

    #[Test]
    public function paiement_valide_est_cree(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.paiements.store'), [
            'contrat_id'    => $this->contrat->id,
            'periode'       => now()->startOfMonth()->format('Y-m-d'),
            'mode_paiement' => 'virement',
            'date_paiement' => now()->toDateString(),
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('paiements', [
            'contrat_id'  => $this->contrat->id,
            'statut'      => 'valide',
            'mode_paiement' => 'virement',
        ]);
    }

    #[Test]
    public function locataire_ne_voit_pas_paiement_dun_autre(): void
    {
        // Deuxième locataire dans la même agence
        $autreLocataire = User::factory()->createOne([
            'role'      => 'locataire',
            'agency_id' => $this->agency->id,
        ]);

        // Paiement sur le contrat du premier locataire
        $paiement = Paiement::create([
            'agency_id'                => $this->agency->id,
            'contrat_id'               => $this->contrat->id,
            'periode'                  => now()->startOfMonth(),
            'loyer_nu'                 => 200000,
            'montant_encaisse'         => 200000,
            'taux_commission_applique' => 10.00,
            'commission_agence'        => 20000,
            'tva_commission'           => 3600,
            'commission_ttc'           => 23600,
            'net_proprietaire'         => 176400,
            'mode_paiement'            => 'especes',
            'date_paiement'            => now(),
            'reference_paiement'       => 'QUITT-TEST-001',
            'statut'                   => 'valide',
        ]);

        // L'autre locataire tente d'accéder au PDF → doit recevoir 403
        $this->actingAs($autreLocataire)
             ->get(route('locataire.paiements.pdf', $paiement))
             ->assertForbidden();
    }
}