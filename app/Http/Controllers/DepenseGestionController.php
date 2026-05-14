<?php

namespace App\Http\Controllers;

use App\Models\DepenseGestion;
use App\Models\Paiement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepenseGestionController extends Controller
{
    public function store(Request $request, Paiement $paiement): RedirectResponse
    {
        $this->authorize('isAdmin');

        // Isolation IDOR : le paiement doit appartenir à l'agence
        abort_unless($paiement->agency_id === Auth::user()->agency_id, 403);

        $data = $request->validate([
            'libelle'       => ['required', 'string', 'max:255'],
            'montant'       => ['required', 'numeric', 'min:1', 'max:99999999'],
            'categorie'     => ['required', 'in:' . implode(',', array_keys(DepenseGestion::CATEGORIES))],
            'date_depense'  => ['required', 'date', 'before_or_equal:today'],
            'prestataire'   => ['nullable', 'string', 'max:255'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);

        $paiement->depenses()->create(array_merge($data, [
            'agency_id' => Auth::user()->agency_id,
        ]));

        return back()->with('success', 'Dépense ajoutée avec succès.');
    }

    public function destroy(Paiement $paiement, DepenseGestion $depense): RedirectResponse
    {
        $this->authorize('isAdmin');

        // Double isolation : paiement + dépense dans la même agence
        abort_unless($paiement->agency_id === Auth::user()->agency_id, 403);
        abort_unless($depense->agency_id  === Auth::user()->agency_id, 403);
        abort_unless($depense->paiement_id === $paiement->id, 403);

        $depense->delete();

        return back()->with('success', 'Dépense supprimée.');
    }
}
