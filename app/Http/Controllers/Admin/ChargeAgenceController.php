<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChargeAgenceRequest;
use App\Models\ChargeAgence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class ChargeAgenceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:comptabilite'),
        ];
    }

    public function store(StoreChargeAgenceRequest $request): RedirectResponse
    {
        ChargeAgence::create($request->validated() + [
            'recurrente' => $request->boolean('recurrente'),
        ]);

        return redirect()
            ->route('admin.comptabilite.index')
            ->with('success', 'Dépense enregistrée avec succès.');
    }

    public function destroy(ChargeAgence $charges_agence): RedirectResponse
    {
        $this->authorize('isAdmin');
        abort_unless($charges_agence->agency_id === Auth::user()->agency_id, 403);

        $charges_agence->delete();

        return back()->with('success', 'Dépense supprimée.');
    }

    /**
     * Reporte les charges fixes (récurrentes) sur le mois courant en un clic.
     * Copie le dernier modèle de chaque libellé récurrent absent du mois.
     */
    public function reporter(): RedirectResponse
    {
        $this->authorize('isAdmin');

        $agencyId = Auth::user()->agency_id;
        $periode  = now()->format('Y-m');

        $dejaCeMois = ChargeAgence::where('agency_id', $agencyId)
            ->where('periode', $periode)
            ->where('recurrente', true)
            ->pluck('libelle')
            ->all();

        $modeles = ChargeAgence::where('agency_id', $agencyId)
            ->where('recurrente', true)
            ->where('periode', '<', $periode)
            ->orderByDesc('date_charge')
            ->get()
            ->unique('libelle');

        $count = 0;
        foreach ($modeles as $modele) {
            if (in_array($modele->libelle, $dejaCeMois, true)) {
                continue;
            }

            ChargeAgence::create([
                'agency_id'   => $agencyId,
                'libelle'     => $modele->libelle,
                'montant'     => $modele->montant,
                'categorie'   => $modele->categorie,
                'recurrente'  => true,
                'date_charge' => now()->startOfMonth()->toDateString(),
                'periode'     => $periode,
                'prestataire' => $modele->prestataire,
                'notes'       => $modele->notes,
            ]);
            $count++;
        }

        return redirect()
            ->route('admin.comptabilite.index')
            ->with($count ? 'success' : 'info', $count
                ? "$count charge(s) fixe(s) reportée(s) sur le mois courant."
                : 'Toutes les charges fixes sont déjà présentes ce mois-ci.');
    }
}
