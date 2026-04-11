<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\BienPhoto;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * BienPhotoControllerTest — Tests de la gestion des photos de biens.
 *
 * Couvre :
 *  - store : upload 1 à 10 images, première = principale
 *  - store : SVG rejeté (sécurité XSS)
 *  - destroy : supprime la photo, refile principale à la suivante
 *  - setPrincipale : change la photo principale
 *  - Sécurité multi-tenant : impossible d'agir sur la photo d'un autre bien
 */
class BienPhotoControllerTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User   $admin;
    private User   $proprio;
    private Bien   $bien;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->agency = Agency::factory()->create(['actif' => true]);

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

        $this->proprio = User::factory()->createOne([
            'role'      => 'proprietaire',
            'agency_id' => $this->agency->id,
        ]);

        $this->bien = Bien::factory()->create([
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $this->proprio->id,
        ]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helper : crée une photo en base pour un bien
    // ────────────────────────────────────────────────────────────────────────

    private function creerPhoto(Bien $bien, bool $estPrincipale = false, int $ordre = 1): BienPhoto
    {
        $chemin = 'biens/' . $bien->id . '/photo_' . $ordre . '.jpg';
        Storage::disk('public')->put($chemin, 'contenu faux');

        return BienPhoto::create([
            'bien_id'        => $bien->id,
            'chemin'         => $chemin,
            'nom_original'   => 'photo.jpg',
            'est_principale' => $estPrincipale,
            'ordre'          => $ordre,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Upload de photos
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_peut_uploader_une_photo(): void
    {
        $image = UploadedFile::fake()->create('photo.jpg', 50, 'image/jpeg');

        $this->actingAs($this->admin)
            ->post(route('admin.biens.photos.store', $this->bien), [
                'photos' => [$image],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bien_photos', [
            'bien_id'        => $this->bien->id,
            'est_principale' => true,   // première photo = principale
        ]);
    }

    #[Test]
    public function premiere_photo_uploadee_devient_principale(): void
    {
        $image = UploadedFile::fake()->create('premier.jpg', 50, 'image/jpeg');

        $this->actingAs($this->admin)
            ->post(route('admin.biens.photos.store', $this->bien), [
                'photos' => [$image],
            ]);

        $this->assertDatabaseHas('bien_photos', [
            'bien_id'        => $this->bien->id,
            'est_principale' => true,
        ]);
    }

    #[Test]
    public function photo_supplementaire_nest_pas_principale(): void
    {
        // Déjà une photo principale
        $this->creerPhoto($this->bien, true, 1);

        $image = UploadedFile::fake()->create('deuxieme.jpg', 50, 'image/jpeg');

        $this->actingAs($this->admin)
            ->post(route('admin.biens.photos.store', $this->bien), [
                'photos' => [$image],
            ]);

        // On a 2 photos, 1 seule est principale
        $this->assertEquals(1, $this->bien->photos()->where('est_principale', true)->count());
    }

    #[Test]
    public function svg_est_refuse_lors_de_lupload(): void
    {
        $svg = UploadedFile::fake()->create('image.svg', 5, 'image/svg+xml');

        $this->actingAs($this->admin)
            ->post(route('admin.biens.photos.store', $this->bien), [
                'photos' => [$svg],
            ])
            ->assertSessionHasErrors('photos.0');
    }

    // ════════════════════════════════════════════════════════════════════════
    // Suppression d'une photo
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_peut_supprimer_une_photo(): void
    {
        $photo = $this->creerPhoto($this->bien, false, 2);

        $this->actingAs($this->admin)
            ->delete(route('admin.biens.photos.destroy', [$this->bien, $photo]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('bien_photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing($photo->chemin);
    }

    #[Test]
    public function supprimer_photo_principale_reassigne_la_principale(): void
    {
        $principale = $this->creerPhoto($this->bien, true, 1);
        $suivante   = $this->creerPhoto($this->bien, false, 2);

        $this->actingAs($this->admin)
            ->delete(route('admin.biens.photos.destroy', [$this->bien, $principale]));

        $this->assertDatabaseHas('bien_photos', [
            'id'             => $suivante->id,
            'est_principale' => true,
        ]);
    }

    #[Test]
    public function impossible_de_supprimer_photo_appartenant_a_un_autre_bien(): void
    {
        // Autre agence, autre bien
        $autreAgency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'             => $autreAgency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);
        $autrePropio = User::factory()->createOne([
            'role'      => 'proprietaire',
            'agency_id' => $autreAgency->id,
        ]);
        $autreBien = Bien::factory()->create([
            'agency_id'       => $autreAgency->id,
            'proprietaire_id' => $autrePropio->id,
        ]);

        $photoAutreBien = $this->creerPhoto($autreBien, true, 1);

        // L'admin essaie de supprimer une photo qui appartient au bien d'une autre agence
        // via un URL forgé : biens/{monBien}/photos/{photoAutreBien}
        $this->actingAs($this->admin)
            ->delete(route('admin.biens.photos.destroy', [$this->bien, $photoAutreBien]))
            ->assertNotFound();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Définir la photo principale
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_peut_changer_la_photo_principale(): void
    {
        $photo1 = $this->creerPhoto($this->bien, true,  1);
        $photo2 = $this->creerPhoto($this->bien, false, 2);

        $this->actingAs($this->admin)
            ->patch(route('admin.biens.photos.principale', [$this->bien, $photo2]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bien_photos', ['id' => $photo2->id, 'est_principale' => true]);
        $this->assertDatabaseHas('bien_photos', ['id' => $photo1->id, 'est_principale' => false]);
    }

    #[Test]
    public function impossible_de_definir_principale_pour_photo_autre_bien(): void
    {
        $autreAgency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'             => $autreAgency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);
        $autrePropio = User::factory()->createOne([
            'role'      => 'proprietaire',
            'agency_id' => $autreAgency->id,
        ]);
        $autreBien = Bien::factory()->create([
            'agency_id'       => $autreAgency->id,
            'proprietaire_id' => $autrePropio->id,
        ]);
        $photoAutreBien = $this->creerPhoto($autreBien, true, 1);

        $this->actingAs($this->admin)
            ->patch(route('admin.biens.photos.principale', [$this->bien, $photoAutreBien]))
            ->assertNotFound();
    }
}
