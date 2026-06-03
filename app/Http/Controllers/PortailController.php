<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Bien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PortailController extends Controller
{
    public function home(): View
    {
        $nouveaux = Bien::portail()
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $quartiers = Bien::withoutGlobalScope(\App\Models\Scopes\AgencyScope::class)
            ->selectRaw('quartier, COUNT(*) as nb_biens')
            ->where('statut', 'disponible')
            ->where('visible_portail', true)
            ->whereHas('agency', fn($q) => $q->where('actif', true))
            ->whereNotNull('quartier')
            ->where('quartier', '!=', '')
            ->groupBy('quartier')
            ->orderByDesc('nb_biens')
            ->limit(8)
            ->get();

        // Cache 10 min — stats affichées en vitrine, pas critiques au temps réel
        [$nbBiens, $nbAgences, $nbVilles] = Cache::remember('portail_stats', 600, fn() => [
            Bien::portail()->count(),
            Agency::where('actif', true)->count(),
            Bien::portail()->distinct('ville')->count('ville'),
        ]);

        return view('portail.home', compact('nouveaux', 'quartiers', 'nbBiens', 'nbAgences', 'nbVilles'));
    }

    public function index(Request $request): View
    {
        $query = Bien::portail();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('quartier')) {
            $query->where('quartier', 'like', '%' . $request->quartier . '%');
        }
        if ($request->filled('prix_max')) {
            $query->where('loyer_mensuel', '<=', (int) $request->prix_max);
        }
        if ($request->boolean('meuble')) {
            $query->where('meuble', true);
        }
        if ($request->filled('chambres')) {
            $query->where('nombre_chambres', '>=', (int) $request->chambres);
        }
        if ($request->filled('agence')) {
            $query->whereHas('agency', fn($q) => $q->where('slug', $request->agence));
        }

        $biens = $query->orderByDesc('created_at')->paginate(24)->withQueryString();

        $agenceFiltree = $request->filled('agence')
            ? Agency::where('slug', $request->agence)->first(['id', 'name', 'slug', 'logo_path'])
            : null;

        return view('portail.index', compact('biens', 'agenceFiltree'));
    }

    public function agence(string $slug): View
    {
        $agence = Agency::where('actif', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $biens = Bien::portail()
            ->where('agency_id', $agence->id)
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $nbBiens = $biens->total();

        return view('portail.agence', compact('agence', 'biens', 'nbBiens'));
    }

    public function quartier(string $quartier): View
    {
        if (!Bien::portail()->where('quartier', $quartier)->exists()) {
            abort(404);
        }

        $biens = Bien::portail()
            ->where('quartier', $quartier)
            ->when(request()->filled('type'),     fn($q) => $q->where('type', request('type')))
            ->when(request()->filled('chambres'), fn($q) => $q->where('nombre_chambres', '>=', (int) request('chambres')))
            ->when(request()->filled('prix_max'), fn($q) => $q->where('loyer_mensuel', '<=', (int) request('prix_max')))
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $similaires = Bien::withoutGlobalScope(\App\Models\Scopes\AgencyScope::class)
            ->selectRaw('quartier, COUNT(*) as nb_biens')
            ->where('statut', 'disponible')
            ->where('visible_portail', true)
            ->whereHas('agency', fn($q) => $q->where('actif', true))
            ->whereNotNull('quartier')
            ->where('quartier', '!=', '')
            ->where('quartier', '!=', $quartier)
            ->groupBy('quartier')
            ->orderByDesc('nb_biens')
            ->limit(4)
            ->get();

        return view('portail.quartier', compact('biens', 'quartier', 'similaires'));
    }

    public function show(string $slug): View
    {
        $bien = Bien::withoutAgencyScope()
            ->withTrashed()
            ->where('slug', $slug)
            ->with(['photos', 'agency'])
            ->first();

        if (! $bien) {
            abort(404);
        }

        $estDisponible = ! $bien->trashed()
            && $bien->statut === 'disponible'
            && $bien->visible_portail
            && ! $bien->contratActif          // lazy load OK — page publique, bien unique
            && $bien->agency?->actif
            && ! empty($bien->titre)
            && ! empty($bien->quartier)
            && $bien->photos->where('est_principale', true)->isNotEmpty();

        if (! $estDisponible) {
            return view('portail.bien-indisponible', compact('bien'));
        }

        $numero      = preg_replace('/[^0-9]/', '', $bien->agency->whatsapp ?? $bien->agency->telephone ?? '');
        $message     = urlencode(
            'Bonjour, je suis intéressé(e) par votre bien : '
            . ($bien->titre ?? ($bien->type_label . ' — ' . $bien->quartier))
            . ', ' . $bien->ville
            . '. Référence : ' . $bien->reference
        );
        $whatsappUrl     = $numero ? 'https://wa.me/' . $numero . '?text=' . $message : null;
        $photoPrincipale = $bien->photos->where('est_principale', true)->first();

        return view('portail.show', compact('bien', 'whatsappUrl', 'photoPrincipale'));
    }
}
