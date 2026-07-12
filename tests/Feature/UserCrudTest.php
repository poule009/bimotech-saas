<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Proprietaire;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function admin_peut_voir_la_liste_des_proprietaires()
    {
        $admin = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        $this->actingAs($admin)
             ->get(route('admin.users.proprietaires'))
             ->assertOk()
             ->assertSee($proprio->name);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function admin_peut_creer_un_locataire()
    {
        $admin = $this->adminAvecAgence();

        $this->actingAs($admin)
             ->post(route('admin.users.store'), [
                 'role'                  => 'locataire',
                 'name'                  => 'Aissatou Ba',
                 'email'                 => 'aissatou.ba@test.com',
                 'telephone'             => '+221 76 222 33 44',
                 'password'              => 'Password123!',
                 'password_confirmation' => 'Password123!',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'aissatou.ba@test.com',
            'role'  => 'locataire',
        ]);
    }

    #[Test]
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

    // ── Tests import de fichier (pièce d'identité) ────────────────────────

    #[Test]
    public function admin_peut_importer_une_piece_didentite_a_la_creation()
    {
        Storage::fake('public');
        $admin = $this->adminAvecAgence();

        $fichier = UploadedFile::fake()->create('cni.pdf', 200, 'application/pdf');

        $this->actingAs($admin)
             ->post(route('admin.users.store'), [
                 'role'           => 'proprietaire',
                 'name'           => 'Proprio Avec Piece',
                 'piece_identite' => $fichier,
             ])
             ->assertRedirect();

        $proprio = User::where('name', 'Proprio Avec Piece')->firstOrFail();
        $path    = $proprio->proprietaire->piece_identite_path;

        $this->assertNotNull($path, 'Le chemin de la pièce doit être enregistré.');
        Storage::disk('public')->assertExists($path);
    }

    #[Test]
    public function import_refuse_un_type_de_fichier_non_autorise()
    {
        Storage::fake('public');
        $admin = $this->adminAvecAgence();

        $exe = UploadedFile::fake()->create('virus.exe', 10, 'application/octet-stream');

        $this->actingAs($admin)
             ->post(route('admin.users.store'), [
                 'role'           => 'locataire',
                 'name'           => 'Locataire Fichier Interdit',
                 'piece_identite' => $exe,
             ])
             ->assertSessionHasErrors('piece_identite');

        $this->assertDatabaseMissing('users', ['name' => 'Locataire Fichier Interdit']);
    }

    #[Test]
    public function admin_peut_remplacer_la_piece_a_ledition_et_lancien_fichier_est_supprime()
    {
        Storage::fake('public');
        $admin   = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        // Pièce initiale déjà présente sur le disque + en base.
        $ancien = 'pieces_identite/' . $proprio->id . '/ancien.pdf';
        Storage::disk('public')->put($ancien, 'contenu-initial');
        Proprietaire::create([
            'user_id'             => $proprio->id,
            'piece_identite_path' => $ancien,
        ]);

        $nouveau = UploadedFile::fake()->image('nouveau.jpg');

        $this->actingAs($admin)
             ->patch(route('admin.users.update', $proprio), [
                 'name'           => $proprio->name,
                 'email'          => $proprio->email,
                 'piece_identite' => $nouveau,
             ])
             ->assertRedirect();

        $proprio->refresh()->load('proprietaire');
        $path = $proprio->proprietaire->piece_identite_path;

        $this->assertNotSame($ancien, $path, 'Le chemin doit pointer vers le nouveau fichier.');
        Storage::disk('public')->assertExists($path);
        Storage::disk('public')->assertMissing($ancien);
    }

    // ── Tests édition ────────────────────────────────────────────────────

    #[Test]
    public function admin_peut_voir_le_formulaire_dedition_dun_proprietaire()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        $this->actingAs($admin)
             ->get(route('admin.users.edit', $proprio))
             ->assertOk()
             ->assertSee($proprio->name);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function admin_peut_supprimer_un_utilisateur_de_son_agence()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaire($admin);

        $this->actingAs($admin)
             ->delete(route('admin.users.destroy', $proprio))
             ->assertRedirect();

        $this->assertSoftDeleted('users', ['id' => $proprio->id]);
    }

    #[Test]
    public function admin_ne_peut_pas_se_supprimer_lui_meme()
    {
        $admin = $this->adminAvecAgence();

        // L'admin tente de se supprimer lui-même : 403 ou 404 selon le garde
        $response = $this->actingAs($admin)
             ->delete(route('admin.users.destroy', $admin));

        $this->assertContains($response->status(), [403, 404]);
    }

    // ── Tests accès invité ────────────────────────────────────────────────

    #[Test]
    public function invite_est_redirige_vers_login()
    {
        $this->get(route('admin.users.proprietaires'))
             ->assertRedirect(route('login'));
    }
}