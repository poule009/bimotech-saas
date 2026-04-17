<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use App\Services\BailleurPortfolioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BailleurController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly BailleurPortfolioService $portfolioService)
    {
    }

    // ─────────────────────────────────────────────────────────────────────
    // INDEX — Liste de tous les bailleurs du portefeuille de l'agence
    // ─────────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $this->authorize('isStaff');

        $bailleurs = $this->portfolioService->getPortfolioIndex(Auth::user()->agency_id);

        return view('bailleurs.index', compact('bailleurs'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // SHOW — Fiche détaillée d'un bailleur
    // ─────────────────────────────────────────────────────────────────────

    public function show(int $userId): View
    {
        $this->authorize('isStaff');

        $agencyId = Auth::user()->agency_id;

        // Vérification IDOR : filtre agency_id obligatoire avant tout chargement.
        $user = User::where('id', $userId)
            ->where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->with('proprietaire')
            ->firstOrFail();

        $annee = (int) request('annee', now()->year);
        $mois  = request('mois');

        $data = $this->portfolioService->getPortfolioDetail($userId, $agencyId, $annee, $mois);

        return view('bailleurs.show', array_merge(
            ['user' => $user, 'annee' => $annee, 'mois' => $mois],
            $data,
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // EXPORT PDF — Rapport de gestion mensuel isolé
    // ─────────────────────────────────────────────────────────────────────

    public function exportPdf(int $userId): \Illuminate\Http\Response
    {
        $this->authorize('isStaff');

        $agencyId = Auth::user()->agency_id;
        $agency   = Auth::user()->agency;

        // Filtre agency_id : protection IDOR (même vérification que dans show())
        $user = User::where('id', $userId)
            ->where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->with('proprietaire')
            ->firstOrFail();

        $bienIds    = Bien::where('agency_id', $agencyId)
                         ->where('proprietaire_id', $userId)
                         ->pluck('id');

        if ($bienIds->isEmpty()) abort(403);

        $contratIds = Contrat::whereIn('bien_id', $bienIds)->pluck('id');

        // Période : mois + année passés en paramètre (défaut = mois en cours)
        $annee = (int) request('annee', now()->year);
        $mois  = (int) request('mois',  now()->month);

        $paiements = Paiement::where('agency_id', $agencyId)
            ->whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->whereYear('periode', $annee)
            ->whereMonth('periode', $mois)
            ->with([
                'depenses',
                'contrat:id,bien_id,reference_bail',
                'contrat.bien:id,reference,adresse,ville',
            ])
            ->orderBy('periode')
            ->get();

        $totalLoyers      = (float) $paiements->sum('montant_encaisse');
        $totalCommissions = (float) $paiements->sum('commission_ttc');
        $totalDepenses    = (float) $paiements->flatMap->depenses->sum('montant');
        $netFinal         = round($totalLoyers - $totalCommissions - $totalDepenses, 2);

        $periode = Carbon::createFromDate($annee, $mois, 1);

        $pdf = Pdf::loadView('bailleurs.pdf.rapport-gestion', compact(
            'user', 'agency', 'paiements',
            'totalLoyers', 'totalCommissions', 'totalDepenses', 'netFinal',
            'periode'
        ))->setPaper('a4', 'portrait');

        $filename = 'rapport-gestion-' . $user->name . '-' . $periode->format('Y-m') . '.pdf';

        return $pdf->download(str_replace(' ', '-', $filename));
    }
}
