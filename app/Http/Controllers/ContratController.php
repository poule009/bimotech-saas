<?php

namespace App\Http\Controllers;

use App\Enums\BienStatut;
use App\Enums\ContratStatut;
use App\Enums\UserRole;
use App\Http\Requests\StoreContratRequest;
use App\Http\Requests\UpdateContratRequest;
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

        $contrats = Contrat::select([
            'id',
            'agency_id',
            'bien_id',
            'locataire_id',
            'date_debut',
            'date_fin',
            'loyer_contractuel',
            'caution',
            'statut',
            'type_bail',
            'reference_bail',
            'brs_applicable',
            'loyer_assujetti_tva',
            'date_enregistrement_dgid',
            'enregistrement_exonere',
        ])
            ->with([
                'bien:id,agency_id,reference,adresse,ville',
                'locataire:id,name,email',
            ])
            ->orderByDesc('created_at')
            ->paginate(20);

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

        $biens = Bien::where('statut', 'disponible')
            ->select(['id', 'agency_id', 'proprietaire_id', 'reference', 'type', 'adresse', 'ville', 'loyer_mensuel', 'taux_commission', 'meuble'])
            ->with(['proprietaire:id,name'])
            ->orderBy('reference')
            ->get();

        $locataires = User::where('role', 'locataire')
            ->where('agency_id', $agencyId)
            ->select(['id', 'name', 'email', 'telephone'])
            ->orderBy('name')
            ->get();

        $bienPreselectionne = $request->has('bien_id')
            ? Bien::select(['id', 'reference', 'loyer_mensuel', 'taux_commission', 'meuble', 'type'])->find($request->bien_id)
            : null;

        $typesBail = Contrat::TYPES_BAIL;

        return view('admin.contrats.create', compact(
            'biens',
            'locataires',
            'bienPreselectionne',
            'typesBail'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // ENREGISTREMENT
    // ─────────────────────────────────────────────────────────────────────

    public function store(StoreContratRequest $request): RedirectResponse
    {
        // authorize() et rules() sont gérés par StoreContratRequest.
        $validated = $request->validated();

        if (Contrat::where('bien_id', $validated['bien_id'])->where('statut', 'actif')->exists()) {
            return back()->withInput()->withErrors(['bien_id' => 'Ce bien a déjà un contrat actif.']);
        }

        $loyerNu           = (float) $validated['loyer_nu'];
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
            // ── Fiscal (l'Observer ContratObserver calcule aussi automatiquement)
            'loyer_assujetti_tva'      => $request->boolean('loyer_assujetti_tva'),
            'taux_tva_loyer'           => $validated['taux_tva_loyer'] ?? 0,
            'brs_applicable'           => $request->boolean('brs_applicable'),
            'taux_brs_manuel'          => $validated['taux_brs_manuel'] ?? null,
            // ── DGID
            'date_enregistrement_dgid' => $validated['date_enregistrement_dgid'] ?? null,
            'numero_quittance_dgid'    => $validated['numero_quittance_dgid'] ?? null,
            'montant_droit_de_bail'    => $validated['montant_droit_de_bail'] ?? null,
            'enregistrement_exonere'   => $request->boolean('enregistrement_exonere'),
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

        $aggrContrat = $contrat->paiements()
            ->where('statut', 'valide')
            ->selectRaw('
                COALESCE(SUM(montant_encaisse), 0) AS total_paye,
                COALESCE(SUM(net_proprietaire), 0) AS total_net,
                COUNT(*) AS nb_paiements
            ')
            ->first();

        $totalPaye   = (float) $aggrContrat->total_paye;
        $totalNet    = (float) $aggrContrat->total_net;
        $nbPaiements = (int)   $aggrContrat->nb_paiements;

        $dernierPaiement  = $contrat->paiements()->orderByDesc('periode')->select(['id', 'contrat_id', 'periode'])->first();
        $prochainePeriode = $dernierPaiement
            ? Carbon::parse($dernierPaiement->periode)->addMonth()
            : Carbon::parse($contrat->date_debut);



        $paiements = $contrat->paiements()
            ->select(['id', 'contrat_id', 'agency_id', 'periode', 'montant_encaisse', 'net_proprietaire', 'commission_ttc', 'mode_paiement', 'date_paiement', 'statut', 'reference_paiement'])
            ->orderByDesc('periode')
            ->get();

        return view('admin.contrats.show', compact(
            'contrat',
            'totalPaye',
            'totalNet',
            'nbPaiements',
            'prochainePeriode',
            'paiements'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE ÉDITION
    // ─────────────────────────────────────────────────────────────────────

    public function edit(Contrat $contrat)
    {
        $this->authorize('update', $contrat);

        $contrat->load([
            'bien:id,reference,type,adresse,meuble',
            'locataire:id,name',
        ]);

        $biens = Bien::where(function ($q) use ($contrat) {
            $q->where('statut', 'disponible')->orWhere('id', $contrat->bien_id);
        })
            ->select(['id', 'agency_id', 'reference', 'type', 'adresse', 'ville', 'loyer_mensuel', 'taux_commission', 'meuble'])
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

    public function update(UpdateContratRequest $request, Contrat $contrat): RedirectResponse
    {
        // authorize() et rules() sont gérés par UpdateContratRequest.
        if ($contrat->statut !== ContratStatut::Actif->value) {
            return back()->withErrors(['general' => 'Seul un contrat actif peut être modifié.']);
        }

        $validated = $request->validated();

        $loyerNu           = (float) $validated['loyer_nu'];
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
            // ── Fiscal
            'loyer_assujetti_tva'      => $request->boolean('loyer_assujetti_tva'),
            'taux_tva_loyer'           => $validated['taux_tva_loyer'] ?? 0,
            'brs_applicable'           => $request->boolean('brs_applicable'),
            'taux_brs_manuel'          => $validated['taux_brs_manuel'] ?? null,
            // ── DGID
            'date_enregistrement_dgid' => $validated['date_enregistrement_dgid'] ?? null,
            'numero_quittance_dgid'    => $validated['numero_quittance_dgid'] ?? null,
            'montant_droit_de_bail'    => $validated['montant_droit_de_bail'] ?? null,
            'enregistrement_exonere'   => $request->boolean('enregistrement_exonere'),
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
        $this->authorize('resilier', $contrat);

        if ($contrat->statut !== ContratStatut::Actif->value) {
            return back()->withErrors(['general' => 'Ce contrat n\'est pas actif.']);
        }

        $contrat->update(['statut' => ContratStatut::Resilie->value]);
        $contrat->bien->update(['statut' => BienStatut::Disponible->value]);

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

        $user                    = new User();
        $user->name              = $validated['name'];
        $user->email             = $validated['email'];
        $user->telephone         = $validated['telephone'] ?? null;
        $user->password          = Hash::make($validated['password']);
        $user->role              = UserRole::Locataire->value;
        $user->agency_id         = Auth::user()->agency_id;
        $user->email_verified_at = now();
        $user->save();

        return response()->json(['success' => true, 'id' => $user->id, 'name' => $user->name]);
    }
}
