<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * AgencySettingsControllerTest — Tests des paramètres d'agence.
 *
 * Couvre :
 *  - edit : affiche le formulaire de paramètres
 *  - update : met à jour les champs de l'agence
 *  - update : upload de logo (PNG valide)
 *  - update : SVG rejeté (sécurité XSS)
 *  - update : couleur primaire invalide rejetée
 *  - deleteLogo : supprime le logo
 */
class AgencySettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User   $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->agency = Agency::factory()->create([
            'actif' => true,
            'name'  => 'Agence Test',
            'email' => 'contact@agence.sn',
        ]);

        Subscription::factory()->create([
            'agency_id'             => $this->agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        $this->admin = User::factory()->createOne([
            'role'      => 'admin',
            'agency_id' => $this->agency->id,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Affichage du formulaire
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_voit_le_formulaire_parametres(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.agency.settings'))
            ->assertOk()
            ->assertViewIs('admin.agency-settings');
    }

    #[Test]
    public function locataire_ne_peut_pas_voir_les_parametres(): void
    {
        $locataire = User::factory()->createOne([
            'role'      => 'locataire',
            'agency_id' => $this->agency->id,
        ]);

        $this->actingAs($locataire)
            ->get(route('admin.agency.settings'))
            ->assertForbidden();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Mise à jour des paramètres
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_peut_mettre_a_jour_les_parametres_agence(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.agency.settings.update'), [
                'name'      => 'Nouvelle Agence SARL',
                'email'     => 'nouveau@agence.sn',
                'telephone' => '+221 77 123 45 67',
                'adresse'   => '123 Avenue Cheikh Anta Diop, Dakar',
                'ninea'     => '1234567A2',
            ])
            ->assertRedirect(route('admin.agency.settings'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('agencies', [
            'id'   => $this->agency->id,
            'name' => 'Nouvelle Agence SARL',
        ]);
    }

    #[Test]
    public function nom_manquant_retourne_erreur(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.agency.settings.update'), [
                'name'  => '',
                'email' => 'valid@agence.sn',
            ])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function couleur_primaire_invalide_est_rejetee(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.agency.settings.update'), [
                'name'             => 'Agence OK',
                'email'            => 'ok@agence.sn',
                'couleur_primaire' => 'rouge',   // format invalide
            ])
            ->assertSessionHasErrors('couleur_primaire');
    }

    #[Test]
    public function couleur_primaire_hex_valide_est_acceptee(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.agency.settings.update'), [
                'name'             => 'Agence OK',
                'email'            => 'ok@agence.sn',
                'couleur_primaire' => '#1a3c5e',
            ])
            ->assertRedirect(route('admin.agency.settings'))
            ->assertSessionHas('success');
    }

    // ════════════════════════════════════════════════════════════════════════
    // Upload logo
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function upload_logo_png_valide_est_accepte(): void
    {
        $logo = UploadedFile::fake()->create('logo.png', 50, 'image/png');

        $this->actingAs($this->admin)
            ->patch(route('admin.agency.settings.update'), [
                'name'  => 'Agence Test',
                'email' => 'contact@agence.sn',
                'logo'  => $logo,
            ])
            ->assertRedirect(route('admin.agency.settings'))
            ->assertSessionHas('success');

        $this->assertNotNull($this->agency->fresh()->logo_path);
        Storage::disk('public')->assertExists($this->agency->fresh()->logo_path);
    }

    #[Test]
    public function upload_svg_est_refuse_pour_securite_xss(): void
    {
        // SVG = XML pouvant contenir du JavaScript → rejeté
        $svg = UploadedFile::fake()->create('logo.svg', 5, 'image/svg+xml');

        $this->actingAs($this->admin)
            ->patch(route('admin.agency.settings.update'), [
                'name'  => 'Agence Test',
                'email' => 'contact@agence.sn',
                'logo'  => $svg,
            ])
            ->assertSessionHasErrors('logo');
    }

    // ════════════════════════════════════════════════════════════════════════
    // Suppression du logo
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_peut_supprimer_le_logo(): void
    {
        // Créer un faux logo en base
        $cheminFaux = 'logos/logo_test.png';
        Storage::disk('public')->put($cheminFaux, 'contenu faux');
        $this->agency->update(['logo_path' => $cheminFaux]);

        $this->actingAs($this->admin)
            ->delete(route('admin.agency.logo.delete'))
            ->assertRedirect(route('admin.agency.settings'))
            ->assertSessionHas('success');

        $this->assertNull($this->agency->fresh()->logo_path);
        Storage::disk('public')->assertMissing($cheminFaux);
    }

    #[Test]
    public function supprimer_logo_inexistant_ne_plante_pas(): void
    {
        // Aucun logo configuré
        $this->assertNull($this->agency->logo_path);

        $this->actingAs($this->admin)
            ->delete(route('admin.agency.logo.delete'))
            ->assertRedirect(route('admin.agency.settings'))
            ->assertSessionHas('success');
    }
}
