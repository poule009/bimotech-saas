<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReversementRequest;
use App\Models\ReversementProprietaire;
use App\Models\User;
use App\Services\ComptabiliteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReversementController extends Controller
{
    public function __construct(private ComptabiliteService $comptabiliteService) {}

    public function index(Request $request): View
    {
        $agencyId = Auth::user()->agency_id;

        $reversements = ReversementProprietaire::with('proprietaire')
            ->orderByDesc('date_reversement')
            ->paginate(30)
            ->appends($request->query());

        $soldesMandants = $this->comptabiliteService->soldesMandants($agencyId);

        $proprietaires = User::where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->orderBy('name')
            ->get();

        return view('admin.reversements.index', compact(
            'reversements', 'soldesMandants', 'proprietaires'
        ));
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

        return redirect()
            ->route('reversements.index')
            ->with('success', 'Reversement enregistré avec succès.');
    }

    public function compteMandant(Request $request, User $proprietaire): View
    {
        $agencyId = Auth::user()->agency_id;

        abort_unless($proprietaire->agency_id === $agencyId && $proprietaire->role === 'proprietaire', 404);

        $periode = $request->get('periode');
        $compte  = $this->comptabiliteService->compteMandant($agencyId, $proprietaire->id, $periode);

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

        return view('admin.reversements.compte-mandant', compact(
            'proprietaire', 'compte', 'reversements', 'periodes', 'periode'
        ));
    }
}
