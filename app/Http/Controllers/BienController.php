<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BienController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('isStaff');

        $query = Bien::with([
            'proprietaire:id,name,email',
            'contratActif.locataire:id,name,telephone',
        ]);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $biens = $query->latest()->paginate(12)->withQueryString();

        return view('biens.index', compact('biens'));
    }

    public function show(Bien $bien): View
    {
        $this->authorize('isStaff');

        $bien->load([
            'proprietaire:id,name,email,telephone,adresse',
            'contratActif.locataire:id,name,email,telephone',
            'contrats' => fn($q) => $q->latest()->limit(5)->with('locataire:id,name'),
            'photos',
        ]);

        return view('biens.show', compact('bien'));
    }

    public function create(): View
    {
        $this->authorize('isStaff');

        $proprietaires = User::where('role', 'proprietaire')
            ->where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('biens.create', compact('proprietaires'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('isStaff');

        $validated = $request->validate([
            'proprietaire_id' => ['required', 'exists:users,id'],
            'type'            => ['required', 'in:Appartement,Villa,Studio,Bureau,Commerce,Terrain'],
            'adresse'         => ['required', 'string', 'max:255'],
            'quartier'        => ['nullable', 'string', 'max:100'],
            'commune'         => ['nullable', 'string', 'max:100'],
            'ville'           => ['required', 'string', 'max:100'],
            'surface_m2'      => ['nullable', 'numeric', 'min:1'],
            'nombre_pieces'   => ['nullable', 'integer', 'min:1'],
            'loyer_mensuel'   => ['required', 'numeric', 'min:0'],
            'taux_commission' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'meuble'          => ['nullable', 'boolean'],
            'description'     => ['nullable', 'string'],
        ], [
            'proprietaire_id.required' => 'Veuillez sélectionner un propriétaire.',
            'type.required'            => 'Le type de bien est obligatoire.',
            'adresse.required'         => "L'adresse est obligatoire.",
            'ville.required'           => 'La ville est obligatoire.',
            'loyer_mensuel.required'   => 'Le loyer est obligatoire.',
        ]);

        $agencyId = Auth::user()->agency_id;

        // Vérifier que le propriétaire appartient à l'agence courante
        $proprioValide = \App\Models\User::where('id', $validated['proprietaire_id'])
            ->where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->exists();

        if (! $proprioValide) {
            return back()
                ->withErrors(['proprietaire_id' => 'Ce propriétaire n\'appartient pas à votre agence.'])
                ->withInput();
        }

        $validated['agency_id']      = $agencyId;
        $validated['statut']         = 'disponible';
        $validated['reference']      = $this->genererReference();
        $validated['meuble']         = $request->boolean('meuble');
        $validated['taux_commission'] = $validated['taux_commission'] ?? 10;

        $bien = Bien::create($validated);
        // Enregistrement des photos si présentes
if ($request->hasFile('photos')) {
    foreach ($request->file('photos') as $index => $photo) {
        $chemin = $photo->store('biens', 'public');
        \App\Models\BienPhoto::create([
            'bien_id'      => $bien->id,
            'chemin'       => $chemin,
            'nom_original' => $photo->getClientOriginalName(),
            'est_principale' => $index === 0,
            'ordre'        => $index,
        ]);
    }
}

        return redirect()
            ->route('admin.biens.show', $bien)
            ->with('success', 'Bien créé avec succès.');
    }

    public function edit(Bien $bien): View
    {
        $this->authorize('isStaff');

        $proprietaires = User::where('role', 'proprietaire')
            ->where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('biens.edit', compact('bien', 'proprietaires'));
    }

    public function update(Request $request, Bien $bien): RedirectResponse
    {
        $this->authorize('isStaff');

        $validated = $request->validate([
            'proprietaire_id' => ['required', 'exists:users,id'],
            'type'            => ['required', 'in:Appartement,Villa,Studio,Bureau,Commerce,Terrain'],
            'adresse'         => ['required', 'string', 'max:255'],
            'quartier'        => ['nullable', 'string', 'max:100'],
            'commune'         => ['nullable', 'string', 'max:100'],
            'ville'           => ['required', 'string', 'max:100'],
            'surface_m2'      => ['nullable', 'numeric', 'min:1'],
            'nombre_pieces'   => ['nullable', 'integer', 'min:1'],
            'loyer_mensuel'   => ['required', 'numeric', 'min:0'],
            'taux_commission' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'meuble'          => ['nullable', 'boolean'],
            'statut'          => ['required', 'in:disponible,loue,en_travaux,archive'],
            'description'     => ['nullable', 'string'],
        ]);

        $validated['meuble'] = $request->boolean('meuble');
        $bien->update($validated);

        return redirect()
            ->route('admin.biens.show', $bien)
            ->with('success', 'Bien mis à jour avec succès.');
    }

    public function destroy(Bien $bien): RedirectResponse
    {
        $this->authorize('isStaff');

        if ($bien->contratActif) {
            return back()->withErrors([
                'general' => 'Impossible de supprimer un bien avec un contrat actif.',
            ]);
        }

        $bien->delete();

        return redirect()
            ->route('admin.biens.index')
            ->with('success', 'Bien archivé avec succès.');
    }

    private function genererReference(): string
    {
        $agencyId = str_pad(Auth::user()->agency_id, 2, '0', STR_PAD_LEFT);
        $count    = Bien::withoutGlobalScope(\App\Models\Scopes\AgencyScope::class)
            ->where('agency_id', Auth::user()->agency_id)
            ->withTrashed()
            ->count() + 1;

        return 'BT-AG' . $agencyId . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}