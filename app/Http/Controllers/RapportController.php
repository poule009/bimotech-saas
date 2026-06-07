<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RapportController extends Controller implements HasMiddleware
{
    public function __construct(private ReportService $reportService) {}

    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:rapports_pdf'),
        ];
    }

    public function financier(Request $request)
    {
        $this->authorize('isAdmin');

        $annee = (int) $request->input('annee', now()->year);
        $mois  = (int) $request->input('mois',  now()->month);
        $data  = $this->reportService->generateFinancialReport(Auth::user()->agency_id, $annee, $mois);

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

        $data           = $this->reportService->generateFinancialReport(Auth::user()->agency_id, $annee, $mois);
        $data['agency'] = Auth::user()->agency;

        $pdf = Pdf::loadView('rapports.financier_pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 120);

        $filename = sprintf(
            'rapport-financier-%04d-%02d-%s.pdf',
            $annee, $mois,
            Str::slug(Auth::user()->agency?->name ?? 'agence')
        );

        return $pdf->download($filename);
    }
}