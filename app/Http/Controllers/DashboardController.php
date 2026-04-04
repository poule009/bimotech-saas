<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
// use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    // ─────────────────────────────────────────────────────────────────────
    // DASHBOARD ADMIN
    // ─────────────────────────────────────────────────────────────────────

    public function admin()
    {
        $this->authorize('isAdmin');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        $agencyId = $user->agency_id;

        // ── CORRECTION : 10 requêtes → 1 requête pour les stats all-time ─
        // Avant : sum() x5 + count() x5 = 10 allers-retours DB séparés
        // Après : 1 seul selectRaw qui ramène tout en une passe
        $statsRaw = Paiement::where('statut', 'valide')
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
                (SELECT COUNT(*) FROM biens WHERE agency_id = ? AND deleted_at IS NULL)                  AS nb_biens,
                (SELECT COUNT(*) FROM biens WHERE agency_id = ? AND statut = 'loue' AND deleted_at IS NULL) AS nb_biens_loues,
                (SELECT COUNT(*) FROM contrats WHERE agency_id = ? AND statut = 'actif')                 AS nb_contrats,
                (SELECT COUNT(*) FROM users WHERE agency_id = ? AND role = 'proprietaire')               AS nb_proprietaires,
                (SELECT COUNT(*) FROM users WHERE agency_id = ? AND role = 'locataire')                  AS nb_locataires
        ", [$agencyId, $agencyId, $agencyId, $agencyId, $agencyId]);

        $stats = [
            'total_loyers'         => (float) ($statsRaw->total_loyers         ?? 0),
            'total_commissions'    => (float) ($statsRaw->total_commissions     ?? 0),
            'total_tva'            => (float) ($statsRaw->total_tva             ?? 0),
            'total_commission_ttc' => (float) ($statsRaw->total_commission_ttc  ?? 0),
            'total_net_proprio'    => (float) ($statsRaw->total_net_proprio     ?? 0),
            'nb_biens'             => (int)   ($compteurs->nb_biens             ?? 0),
            'nb_biens_loues'       => (int)   ($compteurs->nb_biens_loues       ?? 0),
            'nb_contrats'          => (int)   ($compteurs->nb_contrats          ?? 0),
            'nb_proprietaires'     => (int)   ($compteurs->nb_proprietaires     ?? 0),
            'nb_locataires'        => (int)   ($compteurs->nb_locataires        ?? 0),
        ];

        $stats['taux_occupation'] = $stats['nb_biens'] > 0
            ? round(($stats['nb_biens_loues'] / $stats['nb_biens']) * 100, 1)
            : 0;

        // ── CORRECTION : 4 requêtes → 1 pour les stats du mois courant ──
        // Avant : sum() x3 + count() = 4 requêtes avec les mêmes filtres répétés
        // Après : 1 seul selectRaw
        $statsMoisRaw = Paiement::where('statut', 'valide')
            ->whereYear('periode', now()->year)
            ->whereMonth('periode', now()->month)
            ->selectRaw('
                COALESCE(SUM(montant_encaisse), 0)  AS loyers,
                COALESCE(SUM(commission_ttc), 0)    AS commissions,
                COALESCE(SUM(net_proprietaire), 0)  AS net_proprio,
                COUNT(*)                             AS nb_payes
            ')
            ->first();

        $statsMois = [
            'loyers'      => (float) ($statsMoisRaw->loyers      ?? 0),
            'commissions' => (float) ($statsMoisRaw->commissions  ?? 0),
            'net_proprio' => (float) ($statsMoisRaw->net_proprio  ?? 0),
            'nb_payes'    => (int)   ($statsMoisRaw->nb_payes     ?? 0),
        ];

        // ── Impayés du mois ───────────────────────────────────────────────
        $nb_impayes_mois = Contrat::where('statut', 'actif')
            ->whereDoesntHave('paiements', function ($q) {
                $q->whereYear('periode', now()->year)
                  ->whereMonth('periode', now()->month)
                  ->where('statut', '!=', 'annule');
            })
            ->count();

        $montant_du_mois = Contrat::where('statut', 'actif')
            ->whereDoesntHave('paiements', function ($q) {
                $q->whereYear('periode', now()->year)
                  ->whereMonth('periode', now()->month)
                  ->where('statut', '!=', 'annule');
            })
            ->sum('loyer_contractuel');

        // ── Urgences impayés (5 plus anciens) ────────────────────────────
        $impayes_urgents = Contrat::where('statut', 'actif')
            ->whereDoesntHave('paiements', function ($q) {
                $q->whereYear('periode', now()->year)
                  ->whereMonth('periode', now()->month)
                  ->where('statut', '!=', 'annule');
            })
            ->select(['id', 'agency_id', 'bien_id', 'locataire_id', 'loyer_contractuel', 'statut'])
            ->with([
                'bien:id,reference,adresse,ville',
                'locataire:id,name',
            ])
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        // ── Contrats expirant dans 30 jours ──────────────────────────────
        $contrats_a_renouveler = Contrat::where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->where('date_fin', '>=', now()->toDateString())
            ->where('date_fin', '<=', now()->addDays(30)->toDateString())
            ->select(['id', 'agency_id', 'bien_id', 'locataire_id', 'date_fin', 'loyer_contractuel'])
            ->with([
                'bien:id,reference,adresse,ville',
                'locataire:id,name',
            ])
            ->orderBy('date_fin')
            ->get();

        $contratsActifs = Contrat::where('statut', 'actif')->count();

        // ── Derniers paiements ────────────────────────────────────────────
        $derniersPaiements = Paiement::select([
                'id', 'agency_id', 'contrat_id',
                'periode', 'montant_encaisse', 'commission_ttc', 'net_proprietaire',
                'mode_paiement', 'date_paiement', 'statut', 'reference_paiement',
            ])
            ->with([
                'contrat:id,bien_id,locataire_id',
                'contrat.bien:id,reference,adresse,ville',
                'contrat.locataire:id,name',
            ])
            ->where('statut', 'valide')
            ->orderByDesc('date_paiement')
            ->limit(6)
            ->get();

        // ── Graphique 6 mois ──────────────────────────────────────────────
        $loyersParMois = Paiement::where('statut', 'valide')
            ->where('periode', '>=', now()->subMonths(6)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(periode, '%Y-%m') as mois"),
                DB::raw('SUM(montant_encaisse) as total'),
                DB::raw('SUM(commission_ttc) as commission')
            )
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // ── Bilan du mois ─────────────────────────────────────────────────
        $bilanMois = [
            'attendu'     => Contrat::where('statut', 'actif')->sum('loyer_contractuel'),
            'encaisse'    => $statsMois['loyers'],
            'a_recouvrer' => $montant_du_mois,
        ];

        // ── Onboarding ────────────────────────────────────────────────────
        $agency     = $user->agency;
        $onboarding = null;

        if ($agency && ! $agency->onboarding_completed) {
            $onboarding = $agency->checkOnboarding();
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

    // ─────────────────────────────────────────────────────────────────────
    // DASHBOARD PROPRIÉTAIRE
    // ─────────────────────────────────────────────────────────────────────

    public function proprietaire()
    {
        $this->authorize('isProprietaire');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $biens = Bien::where('proprietaire_id', $user->id)
            ->select(['id', 'agency_id', 'proprietaire_id', 'reference', 'type', 'adresse', 'ville', 'statut', 'loyer_mensuel'])
            ->withCount('contrats')
            ->paginate(6);

        $contratIds = Contrat::whereHas(
            'bien', fn($q) => $q->where('proprietaire_id', $user->id)
        )->pluck('id');

        // ── CORRECTION : 6 requêtes → 1 selectRaw ────────────────────────
        // Avant : sum() x3 + count() + first() + sum() = 6 requêtes avec
        //         whereIn('contrat_id', ...) répété identiquement à chaque fois
        // Après : 1 seul selectRaw regroupe tout
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

        // Le dernier paiement complet (pour affichage détaillé en vue)
        $dernierPaiement = $aggrRaw->date_dernier_paiement
            ? Paiement::whereIn('contrat_id', $contratIds)
                ->where('statut', 'valide')
                ->where('date_paiement', $aggrRaw->date_dernier_paiement)
                ->select(['id', 'contrat_id', 'montant_encaisse', 'date_paiement', 'reference_paiement'])
                ->first()
            : null;

        $stats = [
            'nb_biens'         => $biens->total(),
            'nb_biens_loues'   => Bien::where('proprietaire_id', $user->id)->where('statut', 'loue')->count(),
            'total_loyers'     => (float) ($aggrRaw->total_loyers    ?? 0),
            'total_net'        => (float) ($aggrRaw->total_net        ?? 0),
            'total_commission' => (float) ($aggrRaw->total_commission ?? 0),
            'nb_paiements'     => (int)   ($aggrRaw->nb_paiements    ?? 0),
            'dernier_paiement' => $dernierPaiement,
            'caution'          => (float) $cautionTotal,
        ];

        $paiements = Paiement::whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->select(['id', 'agency_id', 'contrat_id', 'periode', 'montant_encaisse', 'net_proprietaire', 'mode_paiement', 'date_paiement', 'reference_paiement'])
            ->with([
                'contrat:id,bien_id,locataire_id',
                'contrat.bien:id,reference',
                'contrat.locataire:id,name',
            ])
            ->orderByDesc('date_paiement')
            ->paginate(5);

        return view('proprietaire.dashboard', compact('biens', 'stats', 'paiements'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // DASHBOARD LOCATAIRE
    // ─────────────────────────────────────────────────────────────────────

    public function locataire()
    {
        $this->authorize('isLocataire');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $contrat = Contrat::with([
                'bien:id,reference,adresse,ville,type',
                'bien.proprietaire:id,name,telephone',
            ])
            ->where('locataire_id', $user->id)
            ->where('statut', 'actif')
            ->select(['id', 'bien_id', 'locataire_id', 'loyer_contractuel', 'date_debut', 'date_fin', 'statut', 'caution'])
            ->first();

        $paiements       = collect();
        $dernierPaiement = null;

        if ($contrat) {
            $paiements = Paiement::where('contrat_id', $contrat->id)
                ->where('statut', 'valide')
                ->select(['id', 'contrat_id', 'periode', 'montant_encaisse', 'mode_paiement', 'date_paiement', 'reference_paiement'])
                ->orderByDesc('periode')
                ->get();

            $dernierPaiement = $paiements->first();
        }

        $prochainePeriode = $dernierPaiement
            ? \Carbon\Carbon::parse($dernierPaiement->periode)->addMonth()
            : ($contrat ? \Carbon\Carbon::parse($contrat->date_debut) : null);

        // 1 seule requête pour les agrégats locataire
        $aggrLoc = $contrat
            ? Paiement::where('contrat_id', $contrat->id)
                ->where('statut', 'valide')
                ->selectRaw('
                    COALESCE(SUM(montant_encaisse), 0) AS total_paye,
                    COUNT(*)                            AS nb_paiements
                ')
                ->first()
            : null;

        $stats = [
            'total_paye'   => (float) ($aggrLoc->total_paye   ?? 0),
            'nb_paiements' => (int)   ($aggrLoc->nb_paiements ?? 0),
        ];

        return view('locataire.dashboard', compact(
            'contrat', 'paiements', 'dernierPaiement', 'prochainePeriode', 'stats'
        ));
    }
}