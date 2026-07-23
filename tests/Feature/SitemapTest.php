<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\BienPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * SitemapTest — /sitemap.xml doit servir un XML valide pointant vers les pages
 * PUBLIQUES réelles (marketing + vitrines par agence + fiches biens), et NON vers
 * l'ancien portail central retiré.
 */
class SitemapTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function le_sitemap_est_un_xml_valide_qui_reference_les_vitrines(): void
    {
        $agence = Agency::factory()->create(['actif' => true]);
        $proprio = User::factory()->createOne(['role' => 'proprietaire', 'agency_id' => $agence->id]);

        $bien = Bien::factory()->create([
            'agency_id'       => $agence->id,
            'proprietaire_id' => $proprio->id,
            'statut'          => 'disponible',
            'visible_portail' => true,
            'titre'           => 'Villa Sitemap',
            'quartier'        => 'Ngor',
        ]);
        BienPhoto::create([
            'bien_id' => $bien->id, 'chemin' => 'biens/'.$bien->id.'/p.jpg',
            'est_principale' => true, 'ordre' => 1,
        ]);

        $res = $this->get('/sitemap.xml');

        $res->assertOk();
        $this->assertStringContainsString('application/xml', $res->headers->get('Content-Type'));

        // Contient la vitrine de l'agence + la fiche bien en vitrine…
        $res->assertSee(route('vitrine.home', $agence->slug), false);
        $res->assertSee(route('vitrine.bien', [$agence->slug, $bien->fresh()->slug]), false);

        // …et NE référence plus l'ancien portail central (/biens).
        $res->assertDontSee('<loc>'.url('/biens').'</loc>', false);
    }

    #[Test]
    public function les_routes_du_portail_central_nexistent_plus(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('portail.index'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('portail.home'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('portail.show'));
    }
}
