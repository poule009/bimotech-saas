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
use App\Services\FiscalContext;
use App\Services\FiscalService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ContratController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:contrat_formel_pdf', only: ['bailFormelPdf']),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // LISTE
    // ─────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->authorize('viewAny', Contrat::class);

        $aujourdhui = now()->toDateString();
        $limite30   = now()->addDays(30)->toDateString();

        $query = Contrat::select([
            'id', 'agency_id', 'bien_id', 'locataire_id', 'date_debut', 'date_fin',
            'loyer_contractuel', 'caution', 'statut', 'type_bail', 'reference_bail',
            'brs_applicable', 'loyer_assujetti_tva', 'date_enregistrement_dgid', 'enregistrement_exonere',
        ])->with([
            'bien:id,agency_id,reference,adresse,ville',
            'locataire:id,name,email',
        ]);

        if ($q = trim((string) $request->input('q'))) {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('bien', fn ($b) => $b->where('reference', 'like', "%{$q}%")
                        ->orWhere('adresse', 'like', "%{$q}%")->orWhere('ville', 'like', "%{$q}%"))
                    ->orWhereHas('locataire', fn ($l) => $l->where('name', 'like', "%{$q}%"));
            });
        }
        if ($request->input('filter') === 'actifs') {
            $query->where('statut', 'actif');
        }
        if ($request->boolean('echeance')) {
            $query->where('statut', 'actif')->whereNotNull('date_fin')
                  ->whereBetween('date_fin', [$aujourdhui, $limite30]);
        }

        $contrats = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $statsRaw = Contrat::selectRaw("
            COUNT(*) AS total,
            SUM(CASE WHEN statut = 'actif'   THEN 1 ELSE 0 END) AS actifs,
            SUM(CASE WHEN statut = 'resilié' THEN 1 ELSE 0 END) AS resilies,
            SUM(CASE WHEN statut = 'expiré'  THEN 1 ELSE 0 END) AS expires
        ")->first();

        $bientot = Contrat::where('statut', 'actif')->whereNotNull('date_fin')
            ->whereBetween('date_fin', [$aujourdhui, $limite30])->count();

        $stats = [
            'total'    => (int) $statsRaw->total,
            'actifs'   => (int) $statsRaw->actifs,
            'resilies' => (int) $statsRaw->resilies,
            'expires'  => (int) $statsRaw->expires,
            'bientot'  => $bientot,
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

        $biens = Bien::where('agency_id', $agencyId)
            ->where('statut', 'disponible')
            ->select(['id', 'agency_id', 'proprietaire_id', 'reference', 'type', 'adresse', 'ville', 'loyer_mensuel', 'taux_commission', 'meuble', 'tom_mensuelle'])
            ->with(['proprietaire:id,name'])
            ->orderBy('reference')
            ->get();

        $locataires = User::where('role', 'locataire')
            ->where('agency_id', $agencyId)
            ->select(['id', 'name', 'email', 'telephone'])
            ->orderBy('name')
            ->get();

        $bienPreselectionne = $request->has('bien_id')
            ? Bien::select(['id', 'reference', 'loyer_mensuel', 'taux_commission', 'meuble', 'tom_mensuelle', 'type'])->find($request->bien_id)
            : null;

        // Renouvellement : pré-charger les données de l'ancien contrat
        $fromContrat = null;
        if ($request->filled('from_contrat')) {
            $fromContrat = Contrat::with(['bien', 'locataire'])
                ->select(['id', 'bien_id', 'locataire_id', 'type_bail', 'loyer_nu',
                          'charges_mensuelles', 'tom_amount', 'indexation_annuelle',
                          'taux_commission_snapshot', 'date_fin'])
                ->find($request->from_contrat);
            if ($fromContrat) {
                $bienPreselectionne = $fromContrat->bien;
                // Inclure le bien loué dans la liste pour ce renouvellement
                if ($bienPreselectionne && ! $biens->contains('id', $bienPreselectionne->id)) {
                    $biens->prepend($bienPreselectionne->load('proprietaire:id,name'));
                }
            }
        }

        $typesBail = Contrat::TYPES_BAIL;

        return view('admin.contrats.create', compact(
            'biens',
            'locataires',
            'bienPreselectionne',
            'fromContrat',
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

        $loyerNu           = (float) $validated['loyer_nu'];
        $chargesMensuelles = (float) ($validated['charges_mensuelles'] ?? 0);
        $tomAmount         = (float) ($validated['tom_amount'] ?? 0);

        // ── Mode de facturation des charges → assujettissement TVA charges ──
        // Le mode n'a de sens que si des charges existent. Défaut prudent : débours (0%).
        // forfait → charges_assujetties_tva = true (18%) ; debours → false (0%).
        $modeCharges = $chargesMensuelles > 0
            ? ($validated['mode_facturation_charges'] ?? 'debours')
            : null;
        $chargesAssujetties = $modeCharges === 'forfait';

        // ── Overrides fiscaux du loyer : uniquement si le formulaire les fournit.
        // Sinon, on NE force RIEN → ContratObserver dérive automatiquement
        // loyer_assujetti_tva/taux_tva_loyer depuis type_bail + bien.meuble
        // (règle 'tva_loyer_assujettissement') et brs_applicable depuis le locataire.
        // Forcer $request->boolean() ici mettrait 0% à tout bail créé via le
        // formulaire simplifié (le champ étant absent = false ≠ null override).
        $fiscalOverrides = [];
        if ($request->has('loyer_assujetti_tva')) {
            $assujetti = $request->boolean('loyer_assujetti_tva');
            $fiscalOverrides['loyer_assujetti_tva'] = $assujetti;
            $fiscalOverrides['taux_tva_loyer']      = $validated['taux_tva_loyer']
                ?? ($assujetti ? FiscalService::TVA_TAUX : 0.0);
        }
        if ($request->has('brs_applicable')) {
            $fiscalOverrides['brs_applicable'] = $request->boolean('brs_applicable');
        }
        if (($validated['taux_brs_manuel'] ?? null) !== null) {
            $fiscalOverrides['taux_brs_manuel'] = $validated['taux_brs_manuel'];
        }

        $referenceBail = ! empty($validated['reference_bail'])
            ? trim($validated['reference_bail'])
            : null;

        $agencyId = Auth::user()->agency_id;

        $contrat = DB::transaction(function () use ($validated, $loyerNu, $chargesMensuelles, $tomAmount, $modeCharges, $chargesAssujetties, $fiscalOverrides, $referenceBail, $request, $agencyId) {
            $bien = Bien::withoutGlobalScopes()
                ->where('agency_id', $agencyId)
                ->lockForUpdate()
                ->findOrFail($validated['bien_id']);

            if (Contrat::where('bien_id', $bien->id)->where('statut', 'actif')->exists()) {
                throw ValidationException::withMessages([
                    'bien_id' => 'Ce bien a déjà un contrat actif.',
                ]);
            }

            // TOM : si non saisie sur le bail, reprend la TOM de référence du bien.
            if (! $request->filled('tom_amount') && (float) $bien->tom_mensuelle > 0) {
                $tomAmount = (float) $bien->tom_mensuelle;
            }
            $loyerContractuel = round($loyerNu + $chargesMensuelles + $tomAmount, 2);

            $contrat = Contrat::create([
                'bien_id'             => $validated['bien_id'],
                'locataire_id'        => $validated['locataire_id'],
                'date_debut'          => $validated['date_debut'],
                'date_fin'            => $validated['date_fin'] ?? null,
                'loyer_nu'            => $loyerNu,
                'loyer_contractuel'   => $loyerContractuel,
                'charges_mensuelles'  => $chargesMensuelles,
                'tom_amount'          => $tomAmount,
                'caution'             => $validated['caution'] ?? 0,
                'statut'              => 'actif',
                'type_bail'           => $validated['type_bail'],
                'frais_agence'        => $validated['frais_agence'] ?? 0,
                'indexation_annuelle' => $validated['indexation_annuelle'] ?? 0,
                'nombre_mois_caution' => $validated['nombre_mois_caution'] ?? 1,
                'garant_nom'          => $validated['garant_nom'] ?? null,
                'garant_telephone'    => $validated['garant_telephone'] ?? null,
                'garant_adresse'      => $validated['garant_adresse'] ?? null,
                'garant_cni'          => $validated['garant_cni'] ?? null,
                'observations'           => $validated['observations'] ?? null,
                'clauses_particulieres'  => $validated['clauses_particulieres'] ?? null,
                'reference_bail'      => $referenceBail,
                // mode_facturation_charges pilote charges_assujetties_tva (regles_fiscales 'tva_charges')
                'mode_facturation_charges' => $modeCharges,
                'charges_assujetties_tva'  => $chargesAssujetties,
                // ── DGID
                'date_enregistrement_dgid' => $validated['date_enregistrement_dgid'] ?? null,
                'numero_quittance_dgid'    => $validated['numero_quittance_dgid'] ?? null,
                'montant_droit_de_bail'    => $validated['montant_droit_de_bail'] ?? null,
                'enregistrement_exonere'   => $request->boolean('enregistrement_exonere'),
                // ── Overrides fiscaux loyer/BRS (vides = laissés à l'Observer)
            ] + $fiscalOverrides);

            if (empty($referenceBail)) {
                $contrat->update([
                    'reference_bail' => sprintf('BIMO-%s-%s', now()->year, str_pad($contrat->id, 5, '0', STR_PAD_LEFT)),
                ]);
            }

            $bien->update(['statut' => 'loue']);

            return $contrat;
        });

        // ── Génère tout de suite la quittance du mois courant ───────────────
        // Pour que la ligne existe immédiatement (Quittances = Dashboard), sans
        // attendre le prochain rent:generate. On ne génère que si le bail a déjà
        // commencé ce mois-ci. N'échoue jamais la création du contrat.
        if ($contrat->date_debut && \Carbon\Carbon::parse($contrat->date_debut)->lte(now()->endOfMonth())) {
            try {
                app(\App\Services\QuittanceGenerator::class)
                    ->genererPourContrat($contrat, now()->startOfMonth(), 'création contrat');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Quittance non générée à la création du contrat', [
                    'contrat_id' => $contrat->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('admin.contrats.show', $contrat)
            ->with('success', "Contrat {$contrat->reference_bail} créé ✓");
    }

    // ─────────────────────────────────────────────────────────────────────
    // APERÇU FISCAL (AJAX) — pour le formulaire de création/édition
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Calcule un aperçu fiscal (loyer TTC, TVA, net) AVANT enregistrement.
     *
     * Construit un Contrat transient (non sauvegardé) à partir des valeurs du
     * formulaire, puis réutilise FiscalContext::fromContrat() + FiscalService.
     * Aucune règle fiscale n'est dupliquée : le moteur reste la source unique.
     */
    public function apercuFiscal(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('create', Contrat::class);

        $agencyId = Auth::user()->agency_id;

        // NB : est_personne_morale_is vit sur le profil Proprietaire, pas sur User.
        // FiscalContext::fromContrat le lit sur $bien->proprietaire (User) → null → BRS
        // par défaut applicable (personne physique). On charge juste l'id pour rester
        // cohérent avec le chemin réel (QuittanceGenerator), sans projeter de colonne absente.
        $bien = Bien::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->with([
                'proprietaire:id',
                // Profil propriétaire : assujettissement TVA (F2) + personne morale IS (BRS)
                'proprietaire.proprietaire:user_id,assujetti_tva,est_personne_morale_is,brs_dispense',
            ])
            ->find($request->input('bien_id'));

        $loyerNu = (float) $request->input('loyer_nu', 0);

        if (! $bien || $loyerNu <= 0) {
            return response()->json(['ok' => false]);
        }

        $charges  = (float) $request->input('charges_mensuelles', 0);
        $tom      = $request->filled('tom_amount')
            ? (float) $request->input('tom_amount')
            : (float) $bien->tom_mensuelle;
        $typeBail = in_array($request->input('type_bail'), ['habitation', 'commercial', 'mixte', 'saisonnier'], true)
            ? $request->input('type_bail')
            : 'habitation';
        $mode     = in_array($request->input('mode_facturation_charges'), array_keys(Contrat::MODES_FACTURATION_CHARGES), true)
            ? $request->input('mode_facturation_charges')
            : null;

        // Contrat transient : aucune écriture en base.
        $contrat = new Contrat();
        $contrat->type_bail                = $typeBail;
        $contrat->loyer_nu                 = $loyerNu;
        $contrat->charges_mensuelles       = $charges;
        $contrat->tom_amount               = $tom;
        $contrat->mode_facturation_charges = $mode;
        // Débours (ou mode absent) → 0% ; forfait → 18%. Cohérent avec le store().
        $contrat->charges_assujetties_tva  = ($charges > 0 && $mode === 'forfait');
        $contrat->setRelation('bien', $bien);
        // Agence courante → pilote la TVA commission/frais (F2) dans l'aperçu.
        $contrat->setRelation('agency', Auth::user()->agency);

        $result = FiscalService::calculer(FiscalContext::fromContrat($contrat));

        return response()->json([
            'ok'                => true,
            'loyer_ht'          => $result->loyerHt,
            'taux_tva_loyer'    => $result->tauxTvaLoyerApplique,
            'tva_loyer'         => $result->tvaLoyer,
            'loyer_ttc'         => $result->loyerTtc,
            'charges'           => $result->chargesAmount,
            'tva_charges'       => $result->tvaCharges,
            'charges_ttc'       => $result->chargesTtc,
            'tom'               => $result->tomAmount,
            'montant_encaisse'  => $result->montantEncaisse,
            'commission_ht'     => $result->commissionHt,
            'tva_commission'    => $result->tvaCommission,
            'commission_ttc'    => $result->commissionTtc,
            'net_a_verser'      => $result->netAVerserProprietaire,
            'loyer_assujetti'   => $result->loyerAssujetti,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // MON CONTRAT — route sans paramètre pour la sidebar locataire
    // ─────────────────────────────────────────────────────────────────────

    public function monContrat(): \Illuminate\Http\RedirectResponse
    {
        $contrat = Contrat::where('locataire_id', Auth::id())
            ->where('statut', 'actif')
            ->first();

        if (! $contrat) {
            return redirect()->route('locataire.dashboard')
                ->with('info', 'Aucun contrat actif trouvé.');
        }

        return redirect()->route('locataire.contrat.show', $contrat);
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
                COALESCE(SUM(montant_encaisse), 0)          AS total_paye,
                COALESCE(SUM(net_a_verser_proprietaire), 0) AS total_net,
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
            ->select(['id', 'contrat_id', 'agency_id', 'periode', 'montant_encaisse', 'net_proprietaire', 'net_a_verser_proprietaire', 'commission_ttc', 'mode_paiement', 'date_paiement', 'statut', 'reference_paiement'])
            ->orderByDesc('periode')
            ->get();

        // Locataire → vue dédiée sans liens admin
        if (Auth::user()->role === 'locataire') {
            return view('locataire.contrat', compact(
                'contrat', 'totalPaye', 'nbPaiements', 'prochainePeriode', 'paiements'
            ));
        }

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
    // BAIL FORMEL PDF — format notarial sénégalais avec articles numérotés
    // ─────────────────────────────────────────────────────────────────────

    public function bailFormelPdf(Contrat $contrat): \Illuminate\Http\Response
    {
        $this->authorize('view', $contrat);

        $agency = Auth::user()->agency;

        // Pas de blocage : si l'agence n'a pas encore de signature, le PDF affiche
        // un espace vide à signer à la main (voir la vue). L'usage n'est jamais bloqué.

        $contrat->load([
            'bien:id,reference,titre,type,adresse,ville,quartier,commune,surface_m2,nombre_pieces,meuble',
            'bien.proprietaire:id,name,email,telephone,adresse',
            'bien.proprietaire.proprietaire',
            'locataire:id,name,email,telephone,adresse',
            'locataire.locataire',
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('contrats.pdf.bail-formel', compact(
            'contrat', 'agency'
        ))->setPaper('a4', 'portrait');

        $filename = 'bail-formel-' . ($contrat->reference_bail ?? 'contrat-' . $contrat->id) . '.pdf';

        return $pdf->download(str_replace(' ', '-', $filename));
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
            'caution'             => $validated['caution'] ?? 0,
            'type_bail'           => $validated['type_bail'],
            'frais_agence'        => $validated['frais_agence'] ?? 0,
            'indexation_annuelle' => $validated['indexation_annuelle'] ?? 0,
            'nombre_mois_caution' => $validated['nombre_mois_caution'] ?? 1,
            'garant_nom'          => $validated['garant_nom'] ?? null,
            'garant_telephone'    => $validated['garant_telephone'] ?? null,
            'garant_adresse'      => $validated['garant_adresse'] ?? null,
            'garant_cni'          => $validated['garant_cni'] ?? null,
            'reference_bail'      => ! empty($validated['reference_bail'])
                ? trim($validated['reference_bail'])
                : $contrat->reference_bail,
            'observations'          => $validated['observations'] ?? null,
            'clauses_particulieres' => $validated['clauses_particulieres'] ?? null,
            // ── Fiscal
            // $request->has() vérifie la présence du champ dans la requête.
            // Quand features.fiscalite=false, les champs fiscaux sont absents du formulaire ;
            // on conserve alors la valeur existante plutôt que d'écraser avec false.
            'loyer_assujetti_tva'      => $request->has('loyer_assujetti_tva')
                ? $request->boolean('loyer_assujetti_tva')
                : $contrat->loyer_assujetti_tva,
            'taux_tva_loyer'           => $validated['taux_tva_loyer'] ?? $contrat->taux_tva_loyer ?? 0,
            'brs_applicable'           => $request->has('brs_applicable')
                ? $request->boolean('brs_applicable')
                : $contrat->brs_applicable,
            'taux_brs_manuel'          => $validated['taux_brs_manuel'] ?? $contrat->taux_brs_manuel,
            // mode_facturation_charges pilote charges_assujetties_tva (regles_fiscales 'tva_charges').
            // Débours = 0%, forfait = 18%. Sans charges → mode null, TVA charges désactivée.
            'mode_facturation_charges' => $request->has('mode_facturation_charges')
                ? ($chargesMensuelles > 0 ? $request->input('mode_facturation_charges') : null)
                : $contrat->mode_facturation_charges,
            'charges_assujetties_tva'  => $request->has('mode_facturation_charges')
                ? ($chargesMensuelles > 0 && $request->input('mode_facturation_charges') === 'forfait')
                : ($request->has('charges_assujetties_tva')
                    ? $request->boolean('charges_assujetties_tva')
                    : $contrat->charges_assujetties_tva),
            // ── DGID
            'date_enregistrement_dgid' => $validated['date_enregistrement_dgid'] ?? $contrat->date_enregistrement_dgid,
            'numero_quittance_dgid'    => $validated['numero_quittance_dgid']    ?? $contrat->numero_quittance_dgid,
            'montant_droit_de_bail'    => $validated['montant_droit_de_bail']    ?? $contrat->montant_droit_de_bail,
            'enregistrement_exonere'   => $request->has('enregistrement_exonere')
                ? $request->boolean('enregistrement_exonere')
                : $contrat->enregistrement_exonere,
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
    // ENREGISTREMENT DU BAIL (DGID) — marquer comme effectué
    // ─────────────────────────────────────────────────────────────────────

    public function marquerEnregistre(Request $request, Contrat $contrat): RedirectResponse
    {
        $this->authorize('update', $contrat);

        $validated = $request->validate([
            'droit_enreg_date_effectue' => ['nullable', 'date', 'before_or_equal:today'],
            'numero_quittance_dgid'     => ['nullable', 'string', 'max:60'],
            'droit_enreg_nombre_feuilles' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        // Si le nombre de feuilles est ajusté, l'Observer recalcule le timbre au save.
        if ($request->filled('droit_enreg_nombre_feuilles')) {
            $contrat->droit_enreg_nombre_feuilles = (int) $validated['droit_enreg_nombre_feuilles'];
        }

        $contrat->droit_enreg_effectue      = true;
        $contrat->droit_enreg_date_effectue = $validated['droit_enreg_date_effectue'] ?? now()->toDateString();
        // Trace DGID (réutilise les champs existants)
        $contrat->date_enregistrement_dgid  = $contrat->droit_enreg_date_effectue;
        if ($request->filled('numero_quittance_dgid')) {
            $contrat->numero_quittance_dgid = $validated['numero_quittance_dgid'];
        }
        $contrat->save();

        return back()->with('success', 'Bail marqué comme enregistré à la DGID ✓');
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

        DB::transaction(function () use ($contrat) {
            $contrat->update(['statut' => ContratStatut::Resilie->value]);
            $contrat->bien->update(['statut' => BienStatut::Disponible->value]);
        });

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
            'password'  => ['required', Password::min(8)->letters()->numbers()],
        ], ['email.unique' => 'Cet email est déjà utilisé.']);

        $user = DB::transaction(function () use ($validated) {
            $user                    = new User();
            $user->name              = $validated['name'];
            $user->email             = $validated['email'];
            $user->telephone         = $validated['telephone'] ?? null;
            $user->password          = Hash::make($validated['password']);
            $user->role              = UserRole::Locataire->value;
            $user->agency_id         = Auth::user()->agency_id;
            $user->email_verified_at = now();
            $user->save();

            // Créer le profil Locataire pour que BRS et taux d'effort soient calculables
            \App\Models\Locataire::create([
                'user_id'        => $user->id,
                'type_locataire' => 'particulier',
                'est_entreprise' => false,
            ]);

            return $user;
        });

        return response()->json(['success' => true, 'id' => $user->id, 'name' => $user->name]);
    }
}
