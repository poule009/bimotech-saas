<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TvaDeclaration;
use App\Services\TvaAgenceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TvaAgenceController extends Controller implements HasMiddleware
{
    public function __construct(private TvaAgenceService $service) {}

    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:fiscalite'),
        ];
    }

    public function index(Request $request): View
    {
        $agencyId = Auth::user()->agency_id;
        $annee    = (int) $request->get('annee', now()->year);

        $anneesDisponibles = range(now()->year, max(now()->year - 3, 2024));

        $mois = [];
        for ($m = 1; $m <= 12; $m++) {
            $decl         = TvaDeclaration::where('agency_id', $agencyId)
                ->where('mois', $m)
                ->where('annee', $annee)
                ->first();

            $dateMois     = Carbon::create($annee, $m, 1)->locale('fr');
            $dateEcheance = Carbon::create($annee, $m, 1)->addMonth()->day(15)->startOfDay();
            $maintenant   = now()->timezone('Africa/Dakar');

            // Déterminer le statut d'affichage
            if ($annee > now()->year || ($annee === now()->year && $m > now()->month)) {
                $statutAffichage = 'futur';
            } elseif (! $decl) {
                $statutAffichage = 'non_calcule';
            } elseif ($decl->statut === 'deposee') {
                $statutAffichage = 'deposee';
            } elseif ($decl->statut === 'validee') {
                $statutAffichage = 'validee';
            } elseif ($maintenant->gt($dateEcheance)) {
                $statutAffichage = 'en_retard';
            } else {
                $statutAffichage = 'brouillon';
            }

            $mois[] = [
                'numero'        => $m,
                'label'         => $dateMois->translatedFormat('F Y'),
                'declaration'   => $decl,
                'statut'        => $statutAffichage,
                'date_echeance' => $dateEcheance,
            ];
        }

        // Crédit TVA cumulé reporté non encore absorbé
        $creditCumule = TvaDeclaration::where('agency_id', $agencyId)
            ->where('annee', $annee)
            ->where('statut', 'deposee')
            ->sum('credit_reporte_sortant');

        return view('admin.tva-agence.index', compact('mois', 'annee', 'anneesDisponibles', 'creditCumule'));
    }

    public function show(int $annee, int $mois): View
    {
        $agencyId = Auth::user()->agency_id;

        $tvaData = $this->service->calculerTvaCollectee($agencyId, $mois, $annee);

        $declaration = TvaDeclaration::where('agency_id', $agencyId)
            ->where('annee', $annee)
            ->where('mois', $mois)
            ->first();

        if (! $declaration) {
            $declaration = $this->service->creerOuMettreAJour($agencyId, $mois, $annee);
        }

        return view('admin.tva-agence.show', compact('declaration', 'tvaData', 'annee', 'mois'));
    }

    public function update(Request $request, int $annee, int $mois): RedirectResponse
    {
        $agencyId = Auth::user()->agency_id;

        $validated = $request->validate([
            'tva_achats_fournitures' => 'required|numeric|min:0',
            'tva_loyer_bureau'       => 'required|numeric|min:0',
            'tva_autres_deductible'  => 'required|numeric|min:0',
            'notes'                  => 'nullable|string|max:1000',
        ]);

        $declaration = TvaDeclaration::where('agency_id', $agencyId)
            ->where('annee', $annee)
            ->where('mois', $mois)
            ->firstOrFail();

        abort_if($declaration->statut === 'deposee', 403, 'Cette déclaration est déjà déposée.');

        $totalDeductible = (float) $validated['tva_achats_fournitures']
            + (float) $validated['tva_loyer_bureau']
            + (float) $validated['tva_autres_deductible'];

        $declaration->fill([
            'tva_achats_fournitures' => $validated['tva_achats_fournitures'],
            'tva_loyer_bureau'       => $validated['tva_loyer_bureau'],
            'tva_autres_deductible'  => $validated['tva_autres_deductible'],
            'total_tva_deductible'   => $totalDeductible,
            'notes'                  => $validated['notes'] ?? null,
        ]);
        $declaration->save();
        $declaration->calculerTvaNette();

        return redirect()
            ->route('admin.tva-agence.show', [$annee, $mois])
            ->with('success', 'TVA déductible enregistrée avec succès.');
    }

    public function valider(int $annee, int $mois): RedirectResponse
    {
        $agencyId = Auth::user()->agency_id;

        $declaration = TvaDeclaration::where('agency_id', $agencyId)
            ->where('annee', $annee)
            ->where('mois', $mois)
            ->firstOrFail();

        abort_if($declaration->statut === 'deposee', 403, 'Cette déclaration est déjà déposée.');

        // Snapshot final avant validation
        $this->service->creerOuMettreAJour($agencyId, $mois, $annee);
        $declaration->refresh();

        $declaration->statut = 'validee';
        $declaration->save();

        return redirect()
            ->route('admin.tva-agence.show', [$annee, $mois])
            ->with('success', 'Déclaration TVA validée.');
    }

    public function marquerDeposee(int $annee, int $mois): RedirectResponse
    {
        $agencyId = Auth::user()->agency_id;

        $declaration = TvaDeclaration::where('agency_id', $agencyId)
            ->where('annee', $annee)
            ->where('mois', $mois)
            ->firstOrFail();

        abort_if($declaration->statut === 'deposee', 403, 'Cette déclaration est déjà marquée comme déposée.');

        $declaration->statut      = 'deposee';
        $declaration->deposee_le  = now();
        $declaration->deposee_par = Auth::id();
        $declaration->save();

        return redirect()
            ->route('admin.tva-agence.show', [$annee, $mois])
            ->with('success', 'Déclaration marquée comme déposée à la DGI.');
    }

    public function exportPdf(int $annee, int $mois): \Illuminate\Http\Response
    {
        $agencyId = Auth::user()->agency_id;
        $agency   = Auth::user()->agency;

        $declaration = TvaDeclaration::where('agency_id', $agencyId)
            ->where('annee', $annee)
            ->where('mois', $mois)
            ->firstOrFail();

        $tvaData = $this->service->calculerTvaCollectee($agencyId, $mois, $annee);

        $pdf = Pdf::loadView('admin.tva-agence.pdf.declaration', compact(
            'declaration', 'tvaData', 'agency', 'annee', 'mois'
        ))->setPaper('a4');

        $slug     = Str::slug($agency->name ?? 'agence');
        $filename = "declaration-tva-{$annee}-" . str_pad($mois, 2, '0', STR_PAD_LEFT) . "-{$slug}.pdf";

        return $pdf->download($filename);
    }

    public function recalculer(int $annee, int $mois): JsonResponse
    {
        $agencyId = Auth::user()->agency_id;

        $existing = TvaDeclaration::where('agency_id', $agencyId)
            ->where('annee', $annee)
            ->where('mois', $mois)
            ->first();

        if ($existing?->statut === 'deposee') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de recalculer une déclaration déjà déposée.',
            ], 403);
        }

        try {
            $decl = $this->service->creerOuMettreAJour($agencyId, $mois, $annee);

            return response()->json([
                'success'             => true,
                'total_tva_collectee' => (float) $decl->total_tva_collectee,
                'tva_nette_due'       => (float) $decl->tva_nette_due,
                'message'             => 'TVA collectée recalculée avec succès.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Erreur recalcul TVA agence', [
                'agency_id' => $agencyId,
                'mois'      => $mois,
                'annee'     => $annee,
                'error'     => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du recalcul.',
            ], 500);
        }
    }
}
