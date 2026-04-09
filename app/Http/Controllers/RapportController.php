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

class RapportController extends Controller
{
    private function getData(int $annee, int $mois): array
    {
        $agencyId  = Auth::user()->agency_id;
        $debutMois = Carbon::create($annee, $mois, 1)->startOfMonth();
        $finMois   = Carbon::create($annee, $mois, 1)->endOfMonth();

        // ✅ CORRECTION M6 : select() sur chaque relation — on ne charge que ce qui est affiché
        // Avant : with('contrat.bien.proprietaire', 'contrat.locataire') chargeait TOUT
        // Après : uniquement les colonnes nécessaires pour la vue et le PDF
        $paiementsMois = Paiement::with([
            'contrat:id,bien_id,locataire_id,reference_bail',
            'contrat.bien:id,proprietaire_id,reference,adresse,ville,type',
            'contrat.bien.proprietaire:id,name,telephone,adresse',
            'contrat.locataire:id,name,telephone',
        ])
        ->select([
            'id', 'contrat_id', 'agency_id', 'periode',
            'loyer_nu', 'charges_amount', 'tom_amount',
            'montant_encaisse', 'commission_agence', 'tva_commission',
            'commission_ttc', 'net_proprietaire', 'taux_commission_applique',
            'mode_paiement', 'date_paiement', 'reference_paiement', 'reference_bail',
        ])
        ->where('statut', 'valide')
        ->whereBetween('date_paiement', [$debutMois, $finMois])
        ->orderBy('date_paiement')
        ->get();

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
            'total_loyers'      => (float) ($kpiRaw->total_loyers      ?? 0),
            'total_commission'  => (float) ($kpiRaw->total_commission   ?? 0),
            'total_tva'         => (float) ($kpiRaw->total_tva          ?? 0),
            'total_ttc'         => (float) ($kpiRaw->total_ttc          ?? 0),
            'total_net_proprio' => (float) ($kpiRaw->total_net_proprio  ?? 0),
            'nb_paiements'      => (int)   ($kpiRaw->nb_paiements       ?? 0),
        ];

        $evolution = Paiement::where('statut', 'valide')
            ->whereBetween('date_paiement', [
                Carbon::create($annee, $mois, 1)->subMonths(5)->startOfMonth(),
                $finMois,
            ])
            ->selectRaw('
                DATE_FORMAT(date_paiement, "%Y-%m") AS mois_label,
                COALESCE(SUM(montant_encaisse), 0)  AS total_loyers,
                COALESCE(SUM(commission_ttc), 0)    AS total_commission
            ')
            ->groupByRaw('DATE_FORMAT(date_paiement, "%Y-%m")')
            ->orderByRaw('DATE_FORMAT(date_paiement, "%Y-%m")')
            ->get();

        $parProprietaire = Paiement::with([
                'contrat:id,bien_id',
                'contrat.bien:id,proprietaire_id,reference',
                'contrat.bien.proprietaire:id,name',
            ])
            ->select(['id', 'contrat_id', 'montant_encaisse', 'net_proprietaire', 'commission_ttc'])
            ->where('statut', 'valide')
            ->whereBetween('date_paiement', [$debutMois, $finMois])
            ->get()
            ->groupBy(fn($p) => $p->contrat?->bien?->proprietaire?->name ?? 'Inconnu')
            ->map(fn($group) => [
                'nb_paiements'     => $group->count(),
                'total_encaisse'   => $group->sum('montant_encaisse'),
                'total_net'        => $group->sum('net_proprietaire'),
                'total_commission' => $group->sum('commission_ttc'),
            ]);

        $allContrats = Contrat::where('statut', 'actif')
            ->select(['id', 'bien_id', 'locataire_id', 'loyer_contractuel'])
            ->with([
                'bien:id,agency_id,proprietaire_id,reference,adresse,ville',
                'bien.proprietaire:id,name',
                'locataire:id,name,telephone',
            ])
            ->get();

        $contratsPaies = Paiement::where('statut', '!=', 'annule')
            ->whereYear('periode', $annee)
            ->whereMonth('periode', $mois)
            ->pluck('contrat_id')
            ->toArray();

        $biensImpayés = $allContrats->filter(
            fn($c) => ! in_array($c->id, $contratsPaies)
        );

        $nbBiens      = Bien::count();
        $nbBiensLoues = Bien::where('statut', 'loue')->count();

        $statsGenerales = [
            'nb_biens'         => $nbBiens,
            'nb_biens_loues'   => $nbBiensLoues,
            'nb_contrats'      => Contrat::where('statut', 'actif')->count(),
            'nb_proprietaires' => User::where('agency_id', $agencyId)->where('role', 'proprietaire')->count(),
            'nb_locataires'    => User::where('agency_id', $agencyId)->where('role', 'locataire')->count(),
            'taux_occupation'  => $nbBiens > 0 ? round(($nbBiensLoues / $nbBiens) * 100, 1) : 0,
        ];

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

    public function financier(Request $request)
    {
        $this->authorize('isAdmin');

        $annee = (int) $request->input('annee', now()->year);
        $mois  = (int) $request->input('mois',  now()->month);
        $data  = $this->getData($annee, $mois);

        $paiementsMois = new \Illuminate\Pagination\LengthAwarePaginator(
            $data['paiementsMois']->forPage($page = $request->input('page', 1), $perPage = 50),
            $data['paiementsMois']->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $data['paiementsMois'] = $paiementsMois;

        return view('rapports.financier', $data);
    }

    public function exportPdf(Request $request)
    {
        $this->authorize('isAdmin');

        $annee = (int) $request->input('annee', now()->year);
        $mois  = (int) $request->input('mois',  now()->month);

        $data           = $this->getData($annee, $mois);
        $data['agency'] = Auth::user()->agency;

        $pdf = Pdf::loadView('rapports.financier_pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 120);

        $filename = sprintf(
            'rapport-financier-%04d-%02d-%s.pdf',
            $annee, $mois,
            Auth::user()->agency?->name ?? 'agence'
        );

        return $pdf->download($filename);
    }
}