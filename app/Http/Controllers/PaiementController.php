<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Paiement;
use App\Notifications\PaiementProprietaireNotification;
use App\Services\FiscalContext;
use App\Services\FiscalService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaiementController extends Controller
{
    use AuthorizesRequests;

    const MODES_PAIEMENT = [
        'especes'         => 'Espèces',
        'virement'        => 'Virement bancaire',
        'cheque'          => 'Chèque',
        'wave'            => 'Wave',
        'orange_money'    => 'Orange Money',
        'free_money'      => 'Free Money',
        'e_money'         => 'E-Money',
    ];

    // ─────────────────────────────────────────────────────────────────────
    // LISTE
    // ─────────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $this->authorize('isStaff');

        $agencyId = Auth::user()->agency_id;

        $query = Paiement::where('agency_id', $agencyId)
            ->with([
                'contrat:id,bien_id,locataire_id,reference_bail',
                'contrat.bien:id,reference,adresse,ville',
                'contrat.locataire:id,name',
            ])
            ->select([
                'id', 'agency_id', 'contrat_id', 'periode', 'date_paiement',
                'montant_encaisse', 'net_proprietaire', 'commission_ttc',
                'mode_paiement', 'statut', 'reference_paiement',
            ]);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('contrat_id')) {
            $query->where('contrat_id', $request->contrat_id);
        }
        if ($request->filled('mois')) {
            $query->whereRaw('DATE_FORMAT(periode, "%Y-%m") = ?', [$request->mois]);
        }

        $paiements = $query->orderByDesc('date_paiement')->paginate(20)->withQueryString();

        // Stats mois courant
        $statsRaw = Paiement::where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereYear('periode', now()->year)
            ->whereMonth('periode', now()->month)
            ->selectRaw('
                COALESCE(SUM(montant_encaisse), 0) AS total_loyers,
                COALESCE(SUM(commission_ttc), 0)   AS total_commissions,
                COALESCE(SUM(net_proprietaire), 0) AS total_net,
                COUNT(*)                            AS nb_payes
            ')
            ->first();

        $stats = [
            'total_loyers'      => (float) ($statsRaw->total_loyers      ?? 0),
            'total_commissions' => (float) ($statsRaw->total_commissions  ?? 0),
            'total_net'         => (float) ($statsRaw->total_net          ?? 0),
            'nb_payes'          => (int)   ($statsRaw->nb_payes           ?? 0),
        ];

        return view('paiements.index', compact('paiements', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // FORMULAIRE CRÉATION
    // ─────────────────────────────────────────────────────────────────────

    public function create(Request $request): View
    {
        $this->authorize('isStaff');

        $agencyId = Auth::user()->agency_id;

        // Contrat présélectionné si passé en query string
        $contrat = null;
        if ($request->filled('contrat_id')) {
            $contrat = Contrat::with([
                'bien:id,reference,adresse,ville,taux_commission,loyer_mensuel',
                'locataire:id,name,email',
            ])->where('agency_id', $agencyId)
              ->where('statut', 'actif')
              ->find($request->contrat_id);
        }

        // Liste des contrats actifs pour le select
        $contrats = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->with([
                'bien:id,reference,adresse,ville,taux_commission',
                'locataire:id,name',
            ])
            ->select(['id', 'bien_id', 'locataire_id', 'loyer_nu',
                      'charges_mensuelles', 'tom_amount', 'loyer_contractuel',
                      'reference_bail', 'date_debut'])
            ->orderBy('reference_bail')
            ->get();

        $modesPaiement = self::MODES_PAIEMENT;
        $datePaiement  = now()->format('Y-m-d');
        $periode       = now()->startOfMonth()->format('Y-m-d');

        return view('paiements.create', compact(
            'contrat', 'contrats', 'modesPaiement', 'datePaiement', 'periode'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // ENREGISTREMENT
    // ─────────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('isStaff');

        $agencyId = Auth::user()->agency_id;

        $validated = $request->validate([
            'contrat_id'    => ['required', 'exists:contrats,id'],
            'periode'       => ['required', 'date'],
            'date_paiement' => ['required', 'date'],
            'mode_paiement' => ['required', 'in:' . implode(',', array_keys(self::MODES_PAIEMENT))],
            'caution_percue'=> ['nullable', 'numeric', 'min:0'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ], [
            'contrat_id.required'    => 'Veuillez sélectionner un contrat.',
            'periode.required'       => 'La période est obligatoire.',
            'date_paiement.required' => 'La date de paiement est obligatoire.',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
        ]);

        // Vérifier appartenance du contrat + charger les relations nécessaires au calcul fiscal
        $contrat = Contrat::with([
                'bien',
                'locataire.locataire',
            ])
            ->where('agency_id', $agencyId)
            ->findOrFail($validated['contrat_id']);

        // Vérifier doublon : un seul paiement valide par contrat par mois
        $periodeDebut = Carbon::parse($validated['periode'])->startOfMonth();
        $doublonExiste = Paiement::where('contrat_id', $contrat->id)
            ->where('periode', $periodeDebut)
            ->where('statut', '!=', 'annule')
            ->exists();

        if ($doublonExiste) {
            return back()
                ->withInput()
                ->withErrors(['periode' => 'Un paiement valide existe déjà pour cette période.']);
        }

        // Calcul fiscal via FiscalService (TVA loyer, BRS, commission, nets)
        $ctx    = FiscalContext::fromContrat($contrat);
        $result = FiscalService::calculer($ctx);

        // Vérifier si c'est le premier paiement du contrat
        $estPremier = Paiement::where('contrat_id', $contrat->id)->count() === 0;

        // Référence paiement unique
        $reference = 'PAY-' . strtoupper(Str::random(8));

        $paiement = Paiement::create(array_merge(
            $result->toPaiementFields(),
            [
                'agency_id'               => $agencyId,
                'contrat_id'              => $contrat->id,
                'periode'                 => Carbon::parse($validated['periode'])->startOfMonth(),
                'date_paiement'           => $validated['date_paiement'],
                'montant_encaisse'        => $result->montantEncaisse,
                'mode_paiement'           => $validated['mode_paiement'],
                'taux_commission_applique'=> $ctx->tauxCommission,
                'caution_percue'          => $validated['caution_percue'] ?? 0,
                'est_premier_paiement'    => $estPremier,
                'statut'                  => 'valide',
                'reference_paiement'      => $reference,
                'reference_bail'          => $contrat->reference_bail,
                'notes'                   => $validated['notes'] ?? null,
            ]
        ));

        // Notifier le propriétaire par email
        try {
            $proprio = $contrat->bien->proprietaire ?? null;
            if ($proprio && $proprio->email) {
                $paiement->load('contrat.bien', 'contrat.locataire');
                $proprio->notify(new PaiementProprietaireNotification($paiement));
            }
        } catch (\Throwable $e) {
            Log::warning('Notification propriétaire non envoyée', [
                'paiement_id' => $paiement->id,
                'error'       => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.contrats.show', $contrat)
            ->with('success', 'Paiement enregistré ✓ — ' . number_format($result->montantEncaisse, 0, ',', ' ') . ' FCFA');
    }

    // ─────────────────────────────────────────────────────────────────────
    // DÉTAIL
    // ─────────────────────────────────────────────────────────────────────

    public function show(Paiement $paiement): View
    {
        $this->authorize('isStaff');

        $paiement->load([
            'contrat.bien.proprietaire:id,name,email,telephone',
            'contrat.locataire:id,name,email,telephone',
        ]);

        return view('paiements.show', compact('paiement'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // ANNULATION
    // ─────────────────────────────────────────────────────────────────────

    public function annuler(Paiement $paiement): RedirectResponse
    {
        $this->authorize('isStaff');

        if ($paiement->statut !== 'valide') {
            return back()->withErrors(['general' => 'Seul un paiement valide peut être annulé.']);
        }

        $paiement->update(['statut' => 'annule']);

        return back()->with('success', 'Paiement annulé ✓');
    }

    // ─────────────────────────────────────────────────────────────────────
    // TÉLÉCHARGEMENT PDF QUITTANCE
    // ─────────────────────────────────────────────────────────────────────

    public function downloadPDF(Paiement $paiement): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        $this->authorize('telechargerQuittance', $paiement);

        $paiement->load([
            'contrat.bien.proprietaire:id,name,email,telephone,adresse',
            'contrat.locataire:id,name,email,telephone,adresse',
            'contrat.bien.agency',
        ]);

        $agence = Auth::user()->agency;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('paiements.pdf.quittance', compact('paiement', 'agence'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'quittance-' . $paiement->reference_paiement . '.pdf';

        return $pdf->download($filename);
    }

    // ─────────────────────────────────────────────────────────────────────
    // APERÇU FISCAL AJAX
    // ─────────────────────────────────────────────────────────────────────

    public function fiscalPreview(Contrat $contrat)
    {
        $this->authorize('isStaff');

        $contrat->loadMissing(['bien', 'locataire.locataire']);

        $ctx    = FiscalContext::fromContrat($contrat);
        $result = FiscalService::calculer($ctx);

        return response()->json([
            'loyer_nu'          => $result->loyerHt,
            'tva_loyer'         => $result->tvaLoyer,
            'loyer_ttc'         => $result->loyerTtc,
            'charges'           => $result->chargesAmount,
            'tom'               => $result->tomAmount,
            'montant_encaisse'  => $result->montantEncaisse,
            'taux_comm'         => $ctx->tauxCommission,
            'comm_ht'           => $result->commissionHt,
            'tva_comm'          => $result->tvaCommission,
            'comm_ttc'          => $result->commissionTtc,
            'net_proprietaire'  => $result->netProprietaire,
            'brs_amount'        => $result->brsAmount,
            'net_a_verser'      => $result->netAVerserProprietaire,
            'regime_fiscal'     => $result->regimeFiscal,
            'loyer_assujetti'   => $result->loyerAssujetti,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // DERNIÈRE PÉRIODE AJAX
    // ─────────────────────────────────────────────────────────────────────

    public function dernierePeriode(Contrat $contrat)
    {
        $this->authorize('isStaff');

        $dernier = Paiement::where('contrat_id', $contrat->id)
            ->where('statut', 'valide')
            ->orderByDesc('periode')
            ->value('periode');

        $prochaine = $dernier
            ? Carbon::parse($dernier)->addMonth()->startOfMonth()
            : Carbon::parse($contrat->date_debut)->startOfMonth();

        return response()->json([
            'periode'    => $prochaine->format('Y-m-d'),
            'label'      => $prochaine->translatedFormat('F Y'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // MES PAIEMENTS (LOCATAIRE)
    // ─────────────────────────────────────────────────────────────────────

    public function mesPaiements(): View
    {
        $this->authorize('isLocataire');

        $user = Auth::user();

        $paiements = Paiement::whereHas('contrat', fn($q) => $q->where('locataire_id', $user->id))
            ->where('statut', 'valide')
            ->select([
                'id', 'contrat_id', 'periode', 'date_paiement',
                'montant_encaisse', 'mode_paiement', 'reference_paiement',
            ])
            ->orderByDesc('periode')
            ->paginate(12);

        return view('locataire.paiements', compact('paiements'));
    }
}