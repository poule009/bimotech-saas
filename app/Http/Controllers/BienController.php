<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * BienController — CRUD des biens immobiliers.
 *
 * Principes appliqués :
 *  1. $this->authorize() en première ligne de CHAQUE méthode
 *  2. Eager Loading systématique pour éviter les N+1
 *  3. Aucune logique métier ici → délégué aux Services (étape 2 de la roadmap)
 *  4. L'AgencyScope filtre automatiquement par agence → pas de ->where('agency_id', ...)
 */
class BienController extends Controller
{
    /**
     * Liste des biens de l'agence.
     * GET /biens
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Bien::class);

        $biens = Bien::with(['proprietaire', 'contratActif.locataire'])
            ->when($request->statut, fn($q, $s) => $q->where('statut', $s))
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(12);

        return view('biens.index', compact('biens'));
    }

    /**
     * Détail d'un bien.
     * GET /biens/{bien}
     */
    public function show(Bien $bien): View
    {
        // L'AgencyScope garantit que $bien appartient à l'agence (404 sinon).
        // La Policy affine selon le rôle (proprietaire, locataire...).
        $this->authorize('view', $bien);

        $bien->load([
            'proprietaire',
            'contratActif.locataire',
            'contrats' => fn($q) => $q->latest()->limit(5),
            'paiements' => fn($q) => $q->latest()->limit(10),
        ]);

        return view('biens.show', compact('bien'));
    }

    /**
     * Formulaire de création.
     * GET /biens/create
     */
    public function create(): View
    {
        $this->authorize('create', Bien::class);

        // Eager load : uniquement les propriétaires de l'agence (AgencyScope actif)
        $proprietaires = Proprietaire::orderBy('nom')->get(['id', 'nom', 'prenom']);

        return view('biens.create', compact('proprietaires'));
    }

    /**
     * Enregistrement d'un nouveau bien.
     * POST /biens
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Bien::class);

        $validated = $request->validate([
            'proprietaire_id'    => ['required', 'exists:proprietaires,id'],
            'type'               => ['required', 'in:appartement,villa,bureau,commerce,terrain'],
            'titre'              => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'adresse'            => ['required', 'string'],
            'quartier'           => ['required', 'string', 'max:100'],
            'ville'              => ['required', 'string', 'max:100'],
            'surface'            => ['required', 'numeric', 'min:1'],
            'nb_pieces'          => ['nullable', 'integer', 'min:1'],
            'loyer_hors_charges' => ['required', 'numeric', 'min:0'],
            'charges'            => ['nullable', 'numeric', 'min:0'],
            'depot_garantie'     => ['nullable', 'numeric', 'min:0'],
            'meuble'             => ['boolean'],
        ]);

        // Injection de l'agency_id depuis l'utilisateur connecté (jamais depuis le form)
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        $validated['agency_id'] = $authUser->agency_id;
        $validated['statut']    = 'disponible';
        $validated['reference'] = $this->generateReference();

        $bien = Bien::create($validated);

        return redirect()
            ->route('biens.show', $bien)
            ->with('success', 'Bien créé avec succès.');
    }

    /**
     * Formulaire de modification.
     * GET /biens/{bien}/edit
     */
    public function edit(Bien $bien): View
    {
        $this->authorize('update', $bien);

        $proprietaires = Proprietaire::orderBy('nom')->get(['id', 'nom', 'prenom']);

        return view('biens.edit', compact('bien', 'proprietaires'));
    }

    /**
     * Mise à jour d'un bien.
     * PUT /biens/{bien}
     */
    public function update(Request $request, Bien $bien): RedirectResponse
    {
        $this->authorize('update', $bien);

        $validated = $request->validate([
            'proprietaire_id'    => ['required', 'exists:proprietaires,id'],
            'type'               => ['required', 'in:appartement,villa,bureau,commerce,terrain'],
            'titre'              => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'adresse'            => ['required', 'string'],
            'quartier'           => ['required', 'string', 'max:100'],
            'ville'              => ['required', 'string', 'max:100'],
            'surface'            => ['required', 'numeric', 'min:1'],
            'nb_pieces'          => ['nullable', 'integer', 'min:1'],
            'loyer_hors_charges' => ['required', 'numeric', 'min:0'],
            'charges'            => ['nullable', 'numeric', 'min:0'],
            'depot_garantie'     => ['nullable', 'numeric', 'min:0'],
            'meuble'             => ['boolean'],
        ]);

        $bien->update($validated);

        return redirect()
            ->route('biens.show', $bien)
            ->with('success', 'Bien mis à jour avec succès.');
    }

    /**
     * Suppression (soft delete) d'un bien.
     * DELETE /biens/{bien}
     */
    public function destroy(Bien $bien): RedirectResponse
    {
        $this->authorize('delete', $bien);

        $bien->delete(); // SoftDelete → bien archivé, pas perdu

        return redirect()
            ->route('biens.index')
            ->with('success', 'Bien archivé avec succès.');
    }

    // ─── Méthodes privées ──────────────────────────────────────────────────

    /**
     * Génère une référence unique pour un bien.
     * Format : BT-AGXX-YYYY (ex: BT-AG04-0023)
     */
    private function generateReference(): string
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        $agencyId = str_pad($authUser->agency_id, 2, '0', STR_PAD_LEFT);
        $count    = Bien::withoutGlobalScope(\App\Models\Scopes\AgencyScope::class)
            ->where('agency_id', $authUser->agency_id)
            ->withTrashed()
            ->count() + 1;

        return 'BT-AG' . $agencyId . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}