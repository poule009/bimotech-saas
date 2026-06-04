<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChargeAgenceRequest;
use App\Models\ChargeAgence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChargeAgenceController extends Controller
{
    public function index(Request $request): View
    {
        $mois   = $request->get('mois', now()->format('Y-m'));
        $categorie = $request->get('categorie');

        $query = ChargeAgence::query()->orderByDesc('date_charge');

        if ($mois) {
            $query->where('periode', $mois);
        }

        if ($categorie) {
            $query->where('categorie', $categorie);
        }

        $total   = (clone $query)->sum('montant');
        $charges = $query->paginate(30)->appends($request->query());
        $parCategorie = ChargeAgence::where('periode', $mois)
            ->selectRaw('categorie, SUM(montant) as total')
            ->groupBy('categorie')
            ->get();

        $moisDisponibles = ChargeAgence::selectRaw('periode')->distinct()
            ->orderByDesc('periode')->pluck('periode');

        return view('admin.charges-agence.index', compact(
            'charges', 'total', 'parCategorie', 'mois', 'categorie', 'moisDisponibles'
        ));
    }

    public function create(): View
    {
        return view('admin.charges-agence.create');
    }

    public function store(StoreChargeAgenceRequest $request): RedirectResponse
    {
        ChargeAgence::create($request->validated());

        return redirect()
            ->route('charges-agence.index')
            ->with('success', 'Charge enregistrée avec succès.');
    }

    public function edit(ChargeAgence $chargesAgence): View
    {
        return view('admin.charges-agence.edit', ['charge' => $chargesAgence]);
    }

    public function update(StoreChargeAgenceRequest $request, ChargeAgence $chargesAgence): RedirectResponse
    {
        $chargesAgence->update($request->validated());

        return redirect()
            ->route('charges-agence.index')
            ->with('success', 'Charge mise à jour.');
    }

    public function destroy(ChargeAgence $chargesAgence): RedirectResponse
    {
        $chargesAgence->delete();

        return redirect()
            ->route('charges-agence.index')
            ->with('success', 'Charge supprimée.');
    }
}
