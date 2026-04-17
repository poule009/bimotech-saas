<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * LocataireDashboardController — Tableau de bord du locataire.
 *
 * Responsabilité unique (SRP) : afficher uniquement le dashboard locataire.
 * Le contrat actif et les agrégats sont mis en cache 20 min :
 * les données d'un locataire changent peu en cours de journée.
 */
class LocataireDashboardController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(): View
    {
        $this->authorize('isLocataire');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ── Contrat actif + agrégats (cache 20 min) ───────────────────────
        // Clé incluant userId → isolation parfaite entre locataires.
        $cached = Cache::remember("locataire_dashboard_{$user->id}", 1200, function () use ($user) {
            $contrat = Contrat::with([
                'bien:id,reference,adresse,ville,type',
                'bien.proprietaire:id,name,telephone',
            ])
                ->where('locataire_id', $user->id)
                ->where('statut', 'actif')
                ->select([
                    'id', 'bien_id', 'locataire_id',
                    'loyer_contractuel', 'loyer_nu', 'charges_mensuelles',
                    'date_debut', 'date_fin', 'statut', 'caution',
                    'type_bail', 'reference_bail',
                ])
                ->first();

            $aggrLoc = $contrat
                ? Paiement::where('contrat_id', $contrat->id)
                    ->where('statut', 'valide')
                    ->selectRaw('
                        COALESCE(SUM(montant_encaisse), 0) AS total_paye,
                        COUNT(*)                            AS nb_paiements
                    ')
                    ->first()
                : null;

            return [
                'contrat'  => $contrat,
                'aggrLoc'  => $aggrLoc,
            ];
        });

        $contrat = $cached['contrat'];
        $aggrLoc = $cached['aggrLoc'];

        // ── Paiements récents (pas de cache — liste consultée fréquemment) ─
        $paiements       = collect();
        $dernierPaiement = null;

        if ($contrat) {
            $paiements = Paiement::where('contrat_id', $contrat->id)
                ->where('statut', 'valide')
                ->select([
                    'id', 'contrat_id', 'periode', 'montant_encaisse',
                    'mode_paiement', 'date_paiement', 'reference_paiement',
                ])
                ->orderByDesc('periode')
                ->get();

            $dernierPaiement = $paiements->first();
        }

        $prochainePeriode = $dernierPaiement
            ? Carbon::parse($dernierPaiement->periode)->addMonth()
            : ($contrat ? Carbon::parse($contrat->date_debut) : null);

        $stats = [
            'total_paye'   => (float) ($aggrLoc->total_paye   ?? 0),
            'nb_paiements' => (int)   ($aggrLoc->nb_paiements ?? 0),
        ];

        return view('locataire.dashboard', compact(
            'contrat', 'paiements', 'dernierPaiement', 'prochainePeriode', 'stats'
        ));
    }
}
