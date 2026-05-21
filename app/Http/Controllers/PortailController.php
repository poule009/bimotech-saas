<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Bien;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortailController extends Controller
{
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
        if ($request->filled('agence')) {
            $query->whereHas('agency', fn($q) => $q->where('slug', $request->agence));
        }

        $biens = $query->orderByDesc('created_at')->paginate(24)->withQueryString();

        $agenceFiltree = $request->filled('agence')
            ? Agency::where('slug', $request->agence)->first(['id', 'name', 'slug', 'logo_path'])
            : null;

        return view('portail.index', compact('biens', 'agenceFiltree'));
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
