<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Bien;
use Illuminate\Http\Response;

/**
 * SitemapController — sitemap.xml des pages PUBLIQUES réellement servies :
 *  - pages marketing (accueil, tarifs, démo, FAQ, contact),
 *  - vitrines par agence (/agences/{slug}) — BimoPortail v2,
 *  - fiches biens en vitrine (/agences/{slug}/biens/{bienSlug}).
 *
 * Le portail central agrégé (/biens) a été retiré : ses URLs ne sont plus
 * annoncées (elles renvoyaient 500 depuis la refonte front).
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        // Uniquement des pages VIVANTES : les vues contact/demo/faq/pricing ont été
        // supprimées à la refonte (reconstruction à la demande) — ne pas annoncer
        // à Google des URLs qui répondent 500.
        $urls = [
            ['loc' => url('/'),        'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => route('tarifs'), 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];

        try {
            // Une entrée par vitrine d'agence active.
            foreach (Agency::where('actif', true)->get(['id', 'slug']) as $agence) {
                if (! $agence->slug) {
                    continue;
                }
                $urls[] = [
                    'loc'        => route('vitrine.home', $agence->slug),
                    'priority'   => '0.8',
                    'changefreq' => 'daily',
                ];
            }

            // Une entrée par bien réellement affichable en vitrine.
            $biens = Bien::portail()
                ->with('agency:id,slug')
                ->select(['id', 'slug', 'agency_id', 'updated_at'])
                ->orderByDesc('updated_at')
                ->get();

            foreach ($biens as $bien) {
                if (! $bien->slug || ! $bien->agency?->slug) {
                    continue;
                }
                $urls[] = [
                    'loc'        => route('vitrine.bien', [$bien->agency->slug, $bien->slug]),
                    'lastmod'    => $bien->updated_at?->toAtomString(),
                    'priority'   => '0.7',
                    'changefreq' => 'weekly',
                ];
            }
        } catch (\Throwable) {
            // Table absente / DB indisponible : on sert au moins les pages statiques.
        }

        $content = view('sitemap', compact('urls'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}
