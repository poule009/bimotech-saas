<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    // ── Dashboard Admin ───────────────────────────────────────────────────
    public function admin()
    {
        // BUG 11 FIX : authorize() manquant — défense en profondeur.
        // isAdmin gate autorise admin + superadmin (le superadmin est ensuite redirigé).
        $this->authorize('isAdmin');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Le superadmin n'appartient à aucune agence.
        // S'il accède manuellement à /admin/dashboard, on le redirige vers son propre dashboard.
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        $agencyId = $user->agency_id;

        // ── Stats cumulatives (all-time) ──────────────────────────────────
        // Tous ces counts sont filtrés par agency_id grâce au Global Scope
        // SAUF User qui n'a pas de Global Scope — on filtre manuellement
        $stats = [
            'total_loyers'         => Paiement::where('statut', 'valide')
                                        ->sum('montant_encaisse'),
            'total_commissions'    => Paiement::where('statut', 'valide')
                                        ->sum('commission_agence'),
            'total_tva'            => Paiement::where('statut', 'valide')
                                        ->sum('tva_commission'),
            'total_commission_ttc' => Paiement::where('statut', 'valide')
                                        ->sum('commission_ttc'),
            'total_net_proprio'    => Paiement::where('statut', 'valide')
                                        ->sum('net_proprietaire'),
            'nb_biens'             => Bien::count(),
            'nb_biens_loues'       => Bien::where('statut', 'loue')->count(),
            'nb_contrats'          => Contrat::where('statut', 'actif')->count(),

            // Filtre manuel sur users car pas de Global Scope sur User
            'nb_proprietaires'     => User::withoutGlobalScopes()
                                        ->where('agency_id', $agencyId)
                                        ->where('role', 'proprietaire')
                                        ->count(),
            'nb_locataires'        => User::withoutGlobalScopes()
                                        ->where('agency_id', $agencyId)
                                        ->where('role', 'locataire')
                                        ->count(),
        ];

        $stats['taux_occupation'] = $stats['nb_biens'] > 0
            ? round(($stats['nb_biens_loues'] / $stats['nb_biens']) * 100, 1)
            : 0;

        // ── Stats mois courant ────────────────────────────────────────────
        $statsMois = [
            'loyers'      => Paiement::where('statut', 'valide')
                                ->whereYear('periode', now()->year)
                                ->whereMonth('periode', now()->month)
                                ->sum('montant_encaisse'),
            'commissions' => Paiement::where('statut', 'valide')
                                ->whereYear('periode', now()->year)
                                ->whereMonth('periode', now()->month)
                                ->sum('commission_ttc'),
            'net_proprio' => Paiement::where('statut', 'valide')
                                ->whereYear('periode', now()->year)
                                ->whereMonth('periode', now()->month)
                                ->sum('net_proprietaire'),
            'nb_payes'    => Paiement::where('statut', 'valide')
                                ->whereYear('periode', now()->year)
                                ->whereMonth('periode', now()->month)
                                ->count(),
        ];

        // ── Urgences : impayés du mois courant ───────────────────────────
        $contratsActifs = Contrat::where('statut', 'actif')->count();

        $impayes_urgents = Contrat::where('statut', 'actif')
            ->whereDoesntHave('paiements', function ($q) {
                $q->whereYear('periode', now()->year)
                  ->whereMonth('periode', now()->month)
                  ->where('statut', '!=', 'annule');
            })
            ->with('bien', 'locataire')
            ->orderBy('created_at')
            ->limit(5)
            ->get()
            ->map(function ($contrat) {
                $joursRetard = now()->copy()->startOfMonth()->addDays(4)
                    ->diffInDays(now(), false);
                return [
                    'contrat'      => $contrat,
                    'jours_retard' => max(0, (int) $joursRetard),
                    'montant_du'   => $contrat->loyer_contractuel,
                ];
            });

        $nb_impayes_mois   = Contrat::where('statut', 'actif')
            ->whereDoesntHave('paiements', function ($q) {
                $q->whereYear('periode', now()->year)
                  ->whereMonth('periode', now()->month)
                  ->where('statut', '!=', 'annule');
            })
            ->count();

        $montant_du_mois = Contrat::where('statut', 'actif')
            ->whereDoesntHave('paiements', function ($q) {
                $q->whereYear('periode', now()->year)
                  ->whereMonth('periode', now()->month)
                  ->where('statut', '!=', 'annule');
            })
            ->sum('loyer_contractuel');

        // ── Urgences : contrats expirant dans 30 jours ───────────────────
        $contrats_a_renouveler = Contrat::where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->where('date_fin', '>=', now()->toDateString())
            ->where('date_fin', '<=', now()->addDays(30)->toDateString())
            ->with('bien', 'locataire')
            ->orderBy('date_fin')
            ->get();

        // ── Derniers paiements ────────────────────────────────────────────
        $derniersPaiements = Paiement::with('contrat.bien', 'contrat.locataire')
            ->where('statut', 'valide')
            ->orderByDesc('date_paiement')
            ->limit(6)
            ->get();

        // ── Graphique 6 mois ──────────────────────────────────────────────
        $loyersParMois = Paiement::where('statut', 'valide')
            ->where('periode', '>=', now()->subMonths(6)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(periode, '%Y-%m') as mois"),
                DB::raw('SUM(montant_encaisse) as total'),
                DB::raw('SUM(commission_ttc) as commission')
            )
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // ── TÂCHE 4 : Bilan du mois — Total attendu ───────────────────────
        // Total attendu = somme des loyers contractuels de tous les contrats actifs
        // (ce que l'agence devrait encaisser ce mois-ci si tout le monde payait)
        $total_attendu_mois = Contrat::where('statut', 'actif')
            ->sum('loyer_contractuel');

        // Bilan express : 3 chiffres clés pour la TÂCHE 4
        $bilanMois = [
            'attendu'   => $total_attendu_mois,
            'encaisse'  => $statsMois['loyers'],
            'a_recouvrer' => $montant_du_mois,
        ];

        // ── TÂCHE 1 : Onboarding — calcul des étapes ─────────────────────
        // On ne calcule que si l'onboarding n'est pas encore marqué terminé
        $agency = $user->agency;
        $onboarding = null;

        if ($agency && ! $agency->onboarding_completed) {
            $onboarding = $agency->checkOnboarding();
        }

        return view('admin.dashboard', compact(
            'stats',
            'statsMois',
            'nb_impayes_mois',
            'montant_du_mois',
            'impayes_urgents',
            'contrats_a_renouveler',
            'contratsActifs',
            'derniersPaiements',
            'loyersParMois',
            'bilanMois',
            'onboarding'
        ));
    }

    // ── Dashboard Propriétaire ────────────────────────────────────────────
    public function proprietaire()
    {
        // BUG 11 FIX : authorize() manquant — défense en profondeur.
        $this->authorize('isProprietaire');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $biens = Bien::where('proprietaire_id', $user->id)
            ->withCount('contrats')
            ->paginate(6);

        $contratIds = Contrat::whereHas(
            'bien',
            fn($q) => $q->where('proprietaire_id', $user->id)
        )->pluck('id');

        // Tous les paiements validés pour les stats globales
        $tousLesPaiements = Paiement::whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->get();

        // Dernier paiement reçu
        $dernierPaiement = Paiement::whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->orderByDesc('date_paiement')
            ->first();

        // Caution totale des contrats du propriétaire
        $cautionTotale = Contrat::whereIn('id', $contratIds)->sum('caution');

        $stats = [
            'nb_biens'         => $biens->total(),
            'nb_biens_loues'   => Bien::where('proprietaire_id', $user->id)
                                    ->where('statut', 'loue')->count(),
            'total_loyers'     => $tousLesPaiements->sum('montant_encaisse'),
            'total_net'        => $tousLesPaiements->sum('net_proprietaire'),
            'total_commission' => $tousLesPaiements->sum('commission_ttc'),
            'nb_paiements'     => $tousLesPaiements->count(),
            'dernier_paiement' => $dernierPaiement,
            'caution'          => $cautionTotale,
        ];

        // Paiements paginés pour l'affichage des quittances
        $paiements = Paiement::whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->with('contrat.bien', 'contrat.locataire')
            ->orderByDesc('date_paiement')
            ->paginate(5);

        // Un propriétaire n'a pas de contrat actif en tant que locataire
        $contratActif    = null;
        $prochainePeriode = null;

        return view('proprietaire.dashboard', compact(
            'biens',
            'stats',
            'paiements',
            'contratActif',
            'prochainePeriode'
        ));
    }

    // ── Dashboard Locataire ───────────────────────────────────────────────
    public function locataire()
    {
        // BUG 11 FIX : authorize() manquant — défense en profondeur.
        $this->authorize('isLocataire');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $contrat = Contrat::with('bien.proprietaire')
            ->where('locataire_id', $user->id)
            ->where('statut', 'actif')
            ->first();

        $paiements       = collect();
        $dernierPaiement = null;

        if ($contrat) {
            $paiements = Paiement::where('contrat_id', $contrat->id)
                ->where('statut', 'valide')
                ->orderByDesc('periode')
                ->get();

            $dernierPaiement = $paiements->first();
        }

        $prochainePeriode = $dernierPaiement
            ? \Carbon\Carbon::parse($dernierPaiement->periode)->addMonth()
            : ($contrat ? \Carbon\Carbon::parse($contrat->date_debut) : null);

        $stats = [
            'total_paye'    => $paiements->sum('montant_encaisse'),
            'nb_paiements'  => $paiements->count(),
            'mois_restants' => $contrat?->date_fin
                ? now()->diffInMonths($contrat->date_fin)
                : null,
        ];

        return view('locataire.dashboard', compact(
            'contrat',
            'paiements',
            'dernierPaiement',
            'prochainePeriode',
            'stats'
        ));
    }
}