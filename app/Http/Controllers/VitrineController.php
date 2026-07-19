<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Bien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * VitrineController — site vitrine public généré automatiquement pour une agence.
 *
 * Un seul système (contrôleur + vues) réutilisé pour toutes les agences : l'agence
 * est identifiée par son {slug} dans l'URL. Aucune donnée n'est ressaisie — tout est
 * dérivé des biens et du profil déjà saisis dans l'espace Bimmo.
 *
 * Remplace l'ancienne page agence du portail central (route portail.agence).
 * Le portail central agrégé (/portail, /biens) reste séparé (BimoPortail v1).
 */
class VitrineController extends Controller
{
    /** Nombre de biens affichés par lot dans le catalogue (« Voir plus »). */
    private const PAR_LOT = 9;

    /** Nombre maximum de biens mis en avant simultanément. */
    private const MAX_VEDETTES = 3;

    // ── Page d'accueil de la vitrine ──────────────────────────────────────

    public function home(Request $request, string $slug): View
    {
        $agence = $this->agenceActive($slug);

        // Tous les biens réellement affichables de l'agence, en une requête.
        // portail() filtre (disponible + visible + sans contrat actif + photo…)
        // et charge la photo principale seule — suffisant ici : les cartes
        // n'affichent que la couverture (pas la galerie complète).
        $disponibles = Bien::portail()
            ->where('agency_id', $agence->id)
            ->orderByDesc('created_at')
            ->get();

        // ── Biens en vedette (filet : 2 récents si aucun coché) ────────────
        $vedettes = $this->vedettes($disponibles);

        // ── Explorer par quartier : quartiers réels + comptage + visuel ────
        $quartiers = $disponibles
            ->groupBy('quartier')
            ->map(fn (Collection $biens, string $nom) => [
                'nom'    => $nom,
                'nb'     => $biens->count(),
                'photo'  => $biens->firstWhere(fn (Bien $b) => $b->photo_couverture)?->photo_couverture,
            ])
            ->sortByDesc('nb')
            ->take(8)
            ->values();

        // ── Filtres du catalogue (par type de bien — le modèle est locatif) ─
        $typeActif = $request->filled('type') && array_key_exists($request->input('type'), Bien::TYPES)
            ? $request->input('type')
            : null;

        // Pastilles de filtre : uniquement les types réellement présents.
        $typesPresents = $disponibles
            ->groupBy('type')
            ->map(fn (Collection $b, string $t) => ['type' => $t, 'label' => Bien::TYPES[$t] ?? $t, 'nb' => $b->count()])
            ->sortByDesc('nb')
            ->values();

        // Filtres de la barre de recherche (quartier / budget) + pastille type.
        $catalogueFiltre = $disponibles
            ->when($typeActif, fn (Collection $c) => $c->where('type', $typeActif))
            ->when($request->filled('quartier'), fn (Collection $c) => $c->filter(
                fn (Bien $b) => $b->quartier && str_contains(mb_strtolower($b->quartier), mb_strtolower($request->input('quartier')))
            ))
            ->when($request->filled('budget'), fn (Collection $c) => $c->filter(
                fn (Bien $b) => (float) $b->loyer_mensuel <= (float) $request->input('budget')
            ))
            ->values();

        // ── « Voir plus » sans JS : limite cumulative via ?n= ──────────────
        $affichage      = max(self::PAR_LOT, (int) $request->input('n', self::PAR_LOT));
        $totalCatalogue = $catalogueFiltre->count();
        $catalogue      = $catalogueFiltre->take($affichage);
        $resteAAfficher = max(0, $totalCatalogue - $catalogue->count());

        // Lien « Voir plus » : conserve les filtres, augmente n, ancre #catalogue.
        $lienVoirPlus = $resteAAfficher > 0
            ? route('vitrine.home', array_merge($request->query(), [
                'slug' => $agence->slug,
                'n'    => $affichage + self::PAR_LOT,
            ])) . '#catalogue'
            : null;

        // ── Stats de l'agence (aucun chiffre inventé) ──────────────────────
        $stats = [
            // Carbon 3 : diffInYears() renvoie un float → cast entier (pas de « 1.5 ans »).
            'annees'      => max(1, (int) $agence->created_at->diffInYears(now())),
            'geres_total' => $agence->nbUnitesActives(),
            'disponibles' => $disponibles->count(),
        ];

        $heroBien  = $vedettes->first() ?? $disponibles->first();

        return view('vitrine.home', [
            'agence'          => $agence,
            'vedettes'        => $vedettes,
            'quartiers'       => $quartiers,
            'typesPresents'   => $typesPresents,
            'typeActif'       => $typeActif,
            'catalogue'       => $catalogue,
            'totalCatalogue'  => $totalCatalogue,
            'totalDisponibles'=> $disponibles->count(),
            'resteAAfficher'  => $resteAAfficher,
            'lienVoirPlus'    => $lienVoirPlus,
            'stats'           => $stats,
            'heroBien'        => $heroBien,
            'whatsappUrl'     => $this->whatsappUrl($agence),
        ]);
    }

    // ── Fiche détail d'un bien ────────────────────────────────────────────

    public function bien(string $slug, string $bienSlug): View|RedirectResponse
    {
        $agence = $this->agenceActive($slug);

        $bien = Bien::portail()
            ->where('agency_id', $agence->id)
            ->where('slug', $bienSlug)
            ->with('photos')
            ->first();

        // Bien inexistant, ou plus disponible (loué entre-temps) → retour à la vitrine.
        if (! $bien) {
            return redirect()->route('vitrine.home', $agence->slug);
        }

        $texte = 'Bonjour, je suis intéressé(e) par le bien : '
            . $bien->titre_fallback . ', ' . $bien->quartier . ' (' . $agence->name . ').';

        // Autres biens de l'agence (hors celui-ci) pour la section « à voir aussi ».
        $autres = Bien::portail()
            ->where('agency_id', $agence->id)
            ->where('id', '!=', $bien->id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('vitrine.bien', [
            'agence'      => $agence,
            'bien'        => $bien,
            'photos'      => $bien->photos->sortByDesc('est_principale')->values(),
            'autres'      => $autres,
            'whatsappUrl' => $this->whatsappUrl($agence, $texte),
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /** Agence active par slug, hors périmètre Super Admin (contexte public). */
    private function agenceActive(string $slug): Agency
    {
        return Agency::sansPerimetre()
            ->where('actif', true)
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Biens en vedette avec filet de sécurité : les biens cochés (max 3), ou à
     * défaut les 2 biens disponibles les plus récents — la section n'est jamais vide.
     */
    private function vedettes(Collection $disponibles): Collection
    {
        $cochees = $disponibles->where('est_en_vedette', true)->take(self::MAX_VEDETTES)->values();

        return $cochees->isNotEmpty()
            ? $cochees
            : $disponibles->take(2)->values();
    }

    /** Lien wa.me vers le numéro vitrine de l'agence (whatsapp || téléphone). */
    private function whatsappUrl(Agency $agence, ?string $texte = null): ?string
    {
        $numero = preg_replace('/[^0-9]/', '', $agence->whatsapp ?? $agence->telephone ?? '');

        if (! $numero) {
            return null;
        }

        return 'https://wa.me/' . $numero . ($texte ? '?text=' . rawurlencode($texte) : '');
    }
}
