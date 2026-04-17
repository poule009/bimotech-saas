<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * ProprietaireDashboardController — Tableau de bord du propriétaire.
 *
 * Responsabilité unique (SRP) : afficher uniquement le dashboard propriétaire.
 * Les biens sont paginés (pas de cache) car le propriétaire navigue entre les pages.
 * Les agrégats financiers (all-time) sont mis en cache 15 min.
 */
class ProprietaireDashboardController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(): View
    {
        $this->authorize('isProprietaire');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Biens paginés — pas de cache car la pagination est dynamique.
        $biens = Bien::where('proprietaire_id', $user->id)
            ->select([
                'id', 'agency_id', 'proprietaire_id',
                'reference', 'type', 'adresse', 'ville',
                'statut', 'loyer_mensuel',
            ])
            ->withCount('contrats')
            ->paginate(6);

        $contratIds = Contrat::whereHas(
            'bien', fn($q) => $q->where('proprietaire_id', $user->id)
        )->pluck('id');

        // ── Agrégats financiers (cache 15 min) ────────────────────────────
        // Clé incluant userId → isolation parfaite entre propriétaires.
        $stats = Cache::remember("proprietaire_stats_{$user->id}", 900, function () use ($user, $contratIds) {
            $aggrRaw = Paiement::whereIn('contrat_id', $contratIds)
                ->where('statut', 'valide')
                ->selectRaw('
                    COALESCE(SUM(montant_encaisse), 0)  AS total_loyers,
                    COALESCE(SUM(net_proprietaire), 0)  AS total_net,
                    COALESCE(SUM(commission_ttc), 0)    AS total_commission,
                    COUNT(*)                             AS nb_paiements,
                    MAX(date_paiement)                  AS date_dernier_paiement
                ')
                ->first();

            $cautionTotal = Contrat::whereIn('id', $contratIds)->sum('caution');

            $dernierPaiement = $aggrRaw->date_dernier_paiement
                ? Paiement::whereIn('contrat_id', $contratIds)
                    ->where('statut', 'valide')
                    ->where('date_paiement', $aggrRaw->date_dernier_paiement)
                    ->select(['id', 'contrat_id', 'montant_encaisse', 'date_paiement', 'reference_paiement'])
                    ->first()
                : null;

            return [
                'nb_biens'         => Bien::where('proprietaire_id', $user->id)->count(),
                'nb_biens_loues'   => Bien::where('proprietaire_id', $user->id)->where('statut', 'loue')->count(),
                'total_loyers'     => (float) ($aggrRaw->total_loyers    ?? 0),
                'total_net'        => (float) ($aggrRaw->total_net        ?? 0),
                'total_commission' => (float) ($aggrRaw->total_commission ?? 0),
                'nb_paiements'     => (int)   ($aggrRaw->nb_paiements     ?? 0),
                'dernier_paiement' => $dernierPaiement,
                'caution'          => (float) $cautionTotal,
            ];
        });

        // ── Derniers paiements (pas de cache — données importantes) ───────
        $paiements = Paiement::whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->select([
                'id', 'agency_id', 'contrat_id', 'periode',
                'montant_encaisse', 'net_proprietaire',
                'mode_paiement', 'date_paiement', 'reference_paiement',
            ])
            ->with([
                'contrat:id,bien_id,locataire_id',
                'contrat.bien:id,reference',
                'contrat.locataire:id,name',
            ])
            ->orderByDesc('date_paiement')
            ->paginate(5);

        $currentAgency = $user->agency;

        return view('proprietaire.dashboard', compact('biens', 'stats', 'paiements', 'currentAgency'));
    }
}
