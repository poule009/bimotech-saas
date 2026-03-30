<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\Paiement;

class BienController extends Controller
{
    use AuthorizesRequests;

    // ── Liste des biens ───────────────────────────────────────────────────
    public function index()
    {
        // BUG 4 FIX : authorize() manquant — défense en profondeur cohérente
        // avec les autres méthodes du contrôleur.
        $this->authorize('viewAny', Bien::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $biens = $user->isAdmin()
            ? Bien::with('proprietaire')->orderByDesc('created_at')->paginate(12)
            : Bien::where('proprietaire_id', $user->id)
                  ->with('proprietaire')
                  ->orderByDesc('created_at')
                  ->paginate(12);

        return view('biens.index', compact('biens'));
    }

    // ── Formulaire création ───────────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Bien::class);

        // Admin peut choisir le proprio, proprio est forcé à lui-même
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $proprietaires = $user->isAdmin()
            ? User::where('role', 'proprietaire')->orderBy('name')->get()
            : collect([$user]);

        return view('biens.create', compact('proprietaires'));
    }

    // ── Enregistrement ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorize('create', Bien::class);

        $agencyId = Auth::user()->agency_id;

        $validated = $request->validate([
            // BUG 7 FIX : validation cross-agence — le proprietaire_id doit
            // appartenir à la même agence que l'admin connecté.
            'proprietaire_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($agencyId) {
                    $proprietaire = User::find($value);
                    if (! $proprietaire || $proprietaire->agency_id !== $agencyId) {
                        $fail('Ce propriétaire n\'appartient pas à votre agence.');
                    }
                },
            ],
            'type'            => ['required', 'string', 'max:100'],
            'adresse'         => ['required', 'string', 'max:255'],
            'ville'           => ['required', 'string', 'max:100'],
            'surface_m2'      => ['nullable', 'integer', 'min:1'],
            'nombre_pieces'   => ['nullable', 'integer', 'min:1'],
            'loyer_mensuel'   => ['required', 'numeric', 'min:1'],
            'taux_commission' => ['required', 'numeric', 'min:1', 'max:20'],
            'statut'          => ['required', 'in:disponible,loue,en_travaux'],
            'description'     => ['nullable', 'string', 'max:1000'],
        ]);

        // Génération automatique de la référence
        $count     = Bien::count() + 1;
        $reference = 'BIEN-' . now()->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $bien = Bien::create(array_merge($validated, [
            'reference' => $reference,
        ]));

        return redirect()
            ->route('biens.show', $bien)
            ->with('success', "Bien {$reference} créé avec succès ✓");
    }

    // ── Détail d'un bien ──────────────────────────────────────────────────
    public function show(Bien $bien)
{
    $this->authorize('view', $bien);

    $bien->load('proprietaire', 'photos', 'contrats.locataire');

    $contratActif = $bien->contratActif;

    $totalEncaisse = Paiement::whereIn(
        'contrat_id', $bien->contrats->pluck('id')
    )->where('statut', 'valide')->sum('montant_encaisse');

    $paiements = Paiement::whereIn('contrat_id', $bien->contrats->pluck('id'))
        ->with('contrat.locataire')
        ->orderByDesc('periode')
        ->paginate(10);

    return view('biens.show', compact(
        'bien', 'contratActif', 'totalEncaisse', 'paiements'
    ));
}
    // ── Formulaire édition ────────────────────────────────────────────────
    public function edit(Bien $bien)
    {
        $this->authorize('update', $bien);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $proprietaires = $user->isAdmin()
            ? User::where('role', 'proprietaire')->orderBy('name')->get()
            : collect([$user]);

        return view('biens.edit', compact('bien', 'proprietaires'));
    }

    // ── Mise à jour ───────────────────────────────────────────────────────
    public function update(Request $request, Bien $bien)
    {
        $this->authorize('update', $bien);

        $validated = $request->validate([
            'type'            => ['required', 'string', 'max:100'],
            'adresse'         => ['required', 'string', 'max:255'],
            'ville'           => ['required', 'string', 'max:100'],
            'surface_m2'      => ['nullable', 'integer', 'min:1'],
            'nombre_pieces'   => ['nullable', 'integer', 'min:1'],
            'loyer_mensuel'   => ['required', 'numeric', 'min:1'],
            'taux_commission' => ['required', 'numeric', 'min:1', 'max:20'],
            'statut'          => ['required', 'in:disponible,loue,en_travaux'],
            'description'     => ['nullable', 'string', 'max:1000'],
        ]);

        $bien->update($validated);

        return redirect()
            ->route('biens.show', $bien)
            ->with('success', 'Bien mis à jour ✓');
    }

    // ── Suppression ───────────────────────────────────────────────────────
    public function destroy(Bien $bien)
    {
        $this->authorize('delete', $bien);

        if ($bien->contrats()->where('statut', 'actif')->exists()) {
            return back()->withErrors([
                'general' => 'Impossible de supprimer un bien avec un contrat actif.'
            ]);
        }

        $bien->delete();

        return redirect()
            ->route('biens.index')
            ->with('success', 'Bien supprimé ✓');
    }
}