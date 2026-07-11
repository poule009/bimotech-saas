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
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaiementController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:fiscalite', only: ['fiscalPreview']),
            new Middleware('check.feature:export_csv', only: ['exportCsv']),
        ];
    }

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

    /**
     * Vue « Quittances » : liste de travail groupée par gravité de retard.
     * Même table que la fiche contrat — source unique (voir marquerPaye).
     */
    public function index(Request $request): View
    {
        $this->authorize('isStaff');

        $agencyId = Auth::user()->agency_id;
        $q        = trim((string) $request->input('q'));
        $filter   = in_array($request->input('filter'), ['retard', 'payees'], true) ? $request->input('filter') : null;
        $now      = now();

        $with = [
            'contrat:id,bien_id,locataire_id',
            'contrat.bien:id,reference,titre,adresse,ville',
            'contrat.locataire:id,name,telephone',
        ];
        $cols = ['id', 'agency_id', 'contrat_id', 'periode', 'montant_encaisse', 'statut', 'date_paiement'];

        $applyQ = function ($query) use ($q) {
            if ($q === '') {
                return;
            }
            $query->whereHas('contrat', function ($c) use ($q) {
                $c->whereHas('locataire', fn ($l) => $l->where('name', 'like', "%{$q}%"))
                  ->orWhereHas('bien', fn ($b) => $b->where('reference', 'like', "%{$q}%")
                        ->orWhere('titre', 'like', "%{$q}%")->orWhere('adresse', 'like', "%{$q}%"));
            });
        };

        // ── Impayés échus (statut ni validé ni annulé, période passée) ────
        $iq = Paiement::where('agency_id', $agencyId)
            ->whereNotIn('statut', ['valide', 'annule'])
            ->whereDate('periode', '<=', $now->toDateString())
            ->with($with)->select($cols);
        $applyQ($iq);
        $impayes = $iq->orderBy('periode')->get();

        // ── Bucketing par jours de retard (grâce de 5 j, comme ImpayeController) ──
        $buckets = [
            'crit' => ['titre' => 'Critique — 30 jours et plus', 'items' => collect()],
            'late' => ['titre' => 'Sérieux — 15 à 30 jours',     'items' => collect()],
            'mid'  => ['titre' => 'À surveiller — 4 à 14 jours',  'items' => collect()],
            'soon' => ['titre' => 'Léger — 1 à 3 jours',          'items' => collect()],
        ];
        $enRetardMontant  = 0.0;
        $locatairesRetard = collect();
        $critiques        = 0;

        foreach ($impayes as $p) {
            $jours = (int) \Carbon\Carbon::parse($p->periode)->addDays(5)->diffInDays($now, false);
            if ($jours <= 0) {
                continue; // encore dans le délai de grâce
            }
            $p->jours_retard = $jours;
            $enRetardMontant += (float) $p->montant_encaisse;
            $locatairesRetard->push($p->contrat?->locataire_id);

            if ($jours >= 30)      { $buckets['crit']['items']->push($p); $critiques++; }
            elseif ($jours >= 15)  { $buckets['late']['items']->push($p); }
            elseif ($jours >= 4)   { $buckets['mid']['items']->push($p); }
            else                   { $buckets['soon']['items']->push($p); }
        }

        // ── Payées (mois courant) ─────────────────────────────────────────
        $pq = Paiement::where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereYear('periode', $now->year)->whereMonth('periode', $now->month)
            ->with($with)->select($cols);
        $applyQ($pq);
        $payes = $pq->orderByDesc('date_paiement')->get();

        // ── KPIs (mois courant) ───────────────────────────────────────────
        $moisRows = Paiement::where('agency_id', $agencyId)->where('statut', '!=', 'annule')
            ->whereYear('periode', $now->year)->whereMonth('periode', $now->month)
            ->get(['statut', 'montant_encaisse']);

        $kpis = [
            'attendu'   => (float) $moisRows->sum('montant_encaisse'),
            'encaisse'  => (float) $moisRows->where('statut', 'valide')->sum('montant_encaisse'),
            'nb_payes'  => $moisRows->where('statut', 'valide')->count(),
            'nb_actifs' => Contrat::where('agency_id', $agencyId)->where('statut', 'actif')->count(),
            'en_retard' => $enRetardMontant,
            'nb_retard' => $locatairesRetard->filter()->unique()->count(),
            'critiques' => $critiques,
        ];

        return view('paiements.index', compact('buckets', 'payes', 'kpis', 'q', 'filter'));
    }

    /**
     * Marque une quittance générée comme payée : bascule statut → valide.
     * Source UNIQUE partagée avec la fiche contrat (aucune duplication de logique).
     * La fiscalité est déjà calculée à la génération (rent:generate).
     */
    public function marquerPaye(Paiement $paiement): RedirectResponse
    {
        $this->authorize('isStaff');

        if ($paiement->agency_id !== Auth::user()->agency_id && ! Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        if ($paiement->statut === 'annule') {
            return back()->withErrors(['general' => 'Cette quittance est annulée.']);
        }
        if ($paiement->statut === 'valide') {
            return back()->with('info', 'Cette quittance est déjà payée.');
        }

        $paiement->update([
            'statut'        => 'valide',
            'date_paiement' => now()->toDateString(),
        ]);

        return back()->with('success', 'Quittance marquée payée ✓');
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
                'bien.proprietaire.proprietaire:user_id,assujetti_tva,est_personne_morale_is',
                'agency:id,assujetti_tva',
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

        $paiement = DB::transaction(function () use ($result, $agencyId, $contrat, $validated, $ctx, $estPremier, $reference) {
            $p = Paiement::create(array_merge(
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

            return $p;
        });

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
            'depenses',
        ]);

        return view('paiements.show', compact('paiement'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // ANNULATION
    // ─────────────────────────────────────────────────────────────────────

    public function annuler(Paiement $paiement): RedirectResponse
    {
        $this->authorize('update', $paiement);

        if ($paiement->statut !== 'valide') {
            return redirect()
                ->route('admin.paiements.show', $paiement)
                ->with('error', 'Seul un paiement valide peut être annulé.');
        }

        // Charger le contrat avant l'update pour éviter un accès null sur locataire_id
        $paiement->loadMissing('contrat');

        $paiement->update([
            'statut'     => 'annule',
            'annule_le'  => now(),
            'annule_par' => Auth::id(),
        ]);

        // Supprimer la quittance liée : elle référence un paiement annulé.
        // La suppression permet de régénérer une quittance propre si le paiement
        // est recréé pour la même période (QuittanceService::generer vérifie l'unicité).
        $paiement->quittance?->delete();

        // Invalider le cache locataire
        Cache::forget("locataire_dashboard_{$paiement->contrat->locataire_id}");

        return redirect()
            ->route('admin.paiements.show', $paiement)
            ->with('success', 'Paiement annulé. La quittance associée a été invalidée.');
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

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('paiements.pdf.quittance', compact(
            'paiement', 'contrat', 'bien', 'locataire', 'proprietaire', 'referenceBail', 'destinataire',
        ) + ['agence' => $agenceModel]);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->download('quittance-' . $paiement->reference_paiement . '.pdf');
    }

    // ─────────────────────────────────────────────────────────────────────
    // APERÇU FISCAL AJAX
    // ─────────────────────────────────────────────────────────────────────

    public function fiscalPreview(Contrat $contrat)
    {
        $this->authorize('isStaff');

        $contrat->loadMissing([
            'bien',
            'bien.proprietaire.proprietaire:user_id,assujetti_tva,est_personne_morale_is',
            'agency:id,assujetti_tva',
            'locataire.locataire',
        ]);

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

    public function mesPaiements(Request $request): View
    {
        $this->authorize('isLocataire');

        $user = Auth::user();

        $annee = $request->input('annee') ? (int) $request->input('annee') : null;

        $query = Paiement::whereHas('contrat', fn($q) => $q->where('locataire_id', $user->id))
            ->where('statut', 'valide')
            ->select([
                'id', 'contrat_id', 'periode', 'date_paiement',
                'montant_encaisse', 'mode_paiement', 'reference_paiement',
            ])
            ->orderByDesc('periode');

        if ($annee) {
            $query->whereYear('periode', $annee);
        }

        $paiements = $query->paginate(12)->withQueryString();

        // Années disponibles pour le sélecteur (sans filtre d'année)
        $anneesDisponibles = Paiement::whereHas('contrat', fn($q) => $q->where('locataire_id', $user->id))
            ->where('statut', 'valide')
            ->selectRaw('YEAR(periode) AS annee')
            ->groupBy('annee')
            ->orderByDesc('annee')
            ->pluck('annee');

        return view('locataire.paiements', compact('paiements', 'annee', 'anneesDisponibles'));
    }
}