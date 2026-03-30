<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RapportController extends Controller
{
    public function financier(Request $request)
    {
        $this->authorize('isAdmin');

        // ── Période sélectionnée (défaut : mois en cours) ─────────────
        $annee = (int) $request->input('annee', now()->year);
        $mois  = (int) $request->input('mois',  now()->month);

        $debutMois = Carbon::create($annee, $mois, 1)->startOfMonth();
        $finMois   = Carbon::create($annee, $mois, 1)->endOfMonth();

        // ── Paiements du mois ─────────────────────────────────────────
        $paiementsMois = Paiement::with('contrat.bien.proprietaire', 'contrat.locataire')
            ->where('statut', 'valide')
            ->whereBetween('date_paiement', [$debutMois, $finMois])
            ->orderBy('date_paiement')
            ->paginate(50);

        // ── KPI du mois (source unique SQL pour cohérence/performance) ──
        $kpiMois = [
            'total_loyers'      => Paiement::where('statut', 'valide')
                ->whereBetween('date_paiement', [$debutMois, $finMois])
                ->sum('montant_encaisse'),
            'total_commission'  => Paiement::where('statut', 'valide')
                ->whereBetween('date_paiement', [$debutMois, $finMois])
                ->sum('commission_agence'),
            'total_tva'         => Paiement::where('statut', 'valide')
                ->whereBetween('date_paiement', [$debutMois, $finMois])
                ->sum('tva_commission'),
            'total_ttc'         => Paiement::where('statut', 'valide')
                ->whereBetween('date_paiement', [$debutMois, $finMois])
                ->sum('commission_ttc'),
            'total_net_proprio' => Paiement::where('statut', 'valide')
                ->whereBetween('date_paiement', [$debutMois, $finMois])
                ->sum('net_proprietaire'),
            'nb_paiements'      => Paiement::where('statut', 'valide')
                ->whereBetween('date_paiement', [$debutMois, $finMois])
                ->count(),
        ];

        // ── Évolution sur 12 mois ─────────────────────────────────────
        $evolution = Paiement::where('statut', 'valide')
            ->where('periode', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(periode, '%Y-%m') as mois"),
                DB::raw("DATE_FORMAT(periode, '%b %Y') as mois_label"),
                DB::raw('SUM(montant_encaisse) as loyers'),
                DB::raw('SUM(commission_agence) as commissions'),
                DB::raw('SUM(tva_commission) as tva'),
                DB::raw('SUM(commission_ttc) as ttc'),
                DB::raw('SUM(net_proprietaire) as net'),
                DB::raw('COUNT(*) as nb_paiements')
            )
            ->groupBy('mois', 'mois_label')
            ->orderBy('mois')
            ->get();

        // ── Par propriétaire (mois sélectionné) ───────────────────────
        $parProprietaire = $paiementsMois
            ->groupBy(fn($p) => $p->contrat->bien->proprietaire->id)
            ->map(function ($paiements) {
                $proprio = $paiements->first()->contrat->bien->proprietaire;
                return [
                    'proprio'      => $proprio,
                    'nb_paiements' => $paiements->count(),
                    'loyers'       => $paiements->sum('montant_encaisse'),
                    'commission'   => $paiements->sum('commission_ttc'),
                    'net'          => $paiements->sum('net_proprietaire'),
                    'paiements'    => $paiements,
                ];
            })
            ->sortByDesc('loyers');

        // ── Biens sans paiement ce mois ───────────────────────────────
        $biensPaies = $paiementsMois->pluck('contrat.bien_id')->unique();
        $biensImpayés = Contrat::where('statut', 'actif')
            ->whereNotIn('bien_id', $biensPaies)
            ->with('bien', 'locataire')
            ->get();

        // ── Stats générales ───────────────────────────────────────────
        $statsGenerales = [
            'nb_biens'         => Bien::count(),
            'nb_biens_loues'   => Bien::where('statut', 'loue')->count(),
            'nb_contrats'      => Contrat::where('statut', 'actif')->count(),
            'nb_proprietaires' => User::where('role', 'proprietaire')->count(),
            'nb_locataires'    => User::where('role', 'locataire')->count(),
            'taux_occupation'  => Bien::count() > 0
                ? round((Bien::where('statut', 'loue')->count() / Bien::count()) * 100, 1)
                : 0,
        ];

        // ── Années disponibles pour le filtre ─────────────────────────
        $anneesDisponibles = Paiement::selectRaw('YEAR(periode) as annee')
            ->groupBy('annee')
            ->orderByDesc('annee')
            ->pluck('annee');

        return view('rapports.financier', compact(
            'annee', 'mois', 'debutMois',
            'paiementsMois', 'kpiMois',
            'evolution', 'parProprietaire',
            'biensImpayés', 'statsGenerales',
            'anneesDisponibles'
        ));
    }
}