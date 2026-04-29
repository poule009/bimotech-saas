<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Paiement;
use App\Notifications\PaiementProprietaireNotification;
use App\Services\FiscalContext;
use App\Services\FiscalService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StorePaiementRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
                'contrat.bien:id,reference,adresse,ville,proprietaire_id',
                'contrat.bien.proprietaire:id,name',
                'contrat.locataire:id,name',
            ])
            ->select([
                'id', 'agency_id', 'contrat_id', 'periode', 'date_paiement',
                'montant_encaisse', 'net_proprietaire', 'net_a_verser_proprietaire',
                'commission_ttc', 'mode_paiement', 'statut', 'reference_paiement',
            ]);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('contrat_id')) {
            $query->where('contrat_id', $request->contrat_id);
        }
        if ($request->filled('mois')) {
            // Utiliser whereYear/whereMonth plutôt que whereRaw pour éviter
            // tout risque d'injection si la liaison est un jour omise.
            [$annee, $mois] = explode('-', $request->mois) + [null, null];
            if ($annee && $mois) {
                $query->whereYear('periode', $annee)->whereMonth('periode', $mois);
            }
        }

        $paiements = $query->orderByDesc('date_paiement')->paginate(20)->withQueryString();

        // Stats mois courant
        $statsRaw = Paiement::where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereYear('periode', now()->year)
            ->whereMonth('periode', now()->month)
            ->selectRaw('
                COALESCE(SUM(montant_encaisse), 0)          AS total_loyers,
                COALESCE(SUM(commission_ttc), 0)            AS total_commissions,
                COALESCE(SUM(net_a_verser_proprietaire), 0) AS total_net,
                COUNT(*)                                     AS nb_payes
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
                'locataire.locataire:id,user_id,est_entreprise,taux_brs_override',
            ])
            ->select(['id', 'bien_id', 'locataire_id', 'loyer_nu',
                      'charges_mensuelles', 'tom_amount', 'loyer_contractuel',
                      'reference_bail', 'date_debut', 'type_bail',
                      'brs_applicable', 'taux_brs_manuel',
                      'loyer_assujetti_tva', 'taux_tva_loyer'])
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

    public function store(StorePaiementRequest $request): RedirectResponse
    {
        $agencyId = Auth::user()->agency_id;

        // StorePaiementRequest gère authorize() + rules() + messages()
        $validated = $request->validated();

        // Vérifier appartenance du contrat + charger les relations nécessaires au calcul fiscal
        $contrat = Contrat::with([
                'bien',
                'locataire.locataire',
            ])
            ->where('agency_id', $agencyId)
            ->findOrFail($validated['contrat_id']);

        // Vérifier si c'est le premier paiement du contrat
        // (anti-doublon déjà géré par StorePaiementRequest::rules())
        $estPremier = Paiement::where('contrat_id', $contrat->id)->count() === 0;

        // ── Prorata temporel (premier paiement en cours de mois) ────────────
        // Si l'entrée n'est pas le 1er du mois, on proratise loyer + charges + TOM.
        $dateDebutOccupation = null;
        $dateFinPeriode      = null;

        if ($estPremier && $contrat->date_debut) {
            $dateDebut   = Carbon::parse($contrat->date_debut);
            $periodeDebut = Carbon::parse($validated['periode'])->startOfMonth();

            // Prorata uniquement si l'entrée tombe dans le même mois/année que la période
            // ET que le locataire n'entre pas le 1er (sinon mois complet).
            if (
                $dateDebut->year  === $periodeDebut->year  &&
                $dateDebut->month === $periodeDebut->month &&
                $dateDebut->day   > 1
            ) {
                $dateDebutOccupation = $dateDebut;
                $dateFinPeriode      = $periodeDebut->copy()->endOfMonth()->startOfDay();
            }
        }

        // Calcul fiscal via FiscalService (TVA loyer, BRS, commission, nets, prorata, frais)
        // $estPremier active la lecture de frais_agence + caution depuis le contrat
        $ctx    = FiscalContext::fromContrat($contrat, $dateDebutOccupation, $dateFinPeriode, $estPremier);
        $result = FiscalService::calculer($ctx);

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

        // Invalider le cache du dashboard locataire pour qu'il voie le nouveau paiement immédiatement
        Cache::forget("locataire_dashboard_{$contrat->locataire_id}");

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

        // Invalider le cache locataire — il verrait encore le paiement comme "validé"
        Cache::forget("locataire_dashboard_{$paiement->contrat->locataire_id}");

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
            'contrat.bien.agency',
            'contrat.locataire:id,name,email,telephone,adresse',
            'contrat.locataire.locataire',
        ]);

        $contrat      = $paiement->contrat;
        $bien         = $contrat?->bien;
        $locataire    = $contrat?->locataire;
        $proprietaire = $bien?->proprietaire;
        $agenceModel  = Auth::user()->agency ?? $bien?->agency;
        $referenceBail = $paiement->reference_bail
            ?? $contrat?->reference_bail
            ?? ('BAIL-' . ($contrat?->id ?? ''));

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $destinataire = match(true) {
            $user->isLocataire()    => 'locataire',
            $user->isProprietaire() => 'proprietaire',
            default                 => 'agence',
        };

        // Locataire → quittance simple (pas de détail fiscal)
        if ($destinataire === 'locataire') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('paiements.pdf.quittance', [
                'paiement'     => $paiement,
                'agence'       => $agenceModel,
                'destinataire' => $destinataire,
            ]);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('quittance-' . $paiement->reference_paiement . '.pdf');
        }

        // Admin / propriétaire → quittance fiscale détaillée
        $snapshot = $paiement->regime_fiscal_snapshot;
        $snapshotData = $snapshot
            ? (is_array($snapshot) ? $snapshot : json_decode((string) $snapshot, true))
            : [];
        $regimeFiscalKey = $snapshotData['regime_fiscal']
            ?? (($paiement->tva_loyer > 0)
                ? (($paiement->brs_amount > 0) ? 'commercial_avec_brs' : 'commercial')
                : (($paiement->brs_amount > 0) ? 'habitation_avec_brs' : 'habitation'));

        $regimeFiscalLabels = [
            'habitation'          => 'Habitation — Exonéré TVA (Art. 355 CGI SN)',
            'habitation_avec_brs' => 'Habitation — BRS applicable (Art. 196bis CGI SN)',
            'commercial'          => 'Commercial — TVA 18% (Art. 355 CGI SN)',
            'commercial_avec_brs' => 'Commercial — TVA 18% + BRS (Art. 355 & 196bis CGI SN)',
        ];
        $regime_fiscal    = $regimeFiscalLabels[$regimeFiscalKey] ?? ucfirst($regimeFiscalKey);
        $loyer_assujetti  = (bool) ($paiement->tva_loyer > 0);
        $brs_applicable   = (bool) ($paiement->brs_amount > 0);
        $taux_brs_applique = (float) ($paiement->taux_brs_applique ?? 0);

        $montantEnLettres    = FiscalService::montantEnLettresFr((float) $paiement->montant_encaisse);
        $loyerNuEnLettres    = FiscalService::montantEnLettresFr((float) ($paiement->loyer_ht ?? $paiement->loyer_nu ?? 0));
        $netAVerserEnLettres = FiscalService::montantEnLettresFr((float) ($paiement->net_a_verser_proprietaire ?? $paiement->net_proprietaire ?? 0));
        $netEnLettres        = FiscalService::montantEnLettresFr((float) ($paiement->net_proprietaire ?? 0));

        $agence = $agenceModel ? [
            'nom'       => $agenceModel->name ?? '',
            'adresse'   => $agenceModel->adresse ?? '',
            'telephone' => $agenceModel->telephone ?? '',
            'email'     => $agenceModel->email ?? '',
            'ninea'     => $agenceModel->ninea ?? '',
            'rccm'      => '',
            'logo_path' => $agenceModel->logo_path ?? '',
        ] : ['nom' => config('app.name'), 'adresse' => '', 'telephone' => '', 'email' => '', 'ninea' => '', 'rccm' => '', 'logo_path' => ''];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('paiements.pdf.quittance-fiscale', compact(
            'paiement', 'agence', 'contrat', 'bien', 'locataire', 'proprietaire', 'referenceBail',
            'regime_fiscal', 'loyer_assujetti', 'brs_applicable', 'taux_brs_applique',
            'montantEnLettres', 'loyerNuEnLettres', 'netAVerserEnLettres', 'netEnLettres'
        ));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->download('quittance-fiscale-' . $paiement->reference_paiement . '.pdf');
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
            'loyer_nu'              => $result->loyerHt,
            'tva_loyer'             => $result->tvaLoyer,
            'loyer_ttc'             => $result->loyerTtc,
            'charges'               => $result->chargesAmount,
            'tom'                   => $result->tomAmount,
            'montant_encaisse'      => $result->montantEncaisse,
            'taux_comm'             => $ctx->tauxCommission,
            'comm_ht'               => $result->commissionHt,
            'tva_comm'              => $result->tvaCommission,
            'comm_ttc'              => $result->commissionTtc,
            'net_proprietaire'      => $result->netProprietaire,
            'brs_amount'            => $result->brsAmount,
            'net_a_verser'          => $result->netAVerserProprietaire,
            'montant_net_locataire' => $result->netLocataire,
            'montant_net_bailleur'  => $result->netBailleur,
            'regime_fiscal'         => $result->regimeFiscal,
            'loyer_assujetti'       => $result->loyerAssujetti,
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
    // EXPORT CSV PAIEMENTS
    // ─────────────────────────────────────────────────────────────────────

    public function exportCsv(\Illuminate\Http\Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('isStaff');

        $agencyId = Auth::user()->agency_id;

        $query = Paiement::where('agency_id', $agencyId)
            ->with([
                'contrat:id,bien_id,locataire_id,reference_bail',
                'contrat.bien:id,reference,adresse',
                'contrat.bien.proprietaire:id,name',
                'contrat.locataire:id,name',
            ])
            ->select([
                'id', 'contrat_id', 'reference_paiement', 'periode',
                'date_paiement', 'montant_encaisse', 'commission_agence',
                'tva_commission', 'commission_ttc', 'brs_amount',
                'net_proprietaire', 'net_a_verser_proprietaire',
                'mode_paiement', 'statut', 'reference_bail',
            ])
            ->orderByDesc('date_paiement');

        // Appliquer les mêmes filtres que la vue index
        if ($request->filled('statut'))     $query->where('statut', $request->statut);
        if ($request->filled('mois')) {
            [$annee, $mois] = explode('-', $request->mois) + [null, null];
            if ($annee && $mois) $query->whereYear('periode', $annee)->whereMonth('periode', $mois);
        }
        if ($request->filled('annee'))      $query->whereYear('periode', $request->annee);
        if ($request->filled('contrat_id')) $query->where('contrat_id', $request->contrat_id);

        $paiements = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="paiements-' . now()->format('Y-m-d') . '.csv"',
        ];

        $colonnes = [
            'Référence', 'Période', 'Date paiement', 'Bien', 'Adresse',
            'Référence bail', 'Locataire', 'Propriétaire',
            'Loyer encaissé (F)', 'Commission HT (F)', 'TVA comm. (F)',
            'Commission TTC (F)', 'BRS (F)', 'Net propriétaire (F)',
            'Net à verser (F)', 'Mode paiement', 'Statut',
        ];

        $callback = function () use ($paiements, $colonnes) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $colonnes, ';');

            foreach ($paiements as $p) {
                fputcsv($handle, [
                    $p->reference_paiement,
                    $p->periode ? \Carbon\Carbon::parse($p->periode)->format('m/Y') : '',
                    $p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '',
                    $p->contrat?->bien?->reference ?? '',
                    $p->contrat?->bien?->adresse ?? '',
                    $p->reference_bail ?? $p->contrat?->reference_bail ?? '',
                    $p->contrat?->locataire?->name ?? '',
                    $p->contrat?->bien?->proprietaire?->name ?? '',
                    number_format($p->montant_encaisse, 0, ',', ' '),
                    number_format($p->commission_agence ?? 0, 0, ',', ' '),
                    number_format($p->tva_commission ?? 0, 0, ',', ' '),
                    number_format($p->commission_ttc ?? 0, 0, ',', ' '),
                    number_format($p->brs_amount ?? 0, 0, ',', ' '),
                    number_format($p->net_proprietaire ?? 0, 0, ',', ' '),
                    number_format($p->net_a_verser_proprietaire ?? 0, 0, ',', ' '),
                    $p->mode_paiement ?? '',
                    $p->statut ?? '',
                ], ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
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