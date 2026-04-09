<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Paiement;
use App\Services\FiscalContext;
use App\Services\FiscalService;
use App\Services\PaiementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaiementController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly FiscalService   $fiscalService,
        private readonly PaiementService $paiementService,
    ) {}

    // ─── LISTE des paiements (resource index) ─────────────────────────────────

    /**
     * GET /admin/paiements
     * Liste paginée de tous les paiements de l'agence avec filtres.
     * CORRIGÉ : plus de Contrat $contrat en paramètre (incompatible resource)
     */
    public function index(Request $request): View
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;

        $query = Paiement::with([
            'contrat:id,bien_id,locataire_id,reference_bail',
            'contrat.bien:id,reference,adresse,ville',
            'contrat.locataire:id,name,telephone',
        ])->select([
            'id', 'contrat_id', 'agency_id', 'periode', 'date_paiement',
            'montant_encaisse', 'net_proprietaire', 'commission_ttc',
            'mode_paiement', 'statut', 'reference_paiement', 'reference_bail',
        ]);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('mois') && $request->filled('annee')) {
            $debut = Carbon::create($request->annee, $request->mois, 1)->startOfMonth();
            $fin   = $debut->copy()->endOfMonth();
            $query->whereBetween('periode', [$debut->toDateString(), $fin->toDateString()]);
        }
        if ($request->filled('contrat_id')) {
            $query->where('contrat_id', $request->contrat_id);
        }

        $paiements = $query->orderByDesc('periode')->paginate(20)->withQueryString();

        // Stats rapides pour l'en-tête
        $stats = Paiement::where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->selectRaw('
                COALESCE(SUM(montant_encaisse), 0) AS total_encaisse,
                COALESCE(SUM(commission_ttc), 0)   AS total_commission,
                COALESCE(SUM(net_proprietaire), 0) AS total_net,
                COUNT(*)                            AS nb_total
            ')->first();

        return view('admin.paiements.index', compact('paiements', 'stats'));
    }

    // ─── FORMULAIRE de création ────────────────────────────────────────────────

    /**
     * GET /admin/paiements/create?contrat_id=X
     */
    public function create(Request $request): View
    {
        $this->authorize('isAdmin');

        $contrat = null;

        if ($request->filled('contrat_id')) {
            $contrat = Contrat::with([
                'bien',
                'locataire',
                'locataire.locataire',
            ])->findOrFail($request->contrat_id);
        }

        $contrats = Contrat::where('statut', 'actif')
            ->with('bien:id,reference,adresse', 'locataire:id,name')
            ->select(['id', 'bien_id', 'locataire_id', 'reference_bail'])
            ->orderBy('reference_bail')
            ->get();

        return view('admin.paiements.create', compact('contrat', 'contrats'));
    }

    // ─── ENREGISTREMENT ───────────────────────────────────────────────────────

    /**
     * POST /admin/paiements
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'contrat_id'    => ['required', 'exists:contrats,id'],
            'periode'       => ['required', 'date'],
            'date_paiement' => ['required', 'date', 'before_or_equal:today'],
            'mode_paiement' => ['required', 'in:especes,virement,wave,orange_money,free_money,cheque'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ], [
            'contrat_id.required'    => 'Veuillez sélectionner un contrat.',
            'contrat_id.exists'      => 'Ce contrat est introuvable.',
            'periode.required'       => 'La période est obligatoire.',
            'date_paiement.required' => 'La date de paiement est obligatoire.',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
        ]);

        $contrat = Contrat::with(['bien', 'locataire.locataire'])
            ->findOrFail($validated['contrat_id']);

        // Vérifier qu'un paiement valide n'existe pas déjà pour cette période
        $periodeDate = Carbon::parse($validated['periode'])->startOfMonth()->toDateString();
        $existe = Paiement::where('contrat_id', $contrat->id)
            ->where('statut', 'valide')
            ->where('periode', $periodeDate)
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors(['periode' => 'Un paiement valide existe déjà pour cette période.']);
        }

        $paiement = $this->paiementService->enregistrerPaiement($contrat, [
            'periode'       => $validated['periode'],
            'date_paiement' => $validated['date_paiement'],
            'mode_paiement' => $validated['mode_paiement'],
            'notes'         => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.contrats.show', $contrat)
            ->with('success', "Paiement enregistré avec succès — Réf. {$paiement->reference_paiement}");
    }

    // ─── DÉTAIL d'un paiement ─────────────────────────────────────────────────

    /**
     * GET /admin/paiements/{paiement}
     */
    public function show(Paiement $paiement): View
    {
        $this->authorize('isAdmin');

        $paiement->load([
            'contrat.bien',
            'contrat.locataire',
            'contrat.bien.proprietaire',
        ]);

        return view('admin.paiements.show', compact('paiement'));
    }

    // ─── FORMULAIRE modification ───────────────────────────────────────────────

    /**
     * GET /admin/paiements/{paiement}/edit
     */
    public function edit(Paiement $paiement): View
    {
        $this->authorize('isAdmin');

        if ($paiement->statut !== 'en_attente') {
            return redirect()
                ->route('admin.paiements.show', $paiement)
                ->with('warning', 'Seul un paiement en attente peut être modifié.');
        }

        $paiement->load(['contrat.bien', 'contrat.locataire']);

        return view('admin.paiements.edit', compact('paiement'));
    }

    // ─── MISE À JOUR ──────────────────────────────────────────────────────────

    /**
     * PUT|PATCH /admin/paiements/{paiement}
     */
    public function update(Request $request, Paiement $paiement): RedirectResponse
    {
        $this->authorize('isAdmin');

        if ($paiement->statut === 'annule') {
            return back()->withErrors(['general' => 'Un paiement annulé ne peut pas être modifié.']);
        }

        $validated = $request->validate([
            'mode_paiement' => ['required', 'in:especes,virement,wave,orange_money,free_money,cheque'],
            'date_paiement' => ['required', 'date'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $paiement->update($validated);

        return redirect()
            ->route('admin.paiements.show', $paiement)
            ->with('success', 'Paiement mis à jour.');
    }

    // ─── ANNULATION ───────────────────────────────────────────────────────────

    /**
     * PATCH /admin/paiements/{paiement}/annuler
     */
    public function annuler(Request $request, Paiement $paiement): RedirectResponse
    {
        $this->authorize('isAdmin');

        if ($paiement->statut === 'annule') {
            return back()->withErrors(['general' => 'Ce paiement est déjà annulé.']);
        }

        $paiement->update([
            'statut'    => 'annule',
            'annule_le' => now(),
            'annule_par'=> Auth::id(),
            'notes'     => ($paiement->notes ? $paiement->notes . "\n" : '')
                         . '[Annulé le ' . now()->format('d/m/Y à H:i') . ' par ' . Auth::user()->name . ']',
        ]);

        return back()->with('success', 'Paiement annulé.');
    }

    // ─── SUPPRESSION ──────────────────────────────────────────────────────────

    /**
     * DELETE /admin/paiements/{paiement}
     */
    public function destroy(Paiement $paiement): RedirectResponse
    {
        $this->authorize('isAdmin');

        // On n'autorise la suppression que pour les paiements en attente
        if ($paiement->statut !== 'en_attente') {
            return back()->withErrors(['general' => 'Seul un paiement en attente peut être supprimé. Utilisez Annuler pour les paiements validés.']);
        }

        $contratId = $paiement->contrat_id;
        $paiement->delete();

        return redirect()
            ->route('admin.contrats.show', $contratId)
            ->with('success', 'Paiement supprimé.');
    }

    // ─── TÉLÉCHARGEMENT PDF ────────────────────────────────────────────────────

    /**
     * GET /admin/paiements/{paiement}/pdf
     * GET /proprietaire/mes-paiements/{paiement}/pdf
     * GET /locataire/mes-paiements/{paiement}/pdf
     * CORRIGÉ : chemin de vue corrigé → paiements.pdf.quittance
     */
    public function downloadPDF(Paiement $paiement)
    {
        $paiement->load([
            'contrat.bien',
            'contrat.locataire',
            'contrat.bien.proprietaire',
        ]);

        $contrat     = $paiement->contrat;
        $bien        = $contrat->bien;
        $locataire   = $contrat->locataire;
        $proprietaire = $bien->proprietaire;
        $agence      = Auth::user()->agency;

        // Données pour la vue PDF
        $agenceData = [
            'nom'       => $agence->name,
            'adresse'   => $agence->adresse,
            'telephone' => $agence->telephone,
            'email'     => $agence->email,
            'ninea'     => $agence->ninea,
        ];

        // Utiliser la quittance fiscale si TVA ou BRS, simple sinon
        $vue = ($paiement->tva_loyer > 0 || $paiement->brs_amount > 0)
            ? 'paiements.pdf.quittance-fiscale'
            : 'paiements.pdf.quittance';

        $pdf = Pdf::loadView($vue, compact(
            'paiement', 'contrat', 'bien', 'locataire', 'proprietaire', 'agenceData'
        ))
            ->setPaper('A4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 96)
            ->setOption('isRemoteEnabled', false);

        $filename = sprintf(
            'Quittance-%s-%s.pdf',
            $paiement->reference_paiement ?? $paiement->id,
            $paiement->periode?->format('Y-m') ?? now()->format('Y-m')
        );

        return $pdf->download($filename);
    }

    // ─── APERÇU FISCAL (AJAX) ─────────────────────────────────────────────────

    /**
     * GET /admin/paiements/fiscal-preview/{contrat}
     * Retourne le calcul fiscal en JSON pour le formulaire (preview temps réel).
     */
    public function fiscalPreview(Contrat $contrat): JsonResponse
    {
        $contrat->loadMissing(['bien', 'locataire.locataire']);

        $ctx    = FiscalContext::fromContrat($contrat);
        $result = FiscalService::calculer($ctx);

        return response()->json([
            'loyer_ht'                  => $result->loyerHt,
            'tva_loyer'                 => $result->tvaLoyer,
            'loyer_ttc'                 => $result->loyerTtc,
            'charges_amount'            => $result->chargesAmount,
            'tom_amount'                => $result->tomAmount,
            'montant_encaisse'          => $result->montantEncaisse,
            'commission_ht'             => $result->commissionHt,
            'tva_commission'            => $result->tvaCommission,
            'commission_ttc'            => $result->commissionTtc,
            'net_proprietaire'          => $result->netProprietaire,
            'brs_amount'                => $result->brsAmount,
            'net_a_verser_proprietaire' => $result->netAVerserProprietaire,
            'regime_fiscal'             => $result->regimeFiscal,
            'loyer_assujetti'           => $result->loyerAssujetti,
            'brs_applicable'            => $result->brsApplicable,
        ]);
    }

    // ─── DERNIÈRE PÉRIODE (AJAX) ──────────────────────────────────────────────

    /**
     * GET /admin/paiements/dernier-periode/{contrat}
     * Retourne la prochaine période à facturer pour un contrat.
     */
    public function dernierePeriode(Contrat $contrat): JsonResponse
    {
        $prochaine = $this->paiementService->prochainePeriode($contrat);

        return response()->json([
            'periode'         => $prochaine->toDateString(),
            'periode_label'   => $prochaine->locale('fr')->translatedFormat('F Y'),
            'periode_input'   => $prochaine->format('Y-m-d'),
        ]);
    }

    // ─── MES PAIEMENTS (Locataire) ────────────────────────────────────────────

    /**
     * GET /locataire/mes-paiements
     */
    public function mesPaiements(Request $request): View
    {
        $this->authorize('isLocataire');

        $user = Auth::user();

        $paiements = Paiement::whereHas('contrat', fn($q) => $q->where('locataire_id', $user->id))
            ->where('statut', 'valide')
            ->with(['contrat.bien:id,reference,adresse,ville'])
            ->select([
                'id', 'contrat_id', 'periode', 'date_paiement',
                'montant_encaisse', 'mode_paiement', 'reference_paiement', 'statut',
            ])
            ->orderByDesc('periode')
            ->paginate(12);

        return view('locataire.paiements', compact('paiements'));
    }
}