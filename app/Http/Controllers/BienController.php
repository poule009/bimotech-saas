<?php

namespace App\Http\Controllers;

use App\Models\Bien;
// use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BienController extends Controller
{
    use AuthorizesRequests;

    // ─────────────────────────────────────────────────────────────────────
    // LISTE
    // ─────────────────────────────────────────────────────────────────────

    public function index()
    {
        $this->authorize('viewAny', Bien::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = $user->isAdmin()
            ? Bien::query()
            : Bien::where('proprietaire_id', $user->id);

        /**
         * PERFORMANCE — select() sélectif :
         *
         * La liste n'affiche que référence, type, adresse, ville, statut,
         * loyer_mensuel et le nom du propriétaire. Charger description,
         * surface_m2, nombre_pieces, etc. est inutile pour une liste paginée.
         *
         * Colonnes conservées : celles affichées dans la vue + les FK nécessaires
         * pour les relations (agency_id pour AgencyScope, proprietaire_id pour with).
         */
        $biens = $query
            ->select([
                'id', 'agency_id', 'proprietaire_id',
                'reference', 'type', 'adresse', 'ville', 'quartier',
                'loyer_mensuel', 'taux_commission', 'statut', 'meuble',
                'created_at',
            ])
            ->with([
                // EAGER LOADING — on sélectionne uniquement les colonnes nécessaires
                'proprietaire:id,name,telephone',
                // withCount évite de charger toute la relation pour compter
                'contratActif:id,bien_id,statut,loyer_contractuel',
            ])
            ->withCount('contrats')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('biens.index', compact('biens'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // ─────────────────────────────────────────────────────────────────────

    public function create()
    {
        $this->authorize('create', Bien::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // select() — pour le dropdown, on n'a besoin que de id et name
        $proprietaires = $user->isAdmin()
            ? User::where('role', 'proprietaire')
                  ->where('agency_id', $user->agency_id)
                  ->select(['id', 'name', 'telephone'])
                  ->orderBy('name')
                  ->get()
            : collect([$user]);

        return view('biens.create', compact('proprietaires'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // ENREGISTREMENT
    // ─────────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Bien::class);

        $agencyId = Auth::user()->agency_id;

        $validated = $request->validate([
            'proprietaire_id' => [
                'required', 'exists:users,id',
                function ($attr, $value, $fail) use ($agencyId) {
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
            ->with('success', "Bien {$reference} créé ✓");
    }

    // ─────────────────────────────────────────────────────────────────────
    // DÉTAIL
    // ─────────────────────────────────────────────────────────────────────

    public function show(Bien $bien)
    {
        $this->authorize('view', $bien);

        // Eager load complet pour la fiche détaillée — ici on veut tout
        $bien->load('proprietaire', 'photos', 'contratActif.locataire');

        /**
         * PERFORMANCE — Agrégat SQL au lieu de whereIn + sum en PHP.
         *
         * On utilise l'ID du contrat actif s'il existe,
         * sinon on cherche via tous les contrats du bien.
         */
        $contratIds = $bien->contrats()->pluck('id');

        $totalEncaisse = Paiement::whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->sum('montant_encaisse'); // SQL SUM — pas de get() en mémoire

        $paiements = Paiement::whereIn('contrat_id', $contratIds)
            ->select([
                'id', 'contrat_id', 'agency_id', 'periode',
                'montant_encaisse', 'net_proprietaire', 'commission_ttc',
                'mode_paiement', 'date_paiement', 'statut', 'reference_paiement',
            ])
            ->with(['contrat:id,bien_id,locataire_id', 'contrat.locataire:id,name'])
            ->where('statut', 'valide')
            ->orderByDesc('periode')
            ->paginate(10);

        return view('biens.show', compact('bien', 'totalEncaisse', 'paiements'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE ÉDITION
    // ─────────────────────────────────────────────────────────────────────

    public function edit(Bien $bien)
    {
        $this->authorize('update', $bien);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $proprietaires = $user->isAdmin()
            ? User::where('role', 'proprietaire')
                  ->where('agency_id', $user->agency_id)
                  ->select(['id', 'name'])
                  ->orderBy('name')
                  ->get()
            : collect([$user]);

        return view('biens.edit', compact('bien', 'proprietaires'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // MISE À JOUR
    // ─────────────────────────────────────────────────────────────────────

    public function update(Request $request, Bien $bien): RedirectResponse
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

        $bien->update(array_merge($validated, ['meuble' => $request->boolean('meuble')]));

        return redirect()->route('biens.show', $bien)->with('success', 'Bien mis à jour ✓');
    }

    // ─────────────────────────────────────────────────────────────────────
    // SUPPRESSION
    // ─────────────────────────────────────────────────────────────────────

    public function destroy(Bien $bien): RedirectResponse
    {
        $this->authorize('delete', $bien);

        if ($bien->contrats()->where('statut', 'actif')->exists()) {
            return back()->withErrors([
                'general' => 'Impossible de supprimer un bien avec un contrat actif.',
            ]);
        }

        $bien->delete();

        return redirect()->route('biens.index')->with('success', 'Bien supprimé ✓');
    }
}