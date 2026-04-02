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
     * Retourne les données du rapport financier.
     * AgencyScope appliqué automatiquement sur Paiement, Bien, Contrat.
     * User filtré manuellement par agency_id (pas de Global Scope sur User).
     */
    private function getData(int $annee, int $mois): array
    {
        $agencyId  = Auth::user()->agency_id;
        $debutMois = Carbon::create($annee, $mois, 1)->startOfMonth();
        $finMois   = Carbon::create($annee, $mois, 1)->endOfMonth();

        // ── Paiements du mois avec eager load complet ──────────────────
        $paiementsMois = Paiement::with('contrat.bien.proprietaire', 'contrat.locataire')
            ->where('statut', 'valide')
            ->whereBetween('date_paiement', [$debutMois, $finMois])
            ->orderBy('date_paiement')
            ->get();

        // ── KPI du mois — UNE seule requête SQL avec selectRaw ─────────
        // Avant : 5 requêtes séparées (sum x5). Après : 1 requête.
        $kpiRaw = Paiement::where('statut', 'valide')
            ->whereBetween('date_paiement', [$debutMois, $finMois])
            ->selectRaw('
                COALESCE(SUM(montant_encaisse), 0)  AS total_loyers,
                COALESCE(SUM(commission_agence), 0) AS total_commission,
                COALESCE(SUM(tva_commission), 0)    AS total_tva,
                COALESCE(SUM(commission_ttc), 0)    AS total_ttc,
                COALESCE(SUM(net_proprietaire), 0)  AS total_net_proprio,
                COUNT(*)                             AS nb_paiements
            ')
            ->first();

        $kpiMois = [
            'total_loyers'      => (float) ($kpiRaw->total_loyers     ?? 0),
            'total_commission'  => (float) ($kpiRaw->total_commission  ?? 0),
            'total_tva'         => (float) ($kpiRaw->total_tva         ?? 0),
            'total_ttc'         => (float) ($kpiRaw->total_ttc         ?? 0),
            'total_net_proprio' => (float) ($kpiRaw->total_net_proprio ?? 0),
            'nb_paiements'      => (int)   ($kpiRaw->nb_paiements      ?? 0),
        ];

        // ── Évolution sur 12 mois ──────────────────────────────────────
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

        // ── Par propriétaire ───────────────────────────────────────────
        // Les paiements du mois sont déjà chargés avec contrat.bien.proprietaire
        // On group en PHP — pas de requête supplémentaire (N+1 évité)
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

        // ── Biens sans paiement ce mois ────────────────────────────────
        $biensPaies   = $paiementsMois->pluck('contrat.bien_id')->unique();
        $biensImpayés = Contrat::where('statut', 'actif')
            ->whereNotIn('bien_id', $biensPaies)
            ->with('bien', 'locataire')
            ->get();

        // ── Stats générales ────────────────────────────────────────────
        $nbBiens      = Bien::count();
        $nbBiensLoues = Bien::where('statut', 'loue')->count();

        $statsGenerales = [
            'nb_biens'         => $nbBiens,
            'nb_biens_loues'   => $nbBiensLoues,
            'nb_contrats'      => Contrat::where('statut', 'actif')->count(),
            'nb_proprietaires' => User::where('role', 'proprietaire')->where('agency_id', $agencyId)->count(),
            'nb_locataires'    => User::where('role', 'locataire')->where('agency_id', $agencyId)->count(),
            'taux_occupation'  => $nbBiens > 0 ? round(($nbBiensLoues / $nbBiens) * 100, 1) : 0,
        ];

        // ── Années disponibles pour le filtre ──────────────────────────
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
    }

    // ─────────────────────────────────────────────────────────────────────
    // VUE RAPPORT
    // ─────────────────────────────────────────────────────────────────────

    public function financier(Request $request)
    {
        $this->authorize('isAdmin');

        $annee = (int) $request->input('annee', now()->year);
        $mois  = (int) $request->input('mois',  now()->month);

        $data = $this->getData($annee, $mois);

        // Pagination manuelle des paiements pour la vue
        $paiementsMois = new \Illuminate\Pagination\LengthAwarePaginator(
            $data['paiementsMois']->forPage(
                $page    = $request->input('page', 1),
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

    // ─────────────────────────────────────────────────────────────────────
    // EXPORT PDF
    // ─────────────────────────────────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        $this->authorize('isAdmin');

        $annee = (int) $request->input('annee', now()->year);
        $mois  = (int) $request->input('mois',  now()->month);

        $data  = $this->getData($annee, $mois);
        $data['agency'] = Auth::user()->agency;

        $pdf = Pdf::loadView('rapports.financier_pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 120);

        $filename = sprintf(
            'rapport-financier-%04d-%02d-%s.pdf',
            $annee,
            $mois,
            $data['agency']?->slug ?? 'agence'
        );

        return $pdf->download($filename);
    }
}