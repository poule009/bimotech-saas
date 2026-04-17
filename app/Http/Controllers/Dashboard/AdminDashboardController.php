<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * AdminDashboardController — Tableau de bord de l'administrateur d'agence.
 *
 * Responsabilité unique (SRP) : afficher uniquement le dashboard admin.
 * Extrait de l'ancien DashboardController monolithique (362 lignes / 3 rôles).
 *
 * Cache :
 *  - Stats all-time + compteurs   → TTL 30 min (données lentes à changer)
 *  - Stats du mois courant        → TTL 10 min (plus volatiles)
 *  - Impayés urgents / renouvellements → pas de cache (données critiques temps réel)
 */
class AdminDashboardController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(): View|RedirectResponse
    {
        $this->authorize('isAdmin');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        $agencyId = $user->agency_id;

        // ── Stats all-time (cache 30 min) ─────────────────────────────────
        // Clé incluant agencyId → chaque agence a son propre cache.
        // Invalidation automatique à l'expiration ; invalidation manuelle
        // possible via Cache::forget("admin_stats_{$agencyId}") lors d'un paiement.
        $stats = Cache::remember("admin_stats_{$agencyId}", 1800, function () use ($agencyId) {
            $statsRaw = Paiement::where('agency_id', $agencyId)
                ->where('statut', 'valide')
                ->selectRaw('
                    COALESCE(SUM(montant_encaisse), 0)   AS total_loyers,
                    COALESCE(SUM(commission_agence), 0)  AS total_commissions,
                    COALESCE(SUM(tva_commission), 0)     AS total_tva,
                    COALESCE(SUM(commission_ttc), 0)     AS total_commission_ttc,
                    COALESCE(SUM(net_proprietaire), 0)   AS total_net_proprio
                ')
                ->first();

            $compteurs = DB::selectOne("
                SELECT
                    (SELECT COUNT(*) FROM biens    WHERE agency_id = ? AND deleted_at IS NULL)                     AS nb_biens,
                    (SELECT COUNT(*) FROM biens    WHERE agency_id = ? AND statut = 'loue' AND deleted_at IS NULL) AS nb_biens_loues,
                    (SELECT COUNT(*) FROM contrats WHERE agency_id = ? AND statut = 'actif')                       AS nb_contrats,
                    (SELECT COUNT(*) FROM users    WHERE agency_id = ? AND role = 'proprietaire')                  AS nb_proprietaires,
                    (SELECT COUNT(*) FROM users    WHERE agency_id = ? AND role = 'locataire')                     AS nb_locataires
            ", array_fill(0, 5, $agencyId));

            $nbBiens     = (int) ($compteurs->nb_biens      ?? 0);
            $nbBiensLoues = (int) ($compteurs->nb_biens_loues ?? 0);

            return [
                'total_loyers'         => (float) ($statsRaw->total_loyers        ?? 0),
                'total_commissions'    => (float) ($statsRaw->total_commissions    ?? 0),
                'total_tva'            => (float) ($statsRaw->total_tva            ?? 0),
                'total_commission_ttc' => (float) ($statsRaw->total_commission_ttc ?? 0),
                'total_net_proprio'    => (float) ($statsRaw->total_net_proprio    ?? 0),
                'nb_biens'             => $nbBiens,
                'nb_biens_loues'       => $nbBiensLoues,
                'nb_contrats'          => (int) ($compteurs->nb_contrats           ?? 0),
                'nb_proprietaires'     => (int) ($compteurs->nb_proprietaires      ?? 0),
                'nb_locataires'        => (int) ($compteurs->nb_locataires         ?? 0),
                'taux_occupation'      => $nbBiens > 0
                    ? round(($nbBiensLoues / $nbBiens) * 100, 1)
                    : 0,
            ];
        });

        // ── Stats du mois courant (cache 10 min) ──────────────────────────
        $moisKey   = now()->format('Y-m');
        $statsMois = Cache::remember("admin_stats_mois_{$agencyId}_{$moisKey}", 600, function () use ($agencyId) {
            $raw = Paiement::where('agency_id', $agencyId)
                ->where('statut', 'valide')
                ->whereYear('periode', now()->year)
                ->whereMonth('periode', now()->month)
                ->selectRaw('
                    COALESCE(SUM(montant_encaisse), 0)  AS loyers,
                    COALESCE(SUM(commission_ttc), 0)    AS commissions,
                    COALESCE(SUM(net_proprietaire), 0)  AS net_proprio,
                    COUNT(*)                             AS nb_payes
                ')
                ->first();

            return [
                'loyers'      => (float) ($raw->loyers      ?? 0),
                'commissions' => (float) ($raw->commissions  ?? 0),
                'net_proprio' => (float) ($raw->net_proprio  ?? 0),
                'nb_payes'    => (int)   ($raw->nb_payes     ?? 0),
            ];
        });

        // ── Impayés du mois (pas de cache — données critiques) ────────────
        $debutMois  = now()->startOfMonth()->toDateString();
        $finMois    = now()->endOfMonth()->toDateString();
        $contratIds = Contrat::where('agency_id', $agencyId)->where('statut', 'actif')->pluck('id');

        $payes = Paiement::where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereBetween('periode', [$debutMois, $finMois])
            ->pluck('contrat_id')
            ->toArray();

        $nb_impayes_mois = $contratIds->count() - count(array_unique($payes));
        $montant_du_mois = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->sum('loyer_contractuel');

        $impayes_urgents = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->whereNotIn('id', $payes)
            ->with(['bien:id,reference,adresse,ville', 'locataire:id,name,telephone'])
            ->select(['id', 'bien_id', 'locataire_id', 'loyer_contractuel', 'date_debut'])
            ->limit(5)
            ->get();

        // ── Contrats à renouveler dans 30 jours ──────────────────────────
        $contrats_a_renouveler = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->whereBetween('date_fin', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->with(['bien:id,reference', 'locataire:id,name'])
            ->select(['id', 'bien_id', 'locataire_id', 'date_fin', 'loyer_contractuel'])
            ->orderBy('date_fin')
            ->get();

        // ── Contrats actifs (sidebar) ─────────────────────────────────────
        $contratsActifs = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->with(['bien:id,reference', 'locataire:id,name'])
            ->select(['id', 'bien_id', 'locataire_id', 'date_fin'])
            ->orderBy('date_fin')
            ->limit(5)
            ->get();

        // ── Derniers paiements ────────────────────────────────────────────
        $derniersPaiements = Paiement::where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->with([
                'contrat:id,bien_id,locataire_id',
                'contrat.bien:id,reference',
                'contrat.locataire:id,name',
            ])
            ->select([
                'id', 'contrat_id', 'agency_id', 'periode', 'date_paiement',
                'montant_encaisse', 'commission_ttc', 'net_proprietaire',
                'mode_paiement', 'statut', 'reference_paiement',
            ])
            ->orderByDesc('date_paiement')
            ->limit(8)
            ->get();

        // ── Évolution loyers 12 derniers mois (graphique) ─────────────────
        $loyersParMois = Paiement::where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->where('periode', '>=', now()->subMonths(11)->startOfMonth()->toDateString())
            ->selectRaw("
                DATE_FORMAT(periode, '%Y-%m') AS mois,
                COALESCE(SUM(montant_encaisse), 0) AS total,
                COALESCE(SUM(commission_ttc), 0)   AS commission
            ")
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // ── Bilan du mois ─────────────────────────────────────────────────
        $bilanMois = [
            'loyers'      => $statsMois['loyers'],
            'commissions' => $statsMois['commissions'],
            'net_proprio' => $statsMois['net_proprio'],
            'nb_payes'    => $statsMois['nb_payes'],
            'nb_impayes'  => max(0, $nb_impayes_mois),
            'attendu'     => (float) $montant_du_mois,
            'encaisse'    => $statsMois['loyers'],
            'a_recouvrer' => max(0, (float) $montant_du_mois - $statsMois['loyers']),
        ];

        // ── Onboarding ────────────────────────────────────────────────────
        $onboarding = null;
        $agency     = $user->agency;
        if ($agency && ! $agency->onboarding_completed) {
            $onboarding = [
                'has_biens'     => Bien::where('agency_id', $agencyId)->exists(),
                'has_contrats'  => Contrat::where('agency_id', $agencyId)->exists(),
                'has_paiements' => Paiement::where('agency_id', $agencyId)->exists(),
                'settings_ok'   => ! empty($agency->telephone) && ! empty($agency->adresse),
            ];
        }

        return view('admin.dashboard', compact(
            'stats',
            'statsMois',
            'nb_impayes_mois',
            'montant_du_mois',
            'impayes_urgents',
            'contrats_a_renouveler',
            'contratsActifs',
            'derniersPaiements',
            'loyersParMois',
            'bilanMois',
            'onboarding'
        ));
    }
}
