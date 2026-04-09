<?php

namespace App\Http\Controllers;

// use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $this->authorize('isAdmin');

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
        $this->authorize('isAdmin');

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
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;

        $validated = $request->validate([
            'contrat_id'    => ['required', 'exists:contrats,id'],
            'periode'       => ['required', 'date'],
            'date_paiement' => ['required', 'date'],
            'montant_encaisse' => ['required', 'numeric', 'min:0'],
            'mode_paiement' => ['required', 'in:' . implode(',', array_keys(self::MODES_PAIEMENT))],
            'caution_percue'=> ['nullable', 'numeric', 'min:0'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ], [
            'contrat_id.required'       => 'Veuillez sélectionner un contrat.',
            'periode.required'          => 'La période est obligatoire.',
            'date_paiement.required'    => 'La date de paiement est obligatoire.',
            'montant_encaisse.required' => 'Le montant est obligatoire.',
            'mode_paiement.required'    => 'Le mode de paiement est obligatoire.',
        ]);

        // Vérifier appartenance du contrat
        $contrat = Contrat::with('bien:id,taux_commission,reference')
            ->where('agency_id', $agencyId)
            ->findOrFail($validated['contrat_id']);

        // Calcul fiscal
        $loyerNu       = (float) $contrat->loyer_nu;
        $charges       = (float) ($contrat->charges_mensuelles ?? 0);
        $tom           = (float) ($contrat->tom_amount ?? 0);
        $tauxComm      = (float) ($contrat->bien->taux_commission ?? 10);
        $montant       = (float) $validated['montant_encaisse'];

        $commAgence    = round($loyerNu * $tauxComm / 100, 2);
        $tvaComm       = round($commAgence * 0.18, 2);
        $commTtc       = $commAgence + $tvaComm;
        $netProprio    = $loyerNu - $commAgence;
        $netAVerser    = $loyerNu - $commTtc;

        // Vérifier si c'est le premier paiement du contrat
        $estPremier = Paiement::where('contrat_id', $contrat->id)->count() === 0;

        // Référence paiement unique
        $reference = 'PAY-' . strtoupper(Str::random(8));

        Paiement::create([
            'agency_id'               => $agencyId,
            'contrat_id'              => $contrat->id,
            'periode'                 => Carbon::parse($validated['periode'])->startOfMonth(),
            'date_paiement'           => $validated['date_paiement'],
            'montant_encaisse'        => $montant,
            'loyer_nu'                => $loyerNu,
            'loyer_ht'                => $loyerNu,
            'tva_loyer'               => 0,
            'loyer_ttc'               => $loyerNu,
            'charges_amount'          => $charges,
            'tom_amount'              => $tom,
            'mode_paiement'           => $validated['mode_paiement'],
            'taux_commission_applique'=> $tauxComm,
            'commission_agence'       => $commAgence,
            'tva_commission'          => $tvaComm,
            'commission_ttc'          => $commTtc,
            'net_proprietaire'        => $netProprio,
            'net_a_verser_proprietaire' => $netAVerser,
            'brs_amount'              => 0,
            'taux_brs_applique'       => 0,
            'caution_percue'          => $validated['caution_percue'] ?? 0,
            'est_premier_paiement'    => $estPremier,
            'statut'                  => 'valide',
            'reference_paiement'      => $reference,
            'reference_bail'          => $contrat->reference_bail,
            'notes'                   => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.contrats.show', $contrat)
            ->with('success', 'Paiement enregistré ✓ — ' . number_format($montant, 0, ',', ' ') . ' FCFA');
    }

    // ─────────────────────────────────────────────────────────────────────
    // DÉTAIL
    // ─────────────────────────────────────────────────────────────────────

    public function show(Paiement $paiement): View
    {
        $this->authorize('isAdmin');

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
        $this->authorize('isAdmin');

        if ($paiement->statut !== 'valide') {
            return back()->withErrors(['general' => 'Seul un paiement valide peut être annulé.']);
        }

        $paiement->update(['statut' => 'annulé']);

        return back()->with('success', 'Paiement annulé ✓');
    }

    // ─────────────────────────────────────────────────────────────────────
    // TÉLÉCHARGEMENT PDF QUITTANCE
    // ─────────────────────────────────────────────────────────────────────

    public function downloadPDF(Paiement $paiement)
    {
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
        $this->authorize('isAdmin');

        $contrat->load('bien:id,taux_commission');

        $loyerNu    = (float) $contrat->loyer_nu;
        $tauxComm   = (float) ($contrat->bien->taux_commission ?? 10);
        $commAgence = round($loyerNu * $tauxComm / 100, 2);
        $tvaComm    = round($commAgence * 0.18, 2);
        $commTtc    = $commAgence + $tvaComm;
        $netProprio = $loyerNu - $commTtc;

        return response()->json([
            'loyer_nu'       => $loyerNu,
            'charges'        => (float) ($contrat->charges_mensuelles ?? 0),
            'tom'            => (float) ($contrat->tom_amount ?? 0),
            'loyer_total'    => $loyerNu + ($contrat->charges_mensuelles ?? 0) + ($contrat->tom_amount ?? 0),
            'taux_comm'      => $tauxComm,
            'comm_ht'        => $commAgence,
            'tva_comm'       => $tvaComm,
            'comm_ttc'       => $commTtc,
            'net_proprietaire' => $netProprio,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // DERNIÈRE PÉRIODE AJAX
    // ─────────────────────────────────────────────────────────────────────

    public function dernierePeriode(Contrat $contrat)
    {
        $this->authorize('isAdmin');

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