<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RapportController extends Controller
{
    /**
     * Retourne les données du rapport financier pour la vue et l'export.
     * Toutes les requêtes User sont filtrées par agency_id pour éviter
     * la fuite de données inter-agences.
     */
    private function getData(int $annee, int $mois): array
    {
        $agencyId  = Auth::user()->agency_id;
        $debutMois = Carbon::create($annee, $mois, 1)->startOfMonth();
        $finMois   = Carbon::create($annee, $mois, 1)->endOfMonth();

        // ── Paiements du mois (AgencyScope appliqué automatiquement) ──
        $paiementsMois = Paiement::with('contrat.bien.proprietaire', 'contrat.locataire')
            ->where('statut', 'valide')
            ->whereBetween('date_paiement', [$debutMois, $finMois])
            ->orderBy('date_paiement')
            ->get();

        // ── KPI du mois ───────────────────────────────────────────────
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
            ->filter(fn($p) => $p->contrat?->bien?->proprietaire !== null)
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
        $biensPaies   = $paiementsMois->pluck('contrat.bien_id')->unique();
        $biensImpayés = Contrat::where('statut', 'actif')
            ->whereNotIn('bien_id', $biensPaies)
            ->with('bien', 'locataire')
            ->get();

        // ── Stats générales — FILTRÉES par agency_id ──────────────────
        $nbBiens      = Bien::count();
        $nbBiensLoues = Bien::where('statut', 'loue')->count();
        $statsGenerales = [
            'nb_biens'         => $nbBiens,
            'nb_biens_loues'   => $nbBiensLoues,
            'nb_contrats'      => Contrat::where('statut', 'actif')->count(),
            // CORRECTION : filtrer par agency_id pour éviter fuite inter-agences
            'nb_proprietaires' => User::where('role', 'proprietaire')
                ->where('agency_id', $agencyId)->count(),
            'nb_locataires'    => User::where('role', 'locataire')
                ->where('agency_id', $agencyId)->count(),
            'taux_occupation'  => $nbBiens > 0
                ? round(($nbBiensLoues / $nbBiens) * 100, 1)
                : 0,
        ];

        // ── Années disponibles pour le filtre ─────────────────────────
        $anneesDisponibles = Paiement::selectRaw('YEAR(periode) as annee')
            ->groupBy('annee')
            ->orderByDesc('annee')
            ->pluck('annee');

        return compact(
            'annee', 'mois', 'debutMois',
            'paiementsMois', 'kpiMois',
            'evolution', 'parProprietaire',
            'biensImpayés', 'statsGenerales',
            'anneesDisponibles'
        );
    } // fin getData

    public function financier(Request $request)
    {
        $this->authorize('isAdmin');

        $annee = (int) $request->input('annee', now()->year);
        $mois  = (int) $request->input('mois',  now()->month);

        $data = $this->getData($annee, $mois);

        // Paginer manuellement les paiements pour la vue
        $paiementsMois = new \Illuminate\Pagination\LengthAwarePaginator(
            $data['paiementsMois']->forPage(
                $page = $request->input('page', 1),
                $perPage = 50
            ),
            $data['paiementsMois']->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $data['paiementsMois'] = $paiementsMois;

        return view('rapports.financier', $data);
    }

    /**
     * Export PDF du rapport financier mensuel.
     */
    public function exportPdf(Request $request)
    {
        $this->authorize('isAdmin');

        $annee = (int) $request->input('annee', now()->year);
        $mois  = (int) $request->input('mois',  now()->month);

        $data    = $this->getData($annee, $mois);
        $agency  = Auth::user()->agency;
        $data['agency'] = $agency;

        $pdf = Pdf::loadView('rapports.financier_pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'  => 'DejaVu Sans',
                'isRemoteEnabled' => false,
            ]);

        $nomFichier = 'rapport-financier-' . $annee . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($nomFichier);
    }
}
