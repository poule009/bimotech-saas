<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // ── Dashboard Admin ───────────────────────────────────────────────────
    public function admin()
    {
        /** @var \App\Models\User $user */
        $user      = Auth::user();
        $agencyId  = $user->agency_id;

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

        $derniersPaiements = Paiement::with('contrat.bien', 'contrat.locataire')
            ->where('statut', 'valide')
            ->orderByDesc('date_paiement')
            ->limit(6)
            ->get();

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

        return view('admin.dashboard', compact(
            'stats',
            'derniersPaiements',
            'loyersParMois'
        ));
    }

    // ── Dashboard Propriétaire ────────────────────────────────────────────
    public function proprietaire()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $biens = Bien::where('proprietaire_id', $user->id)
            ->withCount('contrats')
            ->paginate(6);

        $contratIds = Contrat::whereHas(
            'bien',
            fn($q) => $q->where('proprietaire_id', $user->id)
        )->pluck('id');

        $paiements = Paiement::whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->get();

        $stats = [
            'nb_biens'         => $biens->total(),
            'nb_biens_loues'   => Bien::where('proprietaire_id', $user->id)
                                    ->where('statut', 'loue')->count(),
            'total_loyers'     => $paiements->sum('montant_encaisse'),
            'total_net'        => $paiements->sum('net_proprietaire'),
            'total_commission' => $paiements->sum('commission_ttc'),
        ];

        $derniersPaiements = Paiement::whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->with('contrat.bien', 'contrat.locataire')
            ->orderByDesc('date_paiement')
            ->paginate(5);

        return view('proprietaire.dashboard', compact(
            'biens',
            'stats',
            'derniersPaiements'
        ));
    }

    // ── Dashboard Locataire ───────────────────────────────────────────────
    public function locataire()
    {
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