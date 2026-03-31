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
        // CORRECTION : filtrer par agency_id pour éviter fuite inter-agences
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $proprietaires = $user->isAdmin()
            ? User::where('role', 'proprietaire')
                  ->where('agency_id', $user->agency_id)
                  ->orderBy('name')->get()
            : collect([$user]);

        return view('biens.create', compact('proprietaires'));
    }

    // ── Enregistrement ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorize('create', Bien::class);

        $agencyId = Auth::user()->agency_id;

        $validated = $request->validate([
            // Validation cross-agence — le proprietaire_id doit appartenir à la même agence
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
            'quartier'        => ['nullable', 'string', 'max:100'],
            'commune'         => ['nullable', 'string', 'max:100'],
            'surface_m2'      => ['nullable', 'integer', 'min:1'],
            'nombre_pieces'   => ['nullable', 'integer', 'min:1'],
            'loyer_mensuel'   => ['required', 'numeric', 'min:1'],
            'taux_commission' => ['required', 'numeric', 'min:1', 'max:20'],
            'statut'          => ['required', 'in:disponible,loue,en_travaux'],
            'meuble'          => ['boolean'],
            'description'     => ['nullable', 'string', 'max:1000'],
        ]);

        // CORRECTION P0 : Référence unique par agence — utilise le count filtré
        // par agency_id + un suffixe aléatoire pour éviter les collisions
        $countAgence = Bien::withoutGlobalScopes()->where('agency_id', $agencyId)->count() + 1;
        $reference   = sprintf(
            'BIEN-%s-%s-%s',
            now()->year,
            str_pad($countAgence, 4, '0', STR_PAD_LEFT),
            strtoupper(substr(uniqid(), -4))
        );

        $bien = Bien::create(array_merge($validated, [
            'reference' => $reference,
            'meuble'    => $request->boolean('meuble'),
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
        // CORRECTION : filtrer par agency_id
        $proprietaires = $user->isAdmin()
            ? User::where('role', 'proprietaire')
                  ->where('agency_id', $user->agency_id)
                  ->orderBy('name')->get()
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
            'quartier'        => ['nullable', 'string', 'max:100'],
            'commune'         => ['nullable', 'string', 'max:100'],
            'surface_m2'      => ['nullable', 'integer', 'min:1'],
            'nombre_pieces'   => ['nullable', 'integer', 'min:1'],
            'loyer_mensuel'   => ['required', 'numeric', 'min:1'],
            'taux_commission' => ['required', 'numeric', 'min:1', 'max:20'],
            'statut'          => ['required', 'in:disponible,loue,en_travaux'],
            'meuble'          => ['boolean'],
            'description'     => ['nullable', 'string', 'max:1000'],
        ]);

        $bien->update(array_merge($validated, [
            'meuble' => $request->boolean('meuble'),
        ]));

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