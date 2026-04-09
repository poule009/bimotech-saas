<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
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

    public function index(Request $request)
    {
        $this->authorize('isAdmin');

        $query = Contrat::with([
            'bien:id,agency_id,reference,adresse,ville,type',
            'locataire:id,name,email',
        ])->select([
            'id', 'agency_id', 'bien_id', 'locataire_id',
            'date_debut', 'date_fin', 'loyer_contractuel',
            'caution', 'statut', 'type_bail', 'reference_bail',
        ]);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('type_bail')) {
            $query->where('type_bail', $request->type_bail);
        }
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('reference_bail', 'like', "%{$search}%")
                  ->orWhereHas('locataire', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('bien', fn($b) => $b->where('reference', 'like', "%{$search}%"));
            });
        }

        $contrats = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $statsRaw = Contrat::selectRaw("
            COUNT(*) AS total,
            SUM(CASE WHEN statut = 'actif'   THEN 1 ELSE 0 END) AS actifs,
            SUM(CASE WHEN statut = 'resilié' THEN 1 ELSE 0 END) AS resilies,
            SUM(CASE WHEN statut = 'expiré'  THEN 1 ELSE 0 END) AS expires
        ")->first();

        $stats = [
            'total'    => (int) ($statsRaw->total    ?? 0),
            'actifs'   => (int) ($statsRaw->actifs   ?? 0),
            'resilies' => (int) ($statsRaw->resilies ?? 0),
            'expires'  => (int) ($statsRaw->expires  ?? 0),
        ];

        return view('admin.contrats.index', compact('contrats', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // ─────────────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;

        $biens = Bien::where('statut', 'disponible')
            ->select(['id', 'agency_id', 'proprietaire_id', 'reference', 'type',
                      'adresse', 'ville', 'loyer_mensuel', 'taux_commission', 'meuble'])
            ->with(['proprietaire:id,name'])
            ->orderBy('reference')
            ->get();

        $locataires = User::where('role', 'locataire')
            ->where('agency_id', $agencyId)
            ->select(['id', 'name', 'email', 'telephone'])
            ->orderBy('name')
            ->get();

        $bienPreselectionne = $request->filled('bien_id')
            ? Bien::select(['id', 'reference', 'loyer_mensuel', 'taux_commission', 'meuble', 'type'])
                  ->find($request->bien_id)
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
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;

        $validated = $request->validate([
            'bien_id'            => ['required', 'exists:biens,id'],
            'locataire_id'       => ['required', 'exists:users,id'],
            'date_debut'         => ['required', 'date'],
            'date_fin'           => ['nullable', 'date', 'after:date_debut'],
            'loyer_nu'           => ['required', 'numeric', 'min:0'],
            'charges_mensuelles' => ['nullable', 'numeric', 'min:0'],
            'tom_amount'         => ['nullable', 'numeric', 'min:0'],
            'caution'            => ['required', 'numeric', 'min:0'],
            'nombre_mois_caution'=> ['nullable', 'integer', 'min:1', 'max:6'],
            'frais_agence'       => ['nullable', 'numeric', 'min:0'],
            'type_bail'          => ['required', 'in:habitation,commercial,mixte,saisonnier'],
            'indexation_annuelle'=> ['nullable', 'numeric', 'min:0', 'max:20'],
            'garant_nom'         => ['nullable', 'string', 'max:150'],
            'garant_telephone'   => ['nullable', 'string', 'max:30'],
            'garant_adresse'     => ['nullable', 'string', 'max:255'],
            'reference_bail'     => ['nullable', 'string', 'max:60'],
            'observations'       => ['nullable', 'string'],
        ], [
            'bien_id.required'       => 'Veuillez sélectionner un bien.',
            'locataire_id.required'  => 'Veuillez sélectionner un locataire.',
            'date_debut.required'    => 'La date de début est obligatoire.',
            'loyer_nu.required'      => 'Le loyer est obligatoire.',
            'caution.required'       => 'La caution est obligatoire.',
            'type_bail.required'     => 'Le type de bail est obligatoire.',
        ]);

        // Vérifier que le bien appartient à l'agence
        $bien = Bien::findOrFail($validated['bien_id']);
        if ($bien->agency_id !== $agencyId) {
            return back()->withErrors(['bien_id' => 'Ce bien n\'appartient pas à votre agence.']);
        }

        // Vérifier qu'il n'y a pas déjà un contrat actif sur ce bien
        if (Contrat::where('bien_id', $bien->id)->where('statut', 'actif')->exists()) {
            return back()->withErrors(['bien_id' => 'Ce bien a déjà un contrat actif.'])->withInput();
        }

        // Calcul loyer_contractuel
        $loyerNu           = (float) ($validated['loyer_nu'] ?? 0);
        $chargesMensuelles = (float) ($validated['charges_mensuelles'] ?? 0);
        $tomAmount         = (float) ($validated['tom_amount'] ?? 0);
        $validated['loyer_contractuel'] = $loyerNu + $chargesMensuelles + $tomAmount;
        $validated['agency_id']         = $agencyId;
        $validated['statut']            = 'actif';

        $contrat = Contrat::create($validated);

        // Mettre le bien en statut loué
        $bien->update(['statut' => 'loue']);

        return redirect()
            ->route('admin.contrats.show', $contrat)
            ->with('success', 'Contrat créé avec succès ✓');
    }

    // ─────────────────────────────────────────────────────────────────────
    // DÉTAIL
    // ─────────────────────────────────────────────────────────────────────

    public function show(Contrat $contrat)
    {
        $this->authorize('isAdmin');

        $contrat->load([
            'bien',
            'bien.proprietaire:id,name,email,telephone',
            'locataire:id,name,email,telephone',
        ]);

        // Stats paiements
        $aggrPaiements = Paiement::where('contrat_id', $contrat->id)
            ->where('statut', 'valide')
            ->selectRaw('
                COALESCE(SUM(montant_encaisse), 0) AS total_paye,
                COALESCE(SUM(net_proprietaire), 0) AS total_net,
                COALESCE(SUM(commission_ttc), 0)   AS total_commission,
                COUNT(*)                            AS nb_paiements
            ')
            ->first();

        $totalPaye    = (float) ($aggrPaiements->total_paye    ?? 0);
        $totalNet     = (float) ($aggrPaiements->total_net     ?? 0);
        $nbPaiements  = (int)   ($aggrPaiements->nb_paiements  ?? 0);

        // Prochaine période
        $dernierPaiement = Paiement::where('contrat_id', $contrat->id)
            ->where('statut', 'valide')
            ->orderByDesc('periode')
            ->first();

        $prochainePeriode = $dernierPaiement
            ? Carbon::parse($dernierPaiement->periode)->addMonth()
            : Carbon::parse($contrat->date_debut);

        // Liste paiements
        $paiements = Paiement::where('contrat_id', $contrat->id)
            ->select([
                'id', 'contrat_id', 'agency_id', 'periode', 'date_paiement',
                'montant_encaisse', 'net_proprietaire', 'commission_ttc',
                'mode_paiement', 'statut', 'reference_paiement',
            ])
            ->orderByDesc('periode')
            ->get();

        return view('admin.contrats.show', compact(
            'contrat', 'totalPaye', 'totalNet', 'nbPaiements',
            'prochainePeriode', 'paiements'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE ÉDITION
    // ─────────────────────────────────────────────────────────────────────

    public function edit(Contrat $contrat)
    {
        $this->authorize('isAdmin');

        $contrat->load([
            'bien:id,reference,type,adresse,meuble,loyer_mensuel,taux_commission',
            'locataire:id,name',
        ]);

        $locataires = User::where('role', 'locataire')
            ->where('agency_id', Auth::user()->agency_id)
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get();

        $typesBail = Contrat::TYPES_BAIL;

        return view('admin.contrats.edit', compact('contrat', 'locataires', 'typesBail'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // MISE À JOUR
    // ─────────────────────────────────────────────────────────────────────

    public function update(Request $request, Contrat $contrat): RedirectResponse
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'date_debut'         => ['required', 'date'],
            'date_fin'           => ['nullable', 'date', 'after:date_debut'],
            'loyer_nu'           => ['required', 'numeric', 'min:0'],
            'charges_mensuelles' => ['nullable', 'numeric', 'min:0'],
            'tom_amount'         => ['nullable', 'numeric', 'min:0'],
            'caution'            => ['required', 'numeric', 'min:0'],
            'nombre_mois_caution'=> ['nullable', 'integer', 'min:1', 'max:6'],
            'frais_agence'       => ['nullable', 'numeric', 'min:0'],
            'type_bail'          => ['required', 'in:habitation,commercial,mixte,saisonnier'],
            'indexation_annuelle'=> ['nullable', 'numeric', 'min:0', 'max:20'],
            'garant_nom'         => ['nullable', 'string', 'max:150'],
            'garant_telephone'   => ['nullable', 'string', 'max:30'],
            'garant_adresse'     => ['nullable', 'string', 'max:255'],
            'reference_bail'     => ['nullable', 'string', 'max:60'],
            'observations'       => ['nullable', 'string'],
        ]);

        $loyerNu           = (float) ($validated['loyer_nu'] ?? 0);
        $chargesMensuelles = (float) ($validated['charges_mensuelles'] ?? 0);
        $tomAmount         = (float) ($validated['tom_amount'] ?? 0);
        $validated['loyer_contractuel'] = $loyerNu + $chargesMensuelles + $tomAmount;

        $contrat->update($validated);

        return redirect()
            ->route('admin.contrats.show', $contrat)
            ->with('success', 'Contrat mis à jour ✓');
    }

    // ─────────────────────────────────────────────────────────────────────
    // RÉSILIATION
    // ─────────────────────────────────────────────────────────────────────

    public function destroy(Contrat $contrat): RedirectResponse
    {
        $this->authorize('isAdmin');

        if ($contrat->statut !== 'actif') {
            return back()->withErrors(['general' => 'Seul un contrat actif peut être résilié.']);
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

    public function storeLocataireRapide(Request $request): JsonResponse
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'password'  => ['required', Password::min(8)],
        ], [
            'email.unique' => 'Cet email est déjà utilisé.',
        ]);

        $user                    = new User();
        $user->name              = $validated['name'];
        $user->email             = $validated['email'];
        $user->telephone         = $validated['telephone'] ?? null;
        $user->password          = Hash::make($validated['password']);
        $user->role              = 'locataire';
        $user->agency_id         = Auth::user()->agency_id;
        $user->email_verified_at = now();
        $user->save();

        return response()->json(['success' => true, 'id' => $user->id, 'name' => $user->name]);
    }
}