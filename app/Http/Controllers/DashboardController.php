<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
// use App\Models\User;
use Carbon\Carbon;
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

        // ── Stats all-time — CORRIGÉ : agency_id explicite sur le raw SQL ──
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
        ", [$agencyId, $agencyId, $agencyId, $agencyId, $agencyId]);

        $stats = [
            'total_loyers'         => (float) ($statsRaw->total_loyers        ?? 0),
            'total_commissions'    => (float) ($statsRaw->total_commissions    ?? 0),
            'total_tva'            => (float) ($statsRaw->total_tva            ?? 0),
            'total_commission_ttc' => (float) ($statsRaw->total_commission_ttc ?? 0),
            'total_net_proprio'    => (float) ($statsRaw->total_net_proprio    ?? 0),
            'nb_biens'             => (int)   ($compteurs->nb_biens            ?? 0),
            'nb_biens_loues'       => (int)   ($compteurs->nb_biens_loues      ?? 0),
            'nb_contrats'          => (int)   ($compteurs->nb_contrats         ?? 0),
            'nb_proprietaires'     => (int)   ($compteurs->nb_proprietaires    ?? 0),
            'nb_locataires'        => (int)   ($compteurs->nb_locataires       ?? 0),
        ];

        $stats['taux_occupation'] = $stats['nb_biens'] > 0
            ? round(($stats['nb_biens_loues'] / $stats['nb_biens']) * 100, 1)
            : 0;

        // ── Stats du mois courant — CORRIGÉ : agency_id explicite ──────────
        $statsMoisRaw = Paiement::where('agency_id', $agencyId)
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

        $statsMois = [
            'loyers'      => (float) ($statsMoisRaw->loyers      ?? 0),
            'commissions' => (float) ($statsMoisRaw->commissions  ?? 0),
            'net_proprio' => (float) ($statsMoisRaw->net_proprio  ?? 0),
            'nb_payes'    => (int)   ($statsMoisRaw->nb_payes     ?? 0),
        ];

        // ── Impayés du mois ───────────────────────────────────────────────
        $debutMois = now()->startOfMonth()->toDateString();
        $finMois   = now()->endOfMonth()->toDateString();

        $contratIds = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->pluck('id');

        $payes = Paiement::where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereBetween('periode', [$debutMois, $finMois])
            ->pluck('contrat_id')
            ->toArray();

        $nb_impayes_mois = $contratIds->count() - count(array_unique($payes));

        // ── Montant total dû ce mois ──────────────────────────────────────
        $montant_du_mois = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->sum('loyer_contractuel');

        // ── Impayés urgents (contrats actifs sans paiement ce mois) ──────
        $impayes_urgents = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->whereNotIn('id', $payes)
            ->with([
                'bien:id,reference,adresse,ville',
                'locataire:id,name,telephone',
            ])
            ->select(['id', 'bien_id', 'locataire_id', 'loyer_contractuel', 'date_debut'])
            ->limit(5)
            ->get();

        // ── Contrats à renouveler (expire dans 30 jours) ─────────────────
        $contrats_a_renouveler = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->whereBetween('date_fin', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->with([
                'bien:id,reference',
                'locataire:id,name',
            ])
            ->select(['id', 'bien_id', 'locataire_id', 'date_fin', 'loyer_contractuel'])
            ->orderBy('date_fin')
            ->get();

        // ── Contrats actifs (sidebar) ─────────────────────────────────────
        $contratsActifs = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->with([
                'bien:id,reference',
                'locataire:id,name',
            ])
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
            'loyers'       => $statsMois['loyers'],
            'commissions'  => $statsMois['commissions'],
            'net_proprio'  => $statsMois['net_proprio'],
            'nb_payes'     => $statsMois['nb_payes'],
            'nb_impayes'   => max(0, $nb_impayes_mois),
            // Clés attendues par la vue admin/dashboard.blade.php
            'attendu'      => (float) $montant_du_mois,
            'encaisse'     => $statsMois['loyers'],
            'a_recouvrer'  => max(0, (float) $montant_du_mois - $statsMois['loyers']),
        ];

        // ── Onboarding — CORRIGÉ : pas de checkOnboarding() inexistant ────
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

    // ─────────────────────────────────────────────────────────────────────
    // DASHBOARD PROPRIÉTAIRE
    // ─────────────────────────────────────────────────────────────────────

    public function proprietaire()
    {
        $this->authorize('isProprietaire');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // CORRIGÉ : loyer_mensuel → loyer_hors_charges (colonne réelle en base)
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

        // ── 1 seule requête pour tous les agrégats ─────────────────────────
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

        $stats = [
            'nb_biens'         => $biens->total(),
            'nb_biens_loues'   => Bien::where('proprietaire_id', $user->id)
                                      ->where('statut', 'loue')
                                      ->count(),
            'total_loyers'     => (float) ($aggrRaw->total_loyers    ?? 0),
            'total_net'        => (float) ($aggrRaw->total_net        ?? 0),
            'total_commission' => (float) ($aggrRaw->total_commission ?? 0),
            'nb_paiements'     => (int)   ($aggrRaw->nb_paiements     ?? 0),
            'dernier_paiement' => $dernierPaiement,
            'caution'          => (float) $cautionTotal,
        ];

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
            ->select([
                'id', 'bien_id', 'locataire_id',
                'loyer_contractuel', 'loyer_nu', 'charges_mensuelles',
                'date_debut', 'date_fin', 'statut', 'caution',
                'type_bail', 'reference_bail',
            ])
            ->first();

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
                 $currentAgency = $user->agency;
        return view('locataire.dashboard', compact(
            'contrat', 'paiements', 'dernierPaiement', 'prochainePeriode', 'stats'
        ));
    }
}