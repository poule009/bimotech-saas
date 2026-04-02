<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
// use App\Models\Paiement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
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

        /**
         * PERFORMANCE — select() sur la liste :
         *
         * La vue index affiche : référence bien, locataire, dates, loyer, statut.
         * On ne charge pas : observations, garant_*, indexation_annuelle, etc.
         *
         * Eager loading avec select() sur les relations pour éviter de charger
         * toutes les colonnes des tables liées.
         */
        $contrats = Contrat::select([
                'id', 'agency_id', 'bien_id', 'locataire_id',
                'statut', 'date_debut', 'date_fin',
                'loyer_contractuel', 'caution', 'type_bail', 'reference_bail',
                'created_at',
            ])
            ->with([
                'bien:id,agency_id,reference,type,adresse,ville,statut',
                'locataire:id,name,email,telephone',
            ])
            ->orderByDesc('created_at')
            ->paginate(15);

        /**
         * PERFORMANCE — Stats en une seule requête SQL au lieu de 4 :
         *
         * Avant :
         *   Contrat::count()
         *   Contrat::where('statut', 'actif')->count()
         *   Contrat::where('statut', 'resilié')->count()
         *   Contrat::where('statut', 'expiré')->count()
         * = 4 requêtes SQL
         *
         * Après : 1 requête avec GROUP BY
         */
        $statsRaw = Contrat::selectRaw("
            COUNT(*) AS total,
            SUM(CASE WHEN statut = 'actif'   THEN 1 ELSE 0 END) AS actifs,
            SUM(CASE WHEN statut = 'resilié' THEN 1 ELSE 0 END) AS resilies,
            SUM(CASE WHEN statut = 'expiré'  THEN 1 ELSE 0 END) AS expires
        ")->first();

        $stats = [
            'total'    => (int) $statsRaw->total,
            'actifs'   => (int) $statsRaw->actifs,
            'resilies' => (int) $statsRaw->resilies,
            'expires'  => (int) $statsRaw->expires,
        ];

        return view('admin.contrats.index', compact('contrats', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // ─────────────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $this->authorize('create', Contrat::class);

        $agencyId = Auth::user()->agency_id;

        /**
         * PERFORMANCE — select() sur les dropdowns :
         *
         * Pour un <select> HTML, on n'a besoin que de id, reference, adresse,
         * loyer_mensuel et taux_commission pour pré-remplir le formulaire JS.
         * Charger description, nombre_pieces, etc. est inutile.
         */
        $biens = Bien::where('statut', 'disponible')
            ->select(['id', 'agency_id', 'proprietaire_id', 'reference', 'type', 'adresse', 'ville', 'loyer_mensuel', 'taux_commission'])
            ->with(['proprietaire:id,name'])
            ->orderBy('reference')
            ->get();

        $locataires = User::where('role', 'locataire')
            ->where('agency_id', $agencyId)
            ->select(['id', 'name', 'email', 'telephone'])
            ->orderBy('name')
            ->get();

        $bienPreselectionne = $request->has('bien_id')
            ? Bien::select(['id', 'reference', 'loyer_mensuel', 'taux_commission'])->find($request->bien_id)
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

        $validated = $request->validate([
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
                    $loc = User::find($value);
                    if (! $loc || $loc->agency_id !== $agencyId || $loc->role !== 'locataire') {
                        $fail('Ce locataire n\'appartient pas à votre agence.');
                    }
                },
            ],
            'date_debut'          => ['required', 'date'],
            'date_fin'            => ['nullable', 'date', 'after:date_debut'],
            'loyer_nu'            => ['required', 'numeric', 'min:1'],
            'charges_mensuelles'  => ['nullable', 'numeric', 'min:0'],
            'tom_amount'          => ['nullable', 'numeric', 'min:0'],
            'caution'             => ['required', 'numeric', 'min:0'],
            'type_bail'           => ['required', 'in:habitation,commercial,mixte,saisonnier'],
            'frais_agence'        => ['nullable', 'numeric', 'min:0'],
            'indexation_annuelle' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'nombre_mois_caution' => ['nullable', 'integer', 'min:1', 'max:6'],
            'garant_nom'          => ['nullable', 'string', 'max:150'],
            'garant_telephone'    => ['nullable', 'string', 'max:20'],
            'garant_adresse'      => ['nullable', 'string', 'max:255'],
            'reference_bail'      => ['nullable', 'string', 'max:60'],
            'observations'        => ['nullable', 'string', 'max:1000'],
        ]);

        if (Contrat::where('bien_id', $validated['bien_id'])->where('statut', 'actif')->exists()) {
            return back()->withInput()->withErrors(['bien_id' => 'Ce bien a déjà un contrat actif.']);
        }

        $loyerNu          = (float) $validated['loyer_nu'];
        $chargesMensuelles = (float) ($validated['charges_mensuelles'] ?? 0);
        $tomAmount         = (float) ($validated['tom_amount'] ?? 0);
        $loyerContractuel  = round($loyerNu + $chargesMensuelles + $tomAmount, 2);

        $referenceBail = ! empty($validated['reference_bail'])
            ? trim($validated['reference_bail'])
            : null;

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

        if (empty($referenceBail)) {
            $contrat->update([
                'reference_bail' => sprintf('BIMO-%s-%s', now()->year, str_pad($contrat->id, 5, '0', STR_PAD_LEFT)),
            ]);
        }

        Bien::withoutGlobalScopes()->where('id', $contrat->bien_id)->update(['statut' => 'loue']);

        return redirect()
            ->route('admin.contrats.show', $contrat)
            ->with('success', "Contrat {$contrat->reference_bail} créé ✓");
    }

    // ─────────────────────────────────────────────────────────────────────
    // DÉTAIL
    // ─────────────────────────────────────────────────────────────────────

    public function show(Contrat $contrat)
    {
        $this->authorize('view', $contrat);

        $contrat->load([
            'bien:id,agency_id,proprietaire_id,reference,type,adresse,ville,quartier,commune,surface_m2,nombre_pieces,meuble,statut,taux_commission',
            'bien.proprietaire:id,name,telephone,adresse',
            'locataire:id,name,email,telephone',
        ]);

        // Agrégats SQL — une seule requête
        $aggrContrat = $contrat->paiements()
            ->where('statut', 'valide')
            ->selectRaw('
                COALESCE(SUM(montant_encaisse), 0)  AS total_paye,
                COALESCE(SUM(net_proprietaire), 0)  AS total_net,
                COUNT(*)                            AS nb_paiements
            ')
            ->first();

        $totalPaye   = (float) $aggrContrat->total_paye;
        $totalNet    = (float) $aggrContrat->total_net;
        $nbPaiements = (int)   $aggrContrat->nb_paiements;

        $dernierPaiement  = $contrat->paiements()->orderByDesc('periode')->select(['id', 'contrat_id', 'periode'])->first();
        $prochainePeriode = $dernierPaiement
            ? Carbon::parse($dernierPaiement->periode)->addMonth()
            : Carbon::parse($contrat->date_debut);

        $decomposition = $contrat->decompositionLoyer();

        // Historique paiements avec select() sélectif
        $paiements = $contrat->paiements()
            ->select(['id', 'contrat_id', 'agency_id', 'periode', 'montant_encaisse', 'net_proprietaire', 'commission_ttc', 'mode_paiement', 'date_paiement', 'statut', 'reference_paiement'])
            ->orderByDesc('periode')
            ->get();

        return view('admin.contrats.show', compact(
            'contrat', 'totalPaye', 'totalNet', 'nbPaiements',
            'prochainePeriode', 'decomposition', 'paiements'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE ÉDITION
    // ─────────────────────────────────────────────────────────────────────

    public function edit(Contrat $contrat)
    {
        $this->authorize('update', $contrat);

        $contrat->load([
            'bien:id,reference,type,adresse',
            'locataire:id,name',
        ]);

        $biens = Bien::where(function ($q) use ($contrat) {
            $q->where('statut', 'disponible')->orWhere('id', $contrat->bien_id);
        })
        ->select(['id', 'agency_id', 'reference', 'type', 'adresse', 'ville', 'loyer_mensuel', 'taux_commission'])
        ->with(['proprietaire:id,name'])
        ->orderBy('reference')
        ->get();

        $locataires = User::where('role', 'locataire')
            ->where('agency_id', Auth::user()->agency_id)
            ->select(['id', 'name', 'email'])
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

        $agencyId = Auth::user()->agency_id;

        $validated = $request->validate([
            'date_fin'            => ['nullable', 'date', 'after:date_debut'],
            'loyer_nu'            => ['required', 'numeric', 'min:1'],
            'charges_mensuelles'  => ['nullable', 'numeric', 'min:0'],
            'tom_amount'          => ['nullable', 'numeric', 'min:0'],
            'caution'             => ['required', 'numeric', 'min:0'],
            'type_bail'           => ['required', 'in:habitation,commercial,mixte,saisonnier'],
            'frais_agence'        => ['nullable', 'numeric', 'min:0'],
            'indexation_annuelle' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'nombre_mois_caution' => ['nullable', 'integer', 'min:1', 'max:6'],
            'garant_nom'          => ['nullable', 'string', 'max:150'],
            'garant_telephone'    => ['nullable', 'string', 'max:20'],
            'garant_adresse'      => ['nullable', 'string', 'max:255'],
            'reference_bail'      => ['nullable', 'string', 'max:60'],
            'observations'        => ['nullable', 'string', 'max:1000'],
            'locataire_id'        => [
                'nullable', 'exists:users,id',
                function ($attr, $value, $fail) use ($agencyId) {
                    if (empty($value)) return;
                    $loc = User::find($value);
                    if (! $loc || $loc->agency_id !== $agencyId || $loc->role !== 'locataire') {
                        $fail('Ce locataire n\'appartient pas à votre agence.');
                    }
                },
            ],
        ]);

        $loyerNu          = (float) $validated['loyer_nu'];
        $chargesMensuelles = (float) ($validated['charges_mensuelles'] ?? 0);
        $tomAmount         = (float) ($validated['tom_amount'] ?? 0);

        $updateData = [
            'date_fin'            => $validated['date_fin'] ?? null,
            'loyer_nu'            => $loyerNu,
            'loyer_contractuel'   => round($loyerNu + $chargesMensuelles + $tomAmount, 2),
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
        ];

        if (! empty($validated['locataire_id'])) {
            $updateData['locataire_id'] = $validated['locataire_id'];
        }

        $contrat->update($updateData);

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
        ], ['email.unique' => 'Cet email est déjà utilisé.']);

        $user                   = new User();
        $user->name             = $validated['name'];
        $user->email            = $validated['email'];
        $user->telephone        = $validated['telephone'] ?? null;
        $user->password         = Hash::make($validated['password']);
        $user->role             = 'locataire';
        $user->agency_id        = Auth::user()->agency_id;
        $user->email_verified_at = now();
        $user->save();

        return response()->json(['success' => true, 'id' => $user->id, 'name' => $user->name]);
    }
}