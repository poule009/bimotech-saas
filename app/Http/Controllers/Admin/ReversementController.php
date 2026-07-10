<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReversementRequest;
use App\Models\ReversementProprietaire;
use App\Models\User;
use App\Services\ComptabiliteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReversementController extends Controller implements HasMiddleware
{
    public function __construct(private ComptabiliteService $comptabiliteService) {}

    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:comptabilite'),
        ];
    }

    public function create(Request $request): View
    {
        $agencyId = Auth::user()->agency_id;

        $proprietaires = User::where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->orderBy('name')
            ->get();

        $proprietaireSelectionne = $request->get('proprietaire_id')
            ? $proprietaires->firstWhere('id', $request->get('proprietaire_id'))
            : null;

        $soldeMandant = null;
        if ($proprietaireSelectionne) {
            $compteData  = $this->comptabiliteService->compteMandant($agencyId, $proprietaireSelectionne->id);
            $soldeMandant = $compteData['solde_restant'];
        }

        return view('admin.reversements.create', compact(
            'proprietaires', 'proprietaireSelectionne', 'soldeMandant'
        ));
    }

    public function store(StoreReversementRequest $request): RedirectResponse
    {
        ReversementProprietaire::create($request->validated());

        // Retour sur la fiche du propriétaire (solde à jour) plutôt que la liste.
        return redirect()
            ->route('admin.reversements.compte-mandant', $request->integer('proprietaire_id'))
            ->with('success', 'Reversement enregistré ✓ — solde mis à jour.');
    }

    public function compteMandant(Request $request, User $proprietaire): View
    {
        $agencyId = Auth::user()->agency_id;

        abort_unless($proprietaire->agency_id === $agencyId && $proprietaire->role === 'proprietaire', 404);

        $periode = $request->get('periode');
        // Solde EN COURS (running) pour le bandeau + le reversement ; le détail des
        // opérations reste filtrable par période.
        $compteGlobal = $this->comptabiliteService->compteMandant($agencyId, $proprietaire->id);
        $compte       = $this->comptabiliteService->compteMandant($agencyId, $proprietaire->id, $periode);

        $reversements = ReversementProprietaire::where('proprietaire_id', $proprietaire->id)
            ->orderByDesc('date_reversement')
            ->get();

        $periodes = \App\Models\Paiement::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $proprietaire->id))
            ->selectRaw("DATE_FORMAT(date_paiement, '%Y-%m') as periode")
            ->distinct()
            ->orderByDesc('periode')
            ->pluck('periode');

        // Dépenses directes (sans bien) — non portées par un paiement
        $depensesDirectes = \App\Models\DepenseGestion::where('agency_id', $agencyId)
            ->whereNull('paiement_id')
            ->where('proprietaire_id', $proprietaire->id)
            ->orderByDesc('date_depense')
            ->get();

        // Biens du propriétaire ayant au moins un loyer encaissé (imputables)
        $biensImputables = \App\Models\Bien::where('agency_id', $agencyId)
            ->where('proprietaire_id', $proprietaire->id)
            ->whereHas('contrats.paiements', fn($q) => $q->where('statut', 'valide'))
            ->orderBy('titre')
            ->get(['id', 'titre', 'reference']);

        $categoriesProprio = \App\Models\DepenseGestion::CATEGORIES;
        $modesPaiement     = ReversementProprietaire::MODES_PAIEMENT;

        return view('admin.reversements.compte-mandant', compact(
            'proprietaire', 'compte', 'compteGlobal', 'reversements', 'periodes', 'periode',
            'depensesDirectes', 'biensImputables', 'categoriesProprio', 'modesPaiement'
        ));
    }

    public function relevePdf(Request $request, User $proprietaire): \Illuminate\Http\Response
    {
        $agencyId = Auth::user()->agency_id;

        abort_unless($proprietaire->agency_id === $agencyId && $proprietaire->role === 'proprietaire', 404);

        $periode = $request->get('periode');
        $compte  = $this->comptabiliteService->compteMandant($agencyId, $proprietaire->id, $periode);
        $agency  = Auth::user()->agency;

        $reversementsPeriode = ReversementProprietaire::where('proprietaire_id', $proprietaire->id)
            ->where('agency_id', $agencyId)
            ->when($periode, function ($q) use ($periode) {
                $q->where(function ($q2) use ($periode) {
                    $q2->where(function ($q3) use ($periode) {
                        $q3->where('periode_debut', '<=', $periode)
                           ->where('periode_fin', '>=', $periode);
                    })->orWhere(function ($q3) {
                        $q3->whereNull('periode_debut')
                           ->whereNull('periode_fin');
                    });
                });
            })
            ->orderByDesc('date_reversement')
            ->get();

        $refDoc = 'RELEVE-' . $proprietaire->id . '-' . ($periode ?? now()->format('Y-m'));

        $pdf = Pdf::loadView('admin.reversements.pdf.releve-mensuel', compact(
            'proprietaire', 'compte', 'agency', 'periode', 'reversementsPeriode', 'refDoc'
        ))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 96)
            ->setOption('isRemoteEnabled', false);

        $filename = 'releve-gestion-' . $proprietaire->id . '-' . ($periode ?? now()->format('Y-m')) . '.pdf';

        return $pdf->download($filename);
    }
}
