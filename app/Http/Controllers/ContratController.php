<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ContratController extends Controller
{
    use AuthorizesRequests;

    // ─────────────────────────────────────────────────────────────────────
    // LISTE
    // ─────────────────────────────────────────────────────────────────────

    public function index()
    {
        $this->authorize('viewAny', Contrat::class);

        $contrats = Contrat::with('bien', 'locataire')
            ->orderByDesc('created_at')
            ->paginate(15);

        $stats = [
            'total'    => Contrat::count(),
            'actifs'   => Contrat::where('statut', 'actif')->count(),
            'resilies' => Contrat::where('statut', 'resilié')->count(),
            'expires'  => Contrat::where('statut', 'expiré')->count(),
        ];

        return view('admin.contrats.index', compact('contrats', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // ─────────────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $this->authorize('create', Contrat::class);

        $biens = Bien::where('statut', 'disponible')
            ->with('proprietaire')
            ->orderBy('reference')
            ->get();

        $locataires = User::where('role', 'locataire')
            ->where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get();

        $bienPreselectionne = $request->has('bien_id')
            ? Bien::find($request->bien_id)
            : null;

        $typesBail = Contrat::TYPES_BAIL;

        return view('admin.contrats.create', compact(
            'biens', 'locataires', 'bienPreselectionne', 'typesBail'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // ENREGISTREMENT
    // ─────────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Contrat::class);

        $agencyId = Auth::user()->agency_id;

        // ── Validation ────────────────────────────────────────────────────
        $validated = $request->validate([
            // Parties
            'bien_id' => [
                'required', 'exists:biens,id',
                function ($attr, $value, $fail) use ($agencyId) {
                    $bien = Bien::withoutGlobalScopes()->find($value);
                    if (! $bien || $bien->agency_id !== $agencyId) {
                        $fail('Ce bien n\'appartient pas à votre agence.');
                    }
                },
            ],
            'locataire_id' => [
                'required', 'exists:users,id',
                function ($attr, $value, $fail) use ($agencyId) {
                    $locataire = User::find($value);
                    if (! $locataire || $locataire->agency_id !== $agencyId || $locataire->role !== 'locataire') {
                        $fail('Ce locataire n\'appartient pas à votre agence.');
                    }
                },
            ],

            // Dates
            'date_debut'   => ['required', 'date'],
            'date_fin'     => ['nullable', 'date', 'after:date_debut'],

            // ── Ventilation loyer (nouveaux champs) ───────────────────────
            'loyer_nu'           => ['required', 'numeric', 'min:1'],
            'charges_mensuelles' => ['nullable', 'numeric', 'min:0'],
            'tom_amount'         => ['nullable', 'numeric', 'min:0'],

            // Référence bail manuelle (optionnelle)
            'reference_bail' => ['nullable', 'string', 'max:60'],

            // Financier
            'caution'             => ['required', 'numeric', 'min:0'],
            'type_bail'           => ['required', 'in:habitation,commercial,mixte,saisonnier'],
            'frais_agence'        => ['nullable', 'numeric', 'min:0'],
            'indexation_annuelle' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'nombre_mois_caution' => ['nullable', 'integer', 'min:1', 'max:6'],

            // Garant
            'garant_nom'       => ['nullable', 'string', 'max:150'],
            'garant_telephone' => ['nullable', 'string', 'max:20'],
            'garant_adresse'   => ['nullable', 'string', 'max:255'],

            'observations' => ['nullable', 'string', 'max:1000'],
        ]);

        // ── Vérification doublon contrat actif ────────────────────────────
        if (Contrat::where('bien_id', $validated['bien_id'])->where('statut', 'actif')->exists()) {
            return back()->withInput()->withErrors(['bien_id' => 'Ce bien a déjà un contrat actif.']);
        }

        // ── Calcul du loyer contractuel total ─────────────────────────────
        $loyerNu           = (float) $validated['loyer_nu'];
        $chargesMensuelles = (float) ($validated['charges_mensuelles'] ?? 0);
        $tomAmount         = (float) ($validated['tom_amount'] ?? 0);
        $loyerContractuel  = round($loyerNu + $chargesMensuelles + $tomAmount, 2);

        // ── Référence bail ────────────────────────────────────────────────
        // Priorité : saisie manuelle > génération automatique BIMO-YYYY-NNN
        $referenceBail = null;

        if (! empty($validated['reference_bail'])) {
            // Saisie manuelle — on la conserve telle quelle
            $referenceBail = trim($validated['reference_bail']);
        }
        // Si vide, la référence sera générée APRÈS la création (on a besoin de l'ID)

        // ── Création du contrat ───────────────────────────────────────────
        $contrat = Contrat::create([
            'bien_id'             => $validated['bien_id'],
            'locataire_id'        => $validated['locataire_id'],
            'date_debut'          => $validated['date_debut'],
            'date_fin'            => $validated['date_fin'] ?? null,
            'loyer_nu'            => $loyerNu,
            'loyer_contractuel'   => $loyerContractuel,
            'charges_mensuelles'  => $chargesMensuelles,
            'tom_amount'          => $tomAmount,
            'caution'             => $validated['caution'],
            'statut'              => 'actif',
            'type_bail'           => $validated['type_bail'],
            'frais_agence'        => $validated['frais_agence'] ?? 0,
            'indexation_annuelle' => $validated['indexation_annuelle'] ?? 0,
            'nombre_mois_caution' => $validated['nombre_mois_caution'] ?? 1,
            'garant_nom'          => $validated['garant_nom'] ?? null,
            'garant_telephone'    => $validated['garant_telephone'] ?? null,
            'garant_adresse'      => $validated['garant_adresse'] ?? null,
            'observations'        => $validated['observations'] ?? null,
            'reference_bail'      => $referenceBail,
        ]);

        // ── Génération auto de la référence bail si non saisie ────────────
        // Format : BIMO-{ANNÉE}-{ID sur 5 chiffres}
        // Ex : BIMO-2026-00042
        if (empty($referenceBail)) {
            $contrat->update([
                'reference_bail' => sprintf(
                    'BIMO-%s-%s',
                    now()->year,
                    str_pad((string) $contrat->id, 5, '0', STR_PAD_LEFT)
                ),
            ]);
        }

        // ── Mise à jour statut du bien ────────────────────────────────────
        Bien::withoutGlobalScopes()
            ->where('id', $contrat->bien_id)
            ->update(['statut' => 'loue']);

        return redirect()
            ->route('admin.contrats.show', $contrat)
            ->with('success', "Contrat {$contrat->reference_bail} créé ✓ — Bien {$contrat->bien->reference} marqué comme loué.");
    }

    // ─────────────────────────────────────────────────────────────────────
    // DÉTAIL
    // ─────────────────────────────────────────────────────────────────────

    public function show(Contrat $contrat)
    {
        $this->authorize('view', $contrat);

        $contrat->load('bien.proprietaire', 'locataire', 'paiements');

        $totalPaye   = $contrat->paiements->where('statut', 'valide')->sum('montant_encaisse');
        $totalNet    = $contrat->paiements->where('statut', 'valide')->sum('net_proprietaire');
        $nbPaiements = $contrat->paiements->where('statut', 'valide')->count();

        $dernierPaiement  = $contrat->paiements->sortByDesc('periode')->first();
        $prochainePeriode = $dernierPaiement
            ? Carbon::parse($dernierPaiement->periode)->addMonth()
            : Carbon::parse($contrat->date_debut);

        // Décomposition loyer pour la vue
        $decomposition = $contrat->decompositionLoyer();

        return view('admin.contrats.show', compact(
            'contrat', 'totalPaye', 'totalNet',
            'nbPaiements', 'prochainePeriode', 'decomposition'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE ÉDITION
    // ─────────────────────────────────────────────────────────────────────

    public function edit(Contrat $contrat)
    {
        $this->authorize('update', $contrat);

        $contrat->load('bien', 'locataire');

        $biens = Bien::where(function ($q) use ($contrat) {
            $q->where('statut', 'disponible')
              ->orWhere('id', $contrat->bien_id);
        })->with('proprietaire')->orderBy('reference')->get();

        $locataires = User::where('role', 'locataire')
            ->where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get();

        $typesBail = Contrat::TYPES_BAIL;

        return view('admin.contrats.edit', compact('contrat', 'biens', 'locataires', 'typesBail'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // MISE À JOUR
    // ─────────────────────────────────────────────────────────────────────

    public function update(Request $request, Contrat $contrat): RedirectResponse
    {
        $this->authorize('update', $contrat);

        if ($contrat->statut !== 'actif') {
            return back()->withErrors(['general' => 'Seul un contrat actif peut être modifié.']);
        }

        $validated = $request->validate([
            'date_fin'            => ['nullable', 'date', 'after:date_debut'],
            'loyer_nu'            => ['required', 'numeric', 'min:1'],
            'charges_mensuelles'  => ['nullable', 'numeric', 'min:0'],
            'tom_amount'          => ['nullable', 'numeric', 'min:0'],
            'caution'             => ['required', 'numeric', 'min:0'],
            'type_bail'           => ['required', 'in:habitation,commercial,mixte,saisonnier'],
            'frais_agence'        => ['nullable', 'numeric', 'min:0'],
            'charges_mensuelles'  => ['nullable', 'numeric', 'min:0'],
            'indexation_annuelle' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'nombre_mois_caution' => ['nullable', 'integer', 'min:1', 'max:6'],
            'garant_nom'          => ['nullable', 'string', 'max:150'],
            'garant_telephone'    => ['nullable', 'string', 'max:20'],
            'garant_adresse'      => ['nullable', 'string', 'max:255'],
            'reference_bail'      => ['nullable', 'string', 'max:60'],
            'observations'        => ['nullable', 'string', 'max:1000'],
        ]);

        $loyerNu          = (float) $validated['loyer_nu'];
        $chargesMensuelles = (float) ($validated['charges_mensuelles'] ?? 0);
        $tomAmount         = (float) ($validated['tom_amount'] ?? 0);
        $loyerContractuel  = round($loyerNu + $chargesMensuelles + $tomAmount, 2);

        $contrat->update([
            'date_fin'            => $validated['date_fin'] ?? null,
            'loyer_nu'            => $loyerNu,
            'loyer_contractuel'   => $loyerContractuel,
            'charges_mensuelles'  => $chargesMensuelles,
            'tom_amount'          => $tomAmount,
            'caution'             => $validated['caution'],
            'type_bail'           => $validated['type_bail'],
            'frais_agence'        => $validated['frais_agence'] ?? 0,
            'indexation_annuelle' => $validated['indexation_annuelle'] ?? 0,
            'nombre_mois_caution' => $validated['nombre_mois_caution'] ?? 1,
            'garant_nom'          => $validated['garant_nom'] ?? null,
            'garant_telephone'    => $validated['garant_telephone'] ?? null,
            'garant_adresse'      => $validated['garant_adresse'] ?? null,
            'reference_bail'      => ! empty($validated['reference_bail'])
                                        ? trim($validated['reference_bail'])
                                        : $contrat->reference_bail,
            'observations'        => $validated['observations'] ?? null,
        ]);

        return redirect()
            ->route('admin.contrats.show', $contrat)
            ->with('success', 'Contrat mis à jour ✓');
    }

    // ─────────────────────────────────────────────────────────────────────
    // RÉSILIATION
    // ─────────────────────────────────────────────────────────────────────

    public function destroy(Contrat $contrat): RedirectResponse
    {
        $this->authorize('delete', $contrat);

        if ($contrat->statut !== 'actif') {
            return back()->withErrors(['general' => 'Ce contrat n\'est pas actif.']);
        }

        $contrat->update(['statut' => 'resilié']);
        $contrat->bien->update(['statut' => 'disponible']);

        return redirect()
            ->route('admin.contrats.index')
            ->with('success', "Contrat résilié ✓ — Bien {$contrat->bien->reference} remis disponible.");
    }

    // ─────────────────────────────────────────────────────────────────────
    // CRÉATION RAPIDE LOCATAIRE (AJAX)
    // ─────────────────────────────────────────────────────────────────────

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
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'telephone'         => $validated['telephone'] ?? null,
            'password'          => Hash::make($validated['password']),
            'role'              => 'locataire',
            'agency_id'         => Auth::user()->agency_id,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'id'      => $user->id,
            'name'    => $user->name,
        ]);
    }
}