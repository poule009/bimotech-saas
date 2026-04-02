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

    // ─────────────────────────────────────────────────────────────────────
    // DASHBOARD ADMIN
    // ─────────────────────────────────────────────────────────────────────

    public function admin()
    {
        $this->authorize('isAdmin');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        $agencyId = $user->agency_id;

        // ── Stats cumulatives all-time ────────────────────────────────────
        // AgencyScope appliqué automatiquement sur Bien, Contrat, Paiement
        $stats = [
            'total_loyers'         => Paiement::where('statut', 'valide')->sum('montant_encaisse'),
            'total_commissions'    => Paiement::where('statut', 'valide')->sum('commission_agence'),
            'total_tva'            => Paiement::where('statut', 'valide')->sum('tva_commission'),
            'total_commission_ttc' => Paiement::where('statut', 'valide')->sum('commission_ttc'),
            'total_net_proprio'    => Paiement::where('statut', 'valide')->sum('net_proprietaire'),
            'nb_biens'             => Bien::count(),
            'nb_biens_loues'       => Bien::where('statut', 'loue')->count(),
            'nb_contrats'          => Contrat::where('statut', 'actif')->count(),
            // User sans Global Scope — filtrage manuel
            'nb_proprietaires'     => User::where('agency_id', $agencyId)->where('role', 'proprietaire')->count(),
            'nb_locataires'        => User::where('agency_id', $agencyId)->where('role', 'locataire')->count(),
        ];

        $stats['taux_occupation'] = $stats['nb_biens'] > 0
            ? round(($stats['nb_biens_loues'] / $stats['nb_biens']) * 100, 1)
            : 0;

        // ── Stats mois courant ────────────────────────────────────────────
        // Clés alignées avec admin/dashboard.blade.php :
        // loyers, commissions, net_proprio, nb_payes
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

        // ── Impayés du mois ───────────────────────────────────────────────
        $nb_impayes_mois = Contrat::where('statut', 'actif')
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

        // ── Urgences impayés (5 plus anciens) ────────────────────────────
        $impayes_urgents = Contrat::where('statut', 'actif')
            ->whereDoesntHave('paiements', function ($q) {
                $q->whereYear('periode', now()->year)
                  ->whereMonth('periode', now()->month)
                  ->where('statut', '!=', 'annule');
            })
            ->select(['id', 'agency_id', 'bien_id', 'locataire_id', 'loyer_contractuel', 'statut'])
            ->with([
                'bien:id,reference,adresse,ville',
                'locataire:id,name',
            ])
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        // ── Contrats expirant dans 30 jours ──────────────────────────────
        $contrats_a_renouveler = Contrat::where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->where('date_fin', '>=', now()->toDateString())
            ->where('date_fin', '<=', now()->addDays(30)->toDateString())
            ->select(['id', 'agency_id', 'bien_id', 'locataire_id', 'date_fin', 'loyer_contractuel'])
            ->with([
                'bien:id,reference,adresse,ville',
                'locataire:id,name',
            ])
            ->orderBy('date_fin')
            ->get();

        // ── Nombre de contrats actifs (pour le bilan du mois) ────────────
        $contratsActifs = Contrat::where('statut', 'actif')->count();

        // ── Derniers paiements ────────────────────────────────────────────
        $derniersPaiements = Paiement::select([
                'id', 'agency_id', 'contrat_id',
                'periode', 'montant_encaisse', 'commission_ttc', 'net_proprietaire',
                'mode_paiement', 'date_paiement', 'statut', 'reference_paiement',
            ])
            ->with([
                'contrat:id,bien_id,locataire_id',
                'contrat.bien:id,reference,adresse,ville',
                'contrat.locataire:id,name',
            ])
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

        // ── Bilan du mois express ─────────────────────────────────────────
        $bilanMois = [
            'attendu'      => Contrat::where('statut', 'actif')->sum('loyer_contractuel'),
            'encaisse'     => $statsMois['loyers'],
            'a_recouvrer'  => $montant_du_mois,
        ];

        // ── Onboarding ────────────────────────────────────────────────────
        $agency     = $user->agency;
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

    // ─────────────────────────────────────────────────────────────────────
    // DASHBOARD PROPRIÉTAIRE
    // ─────────────────────────────────────────────────────────────────────

    public function proprietaire()
    {
        $this->authorize('isProprietaire');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $biens = Bien::where('proprietaire_id', $user->id)
            ->select(['id', 'agency_id', 'proprietaire_id', 'reference', 'type', 'adresse', 'ville', 'statut', 'loyer_mensuel'])
            ->withCount('contrats')
            ->paginate(6);

        $contratIds = Contrat::whereHas(
            'bien', fn($q) => $q->where('proprietaire_id', $user->id)
        )->pluck('id');

        // Agrégats SQL — pas de get() en mémoire
        $stats = [
            'nb_biens'         => $biens->total(),
            'nb_biens_loues'   => Bien::where('proprietaire_id', $user->id)->where('statut', 'loue')->count(),
            'total_loyers'     => Paiement::whereIn('contrat_id', $contratIds)->where('statut', 'valide')->sum('montant_encaisse'),
            'total_net'        => Paiement::whereIn('contrat_id', $contratIds)->where('statut', 'valide')->sum('net_proprietaire'),
            'total_commission' => Paiement::whereIn('contrat_id', $contratIds)->where('statut', 'valide')->sum('commission_ttc'),
            'nb_paiements'     => Paiement::whereIn('contrat_id', $contratIds)->where('statut', 'valide')->count(),
            'dernier_paiement' => Paiement::whereIn('contrat_id', $contratIds)
                                    ->where('statut', 'valide')
                                    ->orderByDesc('date_paiement')
                                    ->first(),
            'caution'          => Contrat::whereIn('id', $contratIds)->sum('caution'),
        ];

        $paiements = Paiement::whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->select(['id', 'agency_id', 'contrat_id', 'periode', 'montant_encaisse', 'net_proprietaire', 'mode_paiement', 'date_paiement', 'reference_paiement'])
            ->with([
                'contrat:id,bien_id,locataire_id',
                'contrat.bien:id,reference',
                'contrat.locataire:id,name',
            ])
            ->orderByDesc('date_paiement')
            ->paginate(5);

        return view('proprietaire.dashboard', compact('biens', 'stats', 'paiements'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // DASHBOARD LOCATAIRE
    // ─────────────────────────────────────────────────────────────────────

    public function locataire()
    {
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

        // Agrégats SQL
        $stats = [
            'total_paye'   => $contrat
                ? Paiement::where('contrat_id', $contrat->id)->where('statut', 'valide')->sum('montant_encaisse')
                : 0,
            'nb_paiements' => $contrat
                ? Paiement::where('contrat_id', $contrat->id)->where('statut', 'valide')->count()
                : 0,
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