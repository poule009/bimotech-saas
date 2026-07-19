<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\BienPhoto;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * VitrineControllerTest — vitrine publique par agence (BimoPortail v2).
 *
 * Couvre : accueil (agence + biens + stats), filet de sécurité « en vedette »,
 * filtre par type de bien, fiche détail + WhatsApp + copie de lien, et les gardes
 * (agence inactive, bien loué/inexistant).
 */
class VitrineControllerTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agence;
    private User   $proprio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agence = Agency::factory()->create([
            'actif'    => true,
            'name'     => 'Agence Test Vitrine',
            'whatsapp' => '+221771234567',
            'slogan'   => 'Notre belle accroche vitrine',
        ]);

        Subscription::factory()->create([
            'agency_id'             => $this->agence->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        $this->proprio = User::factory()->createOne([
            'role'      => 'proprietaire',
            'agency_id' => $this->agence->id,
        ]);
    }

    /** Crée un bien réellement affichable en vitrine (dispo + titre + quartier + photo). */
    private function bienDispo(array $attrs = []): Bien
    {
        $bien = Bien::factory()->create(array_merge([
            'agency_id'       => $this->agence->id,
            'proprietaire_id' => $this->proprio->id,
            'statut'          => 'disponible',
            'visible_portail' => true,
            'titre'           => 'Bien ' . fake()->unique()->numerify('###'),
            'quartier'        => 'Ngor',
            'type'            => 'villa',
        ], $attrs));

        BienPhoto::create([
            'bien_id'        => $bien->id,
            'chemin'         => 'biens/' . $bien->id . '/photo.jpg',
            'est_principale' => true,
            'ordre'          => 1,
        ]);

        return $bien->fresh();
    }

    #[Test]
    public function la_vitrine_affiche_l_agence_et_ses_biens(): void
    {
        $this->bienDispo(['titre' => 'Villa contemporaine Ngor']);

        $res = $this->get(route('vitrine.home', $this->agence->slug));

        $res->assertOk()
            ->assertSee('Agence Test Vitrine')
            ->assertSee('Notre belle accroche vitrine')
            ->assertSee('Villa contemporaine Ngor')
            ->assertSee('Vitrine propulsée par')
            ->assertSee('wa.me/221771234567');
    }

    #[Test]
    public function le_filet_de_securite_affiche_des_biens_a_defaut_de_vedette(): void
    {
        // Aucun bien coché « en vedette » → la section vedette n'est pas vide.
        $this->bienDispo(['titre' => 'Bien récent sans vedette']);

        $res = $this->get(route('vitrine.home', $this->agence->slug));

        $res->assertOk()
            ->assertSee('Biens en vedette')
            ->assertSee('Bien récent sans vedette');
    }

    #[Test]
    public function un_bien_coche_en_vedette_est_mis_en_avant(): void
    {
        $this->bienDispo(['titre' => 'Bien ordinaire']);
        $this->bienDispo(['titre' => 'Bien vedette', 'est_en_vedette' => true]);

        $res = $this->get(route('vitrine.home', $this->agence->slug));

        $res->assertOk()
            ->assertSee('Bien vedette')
            ->assertSee('Coup de cœur');
    }

    #[Test]
    public function le_catalogue_filtre_par_type_de_bien(): void
    {
        $this->bienDispo(['titre' => 'La Villa', 'type' => 'villa']);
        $this->bienDispo(['titre' => 'Le Studio', 'type' => 'studio']);

        $res = $this->get(route('vitrine.home', [$this->agence->slug, 'type' => 'studio']));

        // Le catalogue filtré ne compte qu'un bien (la section vedette, elle, n'est
        // pas filtrée par type — c'est le catalogue qui porte le filtre).
        $res->assertOk()
            ->assertSee('Le Studio')
            ->assertSee('1 bien correspondant');
    }

    #[Test]
    public function la_fiche_bien_s_affiche_avec_contact_et_partage(): void
    {
        $bien = $this->bienDispo(['titre' => 'Duplex vue mer', 'description' => 'Superbe duplex.']);

        $res = $this->get(route('vitrine.bien', [$this->agence->slug, $bien->slug]));

        $res->assertOk()
            ->assertSee('Duplex vue mer')
            ->assertSee('Superbe duplex.')
            ->assertSee('wa.me/221771234567')
            ->assertSee('Copier le lien de ce bien');
    }

    #[Test]
    public function un_bien_indisponible_redirige_vers_la_vitrine(): void
    {
        $bien = $this->bienDispo();
        $bien->update(['statut' => 'loue']);

        $res = $this->get(route('vitrine.bien', [$this->agence->slug, $bien->slug]));

        $res->assertRedirect(route('vitrine.home', $this->agence->slug));
    }

    #[Test]
    public function une_agence_inactive_renvoie_404(): void
    {
        // actif n'est pas fillable (sécurité) → assignation directe.
        $this->agence->actif = false;
        $this->agence->save();

        $this->get(route('vitrine.home', $this->agence->slug))->assertNotFound();
    }

    #[Test]
    public function un_slug_inconnu_renvoie_404(): void
    {
        $this->get(route('vitrine.home', 'slug-inexistant'))->assertNotFound();
    }

    #[Test]
    public function l_etat_vide_s_affiche_sans_bien_disponible(): void
    {
        $res = $this->get(route('vitrine.home', $this->agence->slug));

        // Texte statique du template : l'apostrophe n'est pas HTML-échappée,
        // donc on compare sans échappement (escape = false).
        $res->assertOk()
            ->assertSee('Aucun bien disponible pour l\'instant', false);
    }

    #[Test]
    public function les_annees_d_activite_sont_un_entier(): void
    {
        // Carbon 3 : diffInYears() est un float (2.5) — la vitrine doit afficher « 2 ans ».
        $this->agence->created_at = now()->subYears(2)->subMonths(6);
        $this->agence->save();
        $this->bienDispo();

        $res = $this->get(route('vitrine.home', $this->agence->slug));

        $res->assertOk()
            ->assertSee('2 ans à votre service')
            ->assertDontSee('2.5');
    }

    #[Test]
    public function les_metas_echappent_les_donnees_de_l_agence(): void
    {
        // Un slogan piégé ne doit jamais ressortir en HTML brut (XSS stocké public).
        $this->agence->slogan = 'Piège"><script>document.title=1</script>';
        $this->agence->save();
        $this->bienDispo();

        $res = $this->get(route('vitrine.home', $this->agence->slug));

        $res->assertOk()
            ->assertDontSee('"><script>document.title=1</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }
}
