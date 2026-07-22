<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BienCrudTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Crée une agence avec un abonnement actif et retourne l'admin.
     */
    private function adminAvecAgence(): User
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'              => $agency->id,
            'statut'                 => 'actif',
            'plan'                   => 'annuel',
            'date_debut_abonnement'  => now()->subMonth(),
            'date_fin_abonnement'    => now()->addYear(),
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

    private function payloadBienValide(User $admin): array
    {
        return [
            'proprietaire_id' => $this->proprietaire($admin)->id,
            'type'            => 'appartement',
            'adresse'         => '25 Rue de Thiong',
            'ville'           => 'Dakar',
            'quartier'        => 'Plateau',
            'commune'         => 'Dakar-Plateau',
            'surface_m2'      => 85,
            'nombre_pieces'   => 3,
            'meuble'          => '1',
            'loyer_mensuel'   => 250000,
            'taux_commission' => 10,
            'statut'          => 'disponible',
            'description'     => 'Appartement F3 climatisé.',
        ];
    }

    // ── Tests création ────────────────────────────────────────────────────

    #[Test]
    public function admin_peut_voir_le_formulaire_de_creation()
    {
        $admin = $this->adminAvecAgence();

        $this->actingAs($admin)
             ->get(route('admin.biens.create'))
             ->assertOk()
             ->assertSee('Nouveau bien');
    }

    #[Test]
    public function admin_peut_creer_un_bien_avec_tous_les_champs()
    {
        $admin   = $this->adminAvecAgence();
        $payload = $this->payloadBienValide($admin);

        $this->actingAs($admin)
             ->post(route('admin.biens.store'), $payload)
             ->assertRedirect();

        $this->assertDatabaseHas('biens', [
            'agency_id'  => $admin->agency_id,
            'type'       => 'appartement',
            'adresse'    => '25 Rue de Thiong',
            'ville'      => 'Dakar',
            'quartier'   => 'Plateau',
            'commune'    => 'Dakar-Plateau',
            'meuble'     => true,
        ]);
    }

    #[Test]
    public function bien_cree_sans_quartier_ni_commune_est_accepte()
    {
        $admin   = $this->adminAvecAgence();
        $payload = $this->payloadBienValide($admin);
        unset($payload['quartier'], $payload['commune']);

        $this->actingAs($admin)
             ->post(route('admin.biens.store'), $payload)
             ->assertRedirect();

        $this->assertDatabaseHas('biens', [
            'adresse'  => '25 Rue de Thiong',
            'quartier' => null,
            'commune'  => null,
        ]);
    }

    #[Test]
    public function bien_non_meuble_par_defaut()
    {
        $admin   = $this->adminAvecAgence();
        $payload = $this->payloadBienValide($admin);
        unset($payload['meuble']); // checkbox non cochée → absent du POST

        $this->actingAs($admin)
             ->post(route('admin.biens.store'), $payload)
             ->assertRedirect();

        $this->assertDatabaseHas('biens', [
            'adresse' => '25 Rue de Thiong',
            'meuble'  => false,
        ]);
    }

    #[Test]
    public function creation_echoue_si_proprietaire_hors_agence()
    {
        $admin          = $this->adminAvecAgence();
        $autreAgence    = Agency::factory()->create();
        $proprioEtranger = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $autreAgence->id,
        ]);

        $payload = $this->payloadBienValide($admin);
        $payload['proprietaire_id'] = $proprioEtranger->id;

        $this->actingAs($admin)
             ->post(route('admin.biens.store'), $payload)
             ->assertSessionHasErrors('proprietaire_id');
    }

    #[Test]
    public function loyer_obligatoire_a_la_creation()
    {
        $admin   = $this->adminAvecAgence();
        $payload = $this->payloadBienValide($admin);
        unset($payload['loyer_mensuel']);

        $this->actingAs($admin)
             ->post(route('admin.biens.store'), $payload)
             ->assertSessionHasErrors('loyer_mensuel');
    }

    #[Test]
    public function proprietaire_ne_peut_pas_creer_un_bien()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        $this->actingAs($proprio)
             ->post(route('admin.biens.store'), $this->payloadBienValide($admin))
             ->assertForbidden();
    }

    // ── Tests édition ────────────────────────────────────────────────────

    #[Test]
    public function admin_peut_modifier_quartier_commune_et_meuble()
    {
        $admin = $this->adminAvecAgence();
        $bien  = Bien::factory()->create([
            'agency_id'       => $admin->agency_id,
            'proprietaire_id' => $this->proprietaire($admin)->id,
        ]);

        $this->actingAs($admin)
             ->put(route('admin.biens.update', $bien), [
                 'proprietaire_id' => $bien->proprietaire_id,
                 'type'            => $bien->type,
                 'adresse'         => $bien->adresse,
                 'ville'           => 'Thiès',
                 'quartier'        => 'Mbour Route',
                 'commune'         => 'Thiès-Nord',
                 'surface_m2'      => $bien->surface_m2,
                 'nombre_pieces'   => $bien->nombre_pieces,
                 'meuble'          => '1',
                 'loyer_mensuel'   => $bien->loyer_mensuel,
                 'taux_commission' => $bien->taux_commission,
                 'statut'          => $bien->statut,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('biens', [
            'id'       => $bien->id,
            'ville'    => 'Thiès',
            'quartier' => 'Mbour Route',
            'commune'  => 'Thiès-Nord',
            'meuble'   => true,
        ]);
    }

    #[Test]
    public function admin_ne_peut_pas_modifier_un_bien_dune_autre_agence()
    {
        $admin      = $this->adminAvecAgence();
        $autreAgence = Agency::factory()->create();
        $bienEtranger = Bien::factory()->create(['agency_id' => $autreAgence->id]);

        $response = $this->actingAs($admin)
             ->get(route('admin.biens.edit', $bienEtranger));

        // Le bien étranger est masqué par AgencyScope (404) ou bloqué par Policy (403)
        $this->assertContains($response->status(), [403, 404]);
    }

    // ── Sécurité AgencyScope ──────────────────────────────────────────────

    #[Test]
    public function agencyscope_masque_les_biens_des_autres_agences()
    {
        $admin1 = $this->adminAvecAgence();
        $admin2 = $this->adminAvecAgence();

        // La référence est le champ affiché en titre de chaque ligne de la liste.
        Bien::factory()->create([
            'agency_id'       => $admin1->agency_id,
            'proprietaire_id' => $this->proprietaire($admin1)->id,
            'reference'       => 'BIEN-AGENCE-1',
        ]);
        Bien::factory()->create([
            'agency_id'       => $admin2->agency_id,
            'proprietaire_id' => $this->proprietaire($admin2)->id,
            'reference'       => 'BIEN-AGENCE-2',
        ]);

        $this->actingAs($admin1)
             ->get(route('admin.biens.index'))
             ->assertSee('BIEN-AGENCE-1')
             ->assertDontSee('BIEN-AGENCE-2');
    }

    #[Test]
    public function le_filtre_par_type_ne_montre_que_les_biens_du_type_choisi()
    {
        $admin   = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        Bien::factory()->create([
            'agency_id'       => $admin->agency_id,
            'proprietaire_id' => $proprio->id,
            'type'            => 'terrain',
            'reference'       => 'REF-TERRAIN',
        ]);
        Bien::factory()->create([
            'agency_id'       => $admin->agency_id,
            'proprietaire_id' => $proprio->id,
            'type'            => 'appartement',
            'reference'       => 'REF-APPART',
        ]);

        // Filtre type=terrain : seul le terrain apparaît.
        $this->actingAs($admin)
             ->get(route('admin.biens.index', ['type' => 'terrain']))
             ->assertOk()
             ->assertSee('REF-TERRAIN')
             ->assertDontSee('REF-APPART');

        // Sans filtre : les deux apparaissent.
        $this->actingAs($admin)
             ->get(route('admin.biens.index'))
             ->assertSee('REF-TERRAIN')
             ->assertSee('REF-APPART');
    }

    #[Test]
    public function un_type_invalide_est_ignore_et_affiche_tout()
    {
        $admin   = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        Bien::factory()->create([
            'agency_id'       => $admin->agency_id,
            'proprietaire_id' => $proprio->id,
            'type'            => 'villa',
            'reference'       => 'REF-VILLA',
        ]);

        // type=nimportequoi n'est pas dans Bien::TYPES → ignoré (pas de filtre).
        $this->actingAs($admin)
             ->get(route('admin.biens.index', ['type' => 'nimportequoi']))
             ->assertOk()
             ->assertSee('REF-VILLA');
    }
}