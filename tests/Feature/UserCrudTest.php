<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
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

    private function proprietaire(User $admin): User
    {
        return User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $admin->agency_id,
        ]);
    }

    private function locataire(User $admin): User
    {
        return User::factory()->create([
            'role'      => 'locataire',
            'agency_id' => $admin->agency_id,
        ]);
    }

    // ── Tests liste ───────────────────────────────────────────────────────

    /** @test */
    public function admin_peut_voir_la_liste_des_proprietaires()
    {
        $admin = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        $this->actingAs($admin)
             ->get(route('admin.users.proprietaires'))
             ->assertOk()
             ->assertSee($proprio->name);
    }

    /** @test */
    public function admin_ne_voit_pas_les_utilisateurs_des_autres_agences()
    {
        $admin1       = $this->adminAvecAgence();
        $admin2       = $this->adminAvecAgence();
        $proprio2     = $this->proprietaire($admin2);

        $this->actingAs($admin1)
             ->get(route('admin.users.proprietaires'))
             ->assertDontSee($proprio2->name);
    }

    // ── Tests création ────────────────────────────────────────────────────

    /** @test */
    public function admin_peut_creer_un_proprietaire()
    {
        $admin = $this->adminAvecAgence();

        $this->actingAs($admin)
             ->post(route('admin.users.store'), [
                 'role'                  => 'proprietaire',
                 'name'                  => 'Cheikh Diop',
                 'email'                 => 'cheikh.diop@test.com',
                 'telephone'             => '+221 77 111 22 33',
                 'password'              => 'password123',
                 'password_confirmation' => 'password123',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email'     => 'cheikh.diop@test.com',
            'role'      => 'proprietaire',
            'agency_id' => $admin->agency_id,
        ]);
    }

    /** @test */
    public function admin_peut_creer_un_locataire()
    {
        $admin = $this->adminAvecAgence();

        $this->actingAs($admin)
             ->post(route('admin.users.store'), [
                 'role'                  => 'locataire',
                 'name'                  => 'Aissatou Ba',
                 'email'                 => 'aissatou.ba@test.com',
                 'telephone'             => '+221 76 222 33 44',
                 'password'              => 'password123',
                 'password_confirmation' => 'password123',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'aissatou.ba@test.com',
            'role'  => 'locataire',
        ]);
    }

    /** @test */
    public function creation_echoue_avec_email_deja_existant()
    {
        $admin = $this->adminAvecAgence();
        User::factory()->create(['email' => 'doublon@test.com']);

        $this->actingAs($admin)
             ->post(route('admin.users.store'), [
                 'role'                  => 'proprietaire',
                 'name'                  => 'Test Doublon',
                 'email'                 => 'doublon@test.com',
                 'password'              => 'password123',
                 'password_confirmation' => 'password123',
             ])
             ->assertSessionHasErrors('email');
    }

    // ── Tests édition ────────────────────────────────────────────────────

    /** @test */
    public function admin_peut_voir_le_formulaire_dedition_dun_proprietaire()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        $this->actingAs($admin)
             ->get(route('admin.users.edit', $proprio))
             ->assertOk()
             ->assertSee($proprio->name);
    }

    /** @test */
    public function admin_peut_modifier_le_nom_et_telephone_dun_utilisateur()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        $this->actingAs($admin)
             ->patch(route('admin.users.update', $proprio), [
                 'name'      => 'Nouveau Nom Diop',
                 'email'     => $proprio->email,
                 'telephone' => '+221 77 999 88 77',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id'        => $proprio->id,
            'name'      => 'Nouveau Nom Diop',
            'telephone' => '+221 77 999 88 77',
        ]);
    }

    /** @test */
    public function admin_ne_peut_pas_modifier_un_utilisateur_dune_autre_agence()
    {
        $admin       = $this->adminAvecAgence();
        $autreAgence = Agency::factory()->create();
        $userEtranger = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $autreAgence->id,
        ]);

        $this->actingAs($admin)
             ->get(route('admin.users.edit', $userEtranger))
             ->assertForbidden();
    }

    /** @test */
    public function proprietaire_ne_peut_pas_modifier_un_autre_utilisateur()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);
        $autre   = $this->locataire($admin);

        $this->actingAs($proprio)
             ->patch(route('admin.users.update', $autre), ['name' => 'Hacker'])
             ->assertForbidden();
    }

    // ── Tests suppression ─────────────────────────────────────────────────

    /** @test */
    public function admin_peut_supprimer_un_utilisateur_de_son_agence()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        $this->actingAs($admin)
             ->delete(route('admin.users.destroy', $proprio))
             ->assertRedirect();

        $this->assertSoftDeleted('users', ['id' => $proprio->id]);
    }

    /** @test */
    public function admin_ne_peut_pas_se_supprimer_lui_meme()
    {
        $admin = $this->adminAvecAgence();

        // L'admin tente de se supprimer lui-même : 403 ou 404 selon le garde
        $response = $this->actingAs($admin)
             ->delete(route('admin.users.destroy', $admin));

        $this->assertContains($response->status(), [403, 404]);
    }

    // ── Tests accès invité ────────────────────────────────────────────────

    /** @test */
    public function invite_est_redirige_vers_login()
    {
        $this->get(route('admin.users.proprietaires'))
             ->assertRedirect(route('login'));
    }
}