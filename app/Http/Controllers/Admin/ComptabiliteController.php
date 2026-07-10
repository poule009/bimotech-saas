<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\ChargeAgence;
use App\Models\Paiement;
use App\Models\User;
use App\Services\ComptabiliteService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ComptabiliteController extends Controller implements HasMiddleware
{
    public function __construct(private ComptabiliteService $comptabiliteService) {}

    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:comptabilite', only: ['index']),
        ];
    }

    /**
     * Module Comptabilité — page unique à 3 onglets :
     *   Propriétaires (soldes en cours) · Agence (résultat) · Vérification.
     * Réutilise ComptabiliteService : aucun calcul dupliqué ici.
     */
    public function index(Request $request): View
    {
        $agencyId = Auth::user()->agency_id;
        $annee    = (int) $request->get('annee', now()->year);
        $mois     = (int) $request->get('mois', now()->month);
        $periode  = sprintf('%04d-%02d', $annee, $mois);
        $q        = trim((string) $request->get('q'));

        // ── Onglet Propriétaires : solde EN COURS (running, pas mensuel) ──
        $soldes       = $this->comptabiliteService->soldesMandants($agencyId);
        $soldesByProp = $soldes->keyBy('proprietaire_id');

        $loyersMois = Paiement::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereYear('date_paiement', $annee)
            ->whereMonth('date_paiement', $mois)
            ->whereHas('contrat.bien')
            ->with('contrat.bien:id,proprietaire_id')
            ->get()
            ->groupBy(fn($p) => $p->contrat?->bien?->proprietaire_id)
            ->map(fn($g) => (float) $g->sum('montant_encaisse'));

        $nbBiens = Bien::where('agency_id', $agencyId)
            ->whereNotNull('proprietaire_id')
            ->selectRaw('proprietaire_id, COUNT(*) as n')
            ->groupBy('proprietaire_id')
            ->pluck('n', 'proprietaire_id');

        $proprioIds = $soldesByProp->keys()
            ->merge($loyersMois->keys())
            ->filter()
            ->unique();

        $proprietaires = User::where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->whereIn('id', $proprioIds)
            ->when($q !== '', fn ($query) => $query->where('name', 'like', '%' . $q . '%'))
            ->orderBy('name')
            ->get();

        $lignesProprietaires = $proprietaires->map(fn(User $u) => [
            'proprietaire' => $u,
            'nb_biens'     => (int) ($nbBiens[$u->id] ?? 0),
            'loyers_mois'  => (float) ($loyersMois[$u->id] ?? 0),
            'solde'        => (float) ($soldesByProp[$u->id]['solde'] ?? 0),
        ])->sortByDesc('solde')->values();

        // ── Onglet Agence : résultat du mois ──
        $resultat = $this->comptabiliteService->compteResultat($agencyId, $annee, $mois);

        $chargesFixes = ChargeAgence::where('agency_id', $agencyId)
            ->where('periode', $periode)
            ->where('recurrente', true)
            ->orderByDesc('date_charge')
            ->get();

        $chargesOccasionnelles = ChargeAgence::where('agency_id', $agencyId)
            ->where('periode', $periode)
            ->where('recurrente', false)
            ->orderByDesc('date_charge')
            ->get();

        $revenuAgence   = (float) $resultat['commissions_ttc'];
        $depensesAgence = (float) ($chargesFixes->sum('montant') + $chargesOccasionnelles->sum('montant'));
        $beneficeNet    = $revenuAgence - $depensesAgence;

        // Y a-t-il des charges fixes reportables (modèles absents du mois courant) ?
        $moisCourant       = now()->format('Y-m');
        $fixesDejaCeMois   = ChargeAgence::where('agency_id', $agencyId)
            ->where('periode', $moisCourant)->where('recurrente', true)->pluck('libelle')->all();
        $modelesReportables = ChargeAgence::where('agency_id', $agencyId)
            ->where('recurrente', true)->where('periode', '<', $moisCourant)
            ->pluck('libelle')->unique()->diff($fixesDejaCeMois)->count();

        // ── Onglet Vérification : argent des tiers détenu ──
        $soldeTheorique = (float) $soldes->sum('solde');

        $categoriesAgence = ChargeAgence::CATEGORIES;

        return view('admin.comptabilite.index', compact(
            'annee', 'mois', 'periode', 'q',
            'lignesProprietaires',
            'resultat', 'revenuAgence', 'depensesAgence', 'beneficeNet',
            'chargesFixes', 'chargesOccasionnelles', 'modelesReportables',
            'soldeTheorique',
            'categoriesAgence'
        ));
    }
}
