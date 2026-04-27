<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContratCrudTest extends TestCase
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

    private function bienDisponible(User $admin): Bien
    {
        $proprio = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $admin->agency_id,
        ]);

        return Bien::factory()->create([
            'agency_id'       => $admin->agency_id,
            'proprietaire_id' => $proprio->id,
            'statut'          => 'disponible',
            'loyer_mensuel'   => 300000,
            'surface_m2'      => null, // Évite le plafond Loi 81-18 dans les tests de contrat
        ]);
    }

    private function locataire(User $admin): User
    {
        return User::factory()->create([
            'role'      => 'locataire',
            'agency_id' => $admin->agency_id,
        ]);
    }

    private function payloadContratValide(User $admin): array
    {
        return [
            'bien_id'             => $this->bienDisponible($admin)->id,
            'locataire_id'        => $this->locataire($admin)->id,
            'date_debut'          => now()->format('Y-m-d'),
            'date_fin'            => null,
            'loyer_nu'            => 300000,
            'charges_mensuelles'  => 20000,
            'caution'             => 600000,
            'nombre_mois_caution' => 2,
            'type_bail'           => 'habitation',
            'frais_agence'        => 150000,
            'indexation_annuelle' => 3,
            'garant_nom'          => 'Moussa Fall',
            'garant_telephone'    => '+221 77 123 45 67',
            'garant_adresse'      => 'HLM Grand Yoff, Dakar',
            'observations'        => 'RAS',
        ];
    }

    // ── Tests vue formulaire ──────────────────────────────────────────────

    #[Test]
    public function admin_peut_voir_le_formulaire_de_creation_de_contrat()
    {
        $admin = $this->adminAvecAgence();

        $this->actingAs($admin)
             ->get(route('admin.contrats.create'))
             ->assertOk()
             ->assertSee('Nouveau contrat')
             ->assertSee('type_bail')
             ->assertSee('garant_nom')
             ->assertSee('charges_mensuelles');
    }

    // ── Tests création ────────────────────────────────────────────────────

    #[Test]
    public function admin_peut_creer_un_contrat_complet_avec_tous_les_champs()
    {
        $admin   = $this->adminAvecAgence();
        $payload = $this->payloadContratValide($admin);

        $this->actingAs($admin)
             ->post(route('admin.contrats.store'), $payload)
             ->assertRedirect();

        $this->assertDatabaseHas('contrats', [
            'agency_id'           => $admin->agency_id,
            'type_bail'           => 'habitation',
            'caution'             => 600000,
            'nombre_mois_caution' => 2,
            'frais_agence'        => 150000,
            'charges_mensuelles'  => 20000,
            'indexation_annuelle' => 3,
            'garant_nom'          => 'Moussa Fall',
            'garant_telephone'    => '+221 77 123 45 67',
            'garant_adresse'      => 'HLM Grand Yoff, Dakar',
        ]);
    }

    #[Test]
    public function type_bail_commercial_est_accepte()
    {
        $admin   = $this->adminAvecAgence();
        $payload = $this->payloadContratValide($admin);
        $payload['type_bail'] = 'commercial';

        $this->actingAs($admin)
             ->post(route('admin.contrats.store'), $payload)
             ->assertRedirect();

        $this->assertDatabaseHas('contrats', ['type_bail' => 'commercial']);
    }

    #[Test]
    public function type_bail_invalide_est_rejete()
    {
        $admin   = $this->adminAvecAgence();
        $payload = $this->payloadContratValide($admin);
        $payload['type_bail'] = 'inconnu';

        $this->actingAs($admin)
             ->post(route('admin.contrats.store'), $payload)
             ->assertSessionHasErrors('type_bail');
    }

    #[Test]
    public function contrat_sans_garant_est_accepte()
    {
        $admin   = $this->adminAvecAgence();
        $payload = $this->payloadContratValide($admin);
        unset($payload['garant_nom'], $payload['garant_telephone'], $payload['garant_adresse']);

        $this->actingAs($admin)
             ->post(route('admin.contrats.store'), $payload)
             ->assertRedirect();

        $this->assertDatabaseHas('contrats', [
            'garant_nom'       => null,
            'garant_telephone' => null,
            'garant_adresse'   => null,
        ]);
    }

    #[Test]
    public function indexation_superieure_a_20_est_rejetee()
    {
        $admin   = $this->adminAvecAgence();
        $payload = $this->payloadContratValide($admin);
        $payload['indexation_annuelle'] = 25;

        $this->actingAs($admin)
             ->post(route('admin.contrats.store'), $payload)
             ->assertSessionHasErrors('indexation_annuelle');
    }

    #[Test]
    public function impossible_de_creer_deux_contrats_actifs_sur_le_meme_bien()
    {
        $admin = $this->adminAvecAgence();
        $bien  = $this->bienDisponible($admin);

        // Premier contrat
        Contrat::factory()->create([
            'agency_id'    => $admin->agency_id,
            'bien_id'      => $bien->id,
            'locataire_id' => $this->locataire($admin)->id,
            'statut'       => 'actif',
        ]);

        // Tentative d'un second contrat sur le même bien
        $payload                 = $this->payloadContratValide($admin);
        $payload['bien_id']      = $bien->id;
        $payload['locataire_id'] = $this->locataire($admin)->id;

        $this->actingAs($admin)
             ->post(route('admin.contrats.store'), $payload)
             ->assertSessionHasErrors('bien_id');
    }

    #[Test]
    public function impossible_de_creer_un_second_contrat_actif_pour_le_meme_locataire()
    {
        $admin     = $this->adminAvecAgence();
        $locataire = $this->locataire($admin);

        // Premier contrat actif pour ce locataire (sur un premier bien)
        $premierBien = $this->bienDisponible($admin);
        Contrat::factory()->create([
            'agency_id'    => $admin->agency_id,
            'bien_id'      => $premierBien->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'actif',
        ]);

        // Tentative d'un second contrat actif pour le même locataire (sur un autre bien)
        $payload                 = $this->payloadContratValide($admin);
        $payload['locataire_id'] = $locataire->id;

        $this->actingAs($admin)
             ->post(route('admin.contrats.store'), $payload)
             ->assertSessionHasErrors('locataire_id');
    }

    #[Test]
    public function locataire_avec_contrat_resilie_peut_obtenir_un_nouveau_contrat()
    {
        $admin     = $this->adminAvecAgence();
        $locataire = $this->locataire($admin);

        // Ancien contrat résilié — ne doit pas bloquer
        $ancienBien = $this->bienDisponible($admin);
        Contrat::factory()->create([
            'agency_id'    => $admin->agency_id,
            'bien_id'      => $ancienBien->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'resilie',
        ]);

        $payload                 = $this->payloadContratValide($admin);
        $payload['locataire_id'] = $locataire->id;

        $this->actingAs($admin)
             ->post(route('admin.contrats.store'), $payload)
             ->assertRedirect();

        $this->assertDatabaseHas('contrats', [
            'locataire_id' => $locataire->id,
            'statut'       => 'actif',
        ]);
    }

    #[Test]
    public function bien_dune_autre_agence_est_rejete()
    {
        $admin        = $this->adminAvecAgence();
        $autreAgence  = Agency::factory()->create();
        $bienEtranger = Bien::factory()->create([
            'agency_id' => $autreAgence->id,
            'statut'    => 'disponible',
        ]);

        $payload             = $this->payloadContratValide($admin);
        $payload['bien_id']  = $bienEtranger->id;

        $this->actingAs($admin)
             ->post(route('admin.contrats.store'), $payload)
             ->assertSessionHasErrors('bien_id');
    }

    #[Test]
    public function locataire_dune_autre_agence_est_rejete()
    {
        $admin           = $this->adminAvecAgence();
        $autreAgence     = Agency::factory()->create();
        $locataireEtranger = User::factory()->create([
            'role'      => 'locataire',
            'agency_id' => $autreAgence->id,
        ]);

        $payload                 = $this->payloadContratValide($admin);
        $payload['locataire_id'] = $locataireEtranger->id;

        $this->actingAs($admin)
             ->post(route('admin.contrats.store'), $payload)
             ->assertSessionHasErrors('locataire_id');
    }

    // ── Tests édition ────────────────────────────────────────────────────

    #[Test]
    public function admin_peut_modifier_un_contrat_actif()
    {
        $admin   = $this->adminAvecAgence();
        $contrat = Contrat::factory()->create([
            'agency_id'    => $admin->agency_id,
            'locataire_id' => $this->locataire($admin)->id,
            'statut'       => 'actif',
        ]);

        $this->actingAs($admin)
             ->put(route('admin.contrats.update', $contrat), [
                 'date_fin'            => now()->addYear()->format('Y-m-d'),
                 'loyer_nu'            => 330000,
                 'caution'             => 700000,
                 'type_bail'           => 'commercial',
                 'frais_agence'        => 200000,
                 'charges_mensuelles'  => 30000,
                 'indexation_annuelle' => 5,
                 'nombre_mois_caution' => 3,
                 'garant_nom'          => 'Fatou Diallo',
                 'garant_telephone'    => '+221 76 000 00 00',
                 'garant_adresse'      => 'Sacré-Cœur 3, Dakar',
                 'observations'        => 'Renouvellement',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('contrats', [
            'id'                  => $contrat->id,
            'loyer_contractuel'   => 360000, // loyer_nu 330000 + charges 30000
            'type_bail'           => 'commercial',
            'garant_nom'          => 'Fatou Diallo',
            'charges_mensuelles'  => 30000,
            'indexation_annuelle' => 5,
        ]);
    }

    #[Test]
    public function contrat_resilie_ne_peut_pas_etre_modifie()
    {
        $admin   = $this->adminAvecAgence();
        $contrat = Contrat::factory()->create([
            'agency_id'    => $admin->agency_id,
            'locataire_id' => $this->locataire($admin)->id,
            'statut'       => 'resilié',
        ]);

        $this->actingAs($admin)
             ->put(route('admin.contrats.update', $contrat), [
                 'loyer_nu'            => 999999,
                 'caution'             => 80000,
                 'type_bail'           => 'habitation',
             ])
             ->assertSessionHasErrors('general');
    }

    #[Test]
    public function admin_ne_peut_pas_modifier_un_contrat_dune_autre_agence()
    {
        $admin         = $this->adminAvecAgence();
        $autreAgence   = Agency::factory()->create();
        $contratEtranger = Contrat::factory()->create(['agency_id' => $autreAgence->id]);

        $response = $this->actingAs($admin)
             ->get(route('admin.contrats.edit', $contratEtranger));

        // AgencyScope masque le contrat étranger (404) ou Policy le bloque (403)
        $this->assertContains($response->status(), [403, 404]);
    }

    // ── Test locataire rapide (AJAX) ───────────────────────────────────────

    #[Test]
    public function admin_peut_creer_un_locataire_en_ajax()
    {
        $admin = $this->adminAvecAgence();

        $this->actingAs($admin)
             ->postJson(route('admin.contrats.locataire-rapide'), [
                 'name'     => 'Ibrahima Sow',
                 'email'    => 'ibrahima.sow@test.com',
                 'password' => 'password123',
             ])
             ->assertOk()
             ->assertJsonStructure(['success', 'id', 'name']);

        $this->assertDatabaseHas('users', [
            'email'     => 'ibrahima.sow@test.com',
            'role'      => 'locataire',
            'agency_id' => $admin->agency_id,
        ]);
    }

    #[Test]
    public function creation_locataire_rapide_echoue_sans_email()
    {
        $admin = $this->adminAvecAgence();

        $this->actingAs($admin)
             ->postJson(route('admin.contrats.locataire-rapide'), [
                 'name'     => 'Ibrahima Sow',
                 'password' => 'password123',
             ])
             ->assertUnprocessable();
    }
}