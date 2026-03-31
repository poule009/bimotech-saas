<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ContratController extends Controller
{
    use AuthorizesRequests;

    // ── Liste des contrats ────────────────────────────────────────────────
    public function index()
    {
        $this->authorize('viewAny', Contrat::class);

        $contrats = Contrat::with('bien', 'locataire')
            ->orderByDesc('created_at')
            ->paginate(15);

        $stats = [
            'total'   => Contrat::count(),
            'actifs'  => Contrat::where('statut', 'actif')->count(),
            'resilies'=> Contrat::where('statut', 'resilié')->count(),
            'expires' => Contrat::where('statut', 'expiré')->count(),
        ];

        return view('admin.contrats.index', compact('contrats', 'stats'));
    }

    // ── Formulaire création ───────────────────────────────────────────────
    public function create(Request $request)
    {
        $this->authorize('create', Contrat::class);

        // Biens disponibles uniquement
        $biens = Bien::where('statut', 'disponible')
            ->with('proprietaire')
            ->orderBy('reference')
            ->get();

        // Locataires — filtrés par agence
        $locataires = User::where('role', 'locataire')
            ->where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get();

        // Pré-sélection si on vient d'une fiche bien
        $bienPreselectionne = $request->has('bien_id')
            ? Bien::find($request->bien_id)
            : null;

        return view('admin.contrats.create', compact(
            'biens', 'locataires', 'bienPreselectionne'
        ));
    }

    // ── Enregistrement ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorize('create', Contrat::class);

        $agencyId = Auth::user()->agency_id;

        $validated = $request->validate([
            // Validation cross-agence pour bien_id
            'bien_id' => [
                'required',
                'exists:biens,id',
                function ($attribute, $value, $fail) use ($agencyId) {
                    $bien = \App\Models\Bien::withoutGlobalScopes()->find($value);
                    if (! $bien || $bien->agency_id !== $agencyId) {
                        $fail('Ce bien n\'appartient pas à votre agence.');
                    }
                },
            ],
            // Validation cross-agence pour locataire_id
            'locataire_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($agencyId) {
                    $locataire = User::find($value);
                    if (! $locataire || $locataire->agency_id !== $agencyId || $locataire->role !== 'locataire') {
                        $fail('Ce locataire n\'appartient pas à votre agence.');
                    }
                },
            ],
            'date_debut'          => ['required', 'date'],
            'date_fin'            => ['nullable', 'date', 'after:date_debut'],
            'caution'             => ['required', 'numeric', 'min:0'],
            'type_bail'           => ['required', 'in:habitation,commercial,mixte,saisonnier'],
            'frais_agence'        => ['nullable', 'numeric', 'min:0'],
            'charges_mensuelles'  => ['nullable', 'numeric', 'min:0'],
            'indexation_annuelle' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'nombre_mois_caution' => ['nullable', 'integer', 'min:1', 'max:6'],
            'garant_nom'          => ['nullable', 'string', 'max:150'],
            'garant_telephone'    => ['nullable', 'string', 'max:20'],
            'garant_adresse'      => ['nullable', 'string', 'max:255'],
            'observations'        => ['nullable', 'string', 'max:1000'],
        ]);

        // Vérifier qu'il n'y a pas déjà un contrat actif sur ce bien
        $contratExistant = Contrat::where('bien_id', $validated['bien_id'])
            ->where('statut', 'actif')
            ->exists();

        if ($contratExistant) {
            return back()
                ->withInput()
                ->withErrors(['bien_id' => 'Ce bien a déjà un contrat actif.']);
        }

        // Récupère le loyer du bien
        $bien = Bien::findOrFail($validated['bien_id']);

        $contrat = Contrat::create([
            'bien_id'             => $validated['bien_id'],
            'locataire_id'        => $validated['locataire_id'],
            'date_debut'          => $validated['date_debut'],
            'date_fin'            => $validated['date_fin'] ?? null,
            'loyer_contractuel'   => $bien->loyer_mensuel,
            'caution'             => $validated['caution'],
            'statut'              => 'actif',
            'type_bail'           => $validated['type_bail'] ?? 'habitation',
            'frais_agence'        => $validated['frais_agence'] ?? 0,
            'charges_mensuelles'  => $validated['charges_mensuelles'] ?? 0,
            'indexation_annuelle' => $validated['indexation_annuelle'] ?? 0,
            'nombre_mois_caution' => $validated['nombre_mois_caution'] ?? 1,
            'garant_nom'          => $validated['garant_nom'] ?? null,
            'garant_telephone'    => $validated['garant_telephone'] ?? null,
            'garant_adresse'      => $validated['garant_adresse'] ?? null,
            'observations'        => $validated['observations'] ?? null,
        ]);

        // Met à jour le statut du bien
        $bien->update(['statut' => 'loue']);

        return redirect()
            ->route('admin.contrats.show', $contrat)
            ->with('success', "Contrat créé ✓ — Bien {$bien->reference} marqué comme loué.");
    }

    // ── Détail d'un contrat ───────────────────────────────────────────────
    public function show(Contrat $contrat)
    {
        $this->authorize('view', $contrat);

        $contrat->load('bien.proprietaire', 'locataire', 'paiements');

        $totalPaye     = $contrat->paiements->where('statut', 'valide')->sum('montant_encaisse');
        $totalNet      = $contrat->paiements->where('statut', 'valide')->sum('net_proprietaire');
        $nbPaiements   = $contrat->paiements->where('statut', 'valide')->count();

        // Prochain mois à payer
        $dernierPaiement  = $contrat->paiements->sortByDesc('periode')->first();
        $prochainePeriode = $dernierPaiement
            ? Carbon::parse($dernierPaiement->periode)->addMonth()
            : Carbon::parse($contrat->date_debut);

        return view('admin.contrats.show', compact(
            'contrat', 'totalPaye', 'totalNet',
            'nbPaiements', 'prochainePeriode'
        ));
    }

    // ── Création rapide d'un locataire (AJAX depuis la modale) ────────────
    public function storeLocataireRapide(Request $request)
    {
        $this->authorize('create', Contrat::class);

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'password'  => ['required', Password::min(8)],
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'password'  => Hash::make($validated['password']),
            'role'      => 'locataire',
            'agency_id' => Auth::user()->agency_id,
        ]);

        return response()->json([
            'success' => true,
            'id'      => $user->id,
            'name'    => $user->name,
        ]);
    }

    // ── Formulaire édition ────────────────────────────────────────────────
    public function edit(Contrat $contrat)
    {
        $this->authorize('update', $contrat);

        $contrat->load('bien', 'locataire');

        // Biens disponibles + le bien actuel du contrat
        $biens = Bien::where(function ($q) use ($contrat) {
            $q->where('statut', 'disponible')
              ->orWhere('id', $contrat->bien_id);
        })->with('proprietaire')->orderBy('reference')->get();

        $locataires = User::where('role', 'locataire')
            ->where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get();

        $typesBail = \App\Models\Contrat::TYPES_BAIL;

        return view('admin.contrats.edit', compact('contrat', 'biens', 'locataires', 'typesBail'));
    }

    // ── Mise à jour ───────────────────────────────────────────────────────
    public function update(Request $request, Contrat $contrat)
    {
        $this->authorize('update', $contrat);

        // Seuls les contrats actifs peuvent être modifiés
        if ($contrat->statut !== 'actif') {
            return back()->withErrors(['general' => 'Seul un contrat actif peut être modifié.']);
        }

        $validated = $request->validate([
            'date_fin'            => ['nullable', 'date', 'after:date_debut'],
            'loyer_contractuel'   => ['required', 'numeric', 'min:1'],
            'caution'             => ['required', 'numeric', 'min:0'],
            'type_bail'           => ['required', 'in:habitation,commercial,mixte,saisonnier'],
            'frais_agence'        => ['nullable', 'numeric', 'min:0'],
            'charges_mensuelles'  => ['nullable', 'numeric', 'min:0'],
            'indexation_annuelle' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'nombre_mois_caution' => ['nullable', 'integer', 'min:1', 'max:6'],
            'garant_nom'          => ['nullable', 'string', 'max:150'],
            'garant_telephone'    => ['nullable', 'string', 'max:20'],
            'garant_adresse'      => ['nullable', 'string', 'max:255'],
            'observations'        => ['nullable', 'string', 'max:1000'],
        ]);

        $contrat->update([
            'date_fin'            => $validated['date_fin'] ?? null,
            'loyer_contractuel'   => $validated['loyer_contractuel'],
            'caution'             => $validated['caution'],
            'type_bail'           => $validated['type_bail'],
            'frais_agence'        => $validated['frais_agence'] ?? 0,
            'charges_mensuelles'  => $validated['charges_mensuelles'] ?? 0,
            'indexation_annuelle' => $validated['indexation_annuelle'] ?? 0,
            'nombre_mois_caution' => $validated['nombre_mois_caution'] ?? 1,
            'garant_nom'          => $validated['garant_nom'] ?? null,
            'garant_telephone'    => $validated['garant_telephone'] ?? null,
            'garant_adresse'      => $validated['garant_adresse'] ?? null,
            'observations'        => $validated['observations'] ?? null,
        ]);

        return redirect()
            ->route('admin.contrats.show', $contrat)
            ->with('success', 'Contrat mis à jour ✓');
    }

    // ── Résiliation d'un contrat ──────────────────────────────────────────
    public function destroy(Contrat $contrat)
    {
        $this->authorize('delete', $contrat);

        if ($contrat->statut !== 'actif') {
            return back()->withErrors(['general' => 'Ce contrat n\'est pas actif.']);
        }

        $contrat->update(['statut' => 'resilié']);

        // Remet le bien disponible
        $contrat->bien->update(['statut' => 'disponible']);

        return redirect()
            ->route('admin.contrats.index')
            ->with('success', "Contrat résilié ✓ — Bien {$contrat->bien->reference} remis disponible.");
    }
}