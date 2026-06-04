<?php

namespace App\Services;

use App\Models\ChargeAgence;
use App\Models\DepenseGestion;
use App\Models\Paiement;
use App\Models\ReversementProprietaire;
use App\Models\TvaDeclaration;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ComptabiliteService
{
    /**
     * Compte de résultat de l'agence.
     * Revenus = commissions + frais d'entrée. Charges = charges opérationnelles saisies.
     */
    public function compteResultat(int $agencyId, int $annee, ?int $mois = null): array
    {
        $query = Paiement::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereYear('date_paiement', $annee);

        if ($mois) {
            $query->whereMonth('date_paiement', $mois);
        }

        $revenus = $query->selectRaw('
            COALESCE(SUM(commission_agence), 0)    AS commissions_ht,
            COALESCE(SUM(tva_commission), 0)       AS tva_commissions,
            COALESCE(SUM(commission_ttc), 0)       AS commissions_ttc,
            COALESCE(SUM(frais_agence_ht), 0)      AS frais_entree_ht,
            COALESCE(SUM(tva_frais_agence), 0)     AS tva_frais_entree,
            COALESCE(SUM(frais_agence_ttc), 0)     AS frais_entree_ttc,
            COUNT(*)                                AS nb_paiements
        ')->first();

        $chargesQuery = ChargeAgence::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->whereYear('date_charge', $annee);

        if ($mois) {
            $chargesQuery->whereMonth('date_charge', $mois);
        }

        $chargesTotal = (float) $chargesQuery->sum('montant');

        $chargesParCategorie = $chargesQuery->select('categorie', DB::raw('SUM(montant) as total'))
            ->groupBy('categorie')
            ->get()
            ->mapWithKeys(fn($r) => [$r->categorie => (float) $r->total])
            ->toArray();

        $revenusTotal = (float) $revenus->commissions_ht + (float) $revenus->frais_entree_ht;

        // ── Charges fiscales depuis le module fiscal ──────────────────────
        // TVA nette due = TVA collectée - TVA déductible (ce que l'agence verse à la DGI)
        $tvaQuery = TvaDeclaration::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('annee', $annee);

        if ($mois) {
            $tvaQuery->where('mois', $mois);
        }

        $tvaNetteDue     = (float) $tvaQuery->sum('tva_nette_due');
        $chargesTotal    = $chargesTotal + $tvaNetteDue;
        $resultatNet     = $revenusTotal - $chargesTotal;

        // Détail mensuel si vue annuelle
        $parMois = [];
        if (! $mois) {
            $revenusMois = Paiement::withoutGlobalScopes()
                ->where('agency_id', $agencyId)
                ->where('statut', 'valide')
                ->whereYear('date_paiement', $annee)
                ->selectRaw("DATE_FORMAT(date_paiement, '%Y-%m') as mois,
                    COALESCE(SUM(commission_agence), 0) + COALESCE(SUM(frais_agence_ht), 0) as revenus")
                ->groupBy('mois')
                ->get()
                ->pluck('revenus', 'mois');

            $chargesMois = ChargeAgence::withoutGlobalScopes()
                ->where('agency_id', $agencyId)
                ->whereYear('date_charge', $annee)
                ->selectRaw("periode as mois, SUM(montant) as charges")
                ->groupBy('periode')
                ->get()
                ->pluck('charges', 'mois');

            for ($m = 1; $m <= 12; $m++) {
                $cle = $annee . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
                $r   = (float) ($revenusMois[$cle] ?? 0);
                $c   = (float) ($chargesMois[$cle] ?? 0);
                $parMois[$cle] = ['revenus' => $r, 'charges' => $c, 'resultat' => $r - $c];
            }
        }

        return [
            'commissions_ht'       => (float) $revenus->commissions_ht,
            'tva_commissions'      => (float) $revenus->tva_commissions,
            'commissions_ttc'      => (float) $revenus->commissions_ttc,
            'frais_entree_ht'      => (float) $revenus->frais_entree_ht,
            'tva_frais_entree'     => (float) $revenus->tva_frais_entree,
            'frais_entree_ttc'     => (float) $revenus->frais_entree_ttc,
            'revenus_total_ht'     => $revenusTotal,
            'charges_total'        => $chargesTotal,
            'charges_par_categorie'=> $chargesParCategorie,
            'tva_nette_due'        => $tvaNetteDue,
            'resultat_net'         => $resultatNet,
            'nb_paiements'         => (int) $revenus->nb_paiements,
            'par_mois'             => $parMois,
        ];
    }

    /**
     * Compte mandant d'un propriétaire.
     * Ce que l'agence lui doit : loyers - commission - BRS - dépenses avancées - reversements déjà effectués.
     */
    public function compteMandant(int $agencyId, int $proprietaireId, ?string $periode = null): array
    {
        $paiementsQuery = Paiement::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereHas('contrat.bien', fn($q) => $q->where('proprietaire_id', $proprietaireId))
            ->with(['depenses', 'contrat.bien']);

        if ($periode) {
            [$annee, $mois] = explode('-', $periode);
            $paiementsQuery->whereYear('date_paiement', $annee)
                           ->whereMonth('date_paiement', $mois);
        }

        $paiements = $paiementsQuery->get();

        $loyersEncaisses    = $paiements->sum('montant_encaisse');
        $commissionsDeduites= $paiements->sum('commission_ttc');
        $brsRetenu          = $paiements->sum('brs_amount');
        $depensesAvancees   = $paiements->sum(fn($p) => $p->depenses->sum('montant'));
        $netDu              = $paiements->sum('net_final_bailleur');

        $reversementsQuery = ReversementProprietaire::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('proprietaire_id', $proprietaireId);

        if ($periode) {
            $reversementsQuery->where(function ($q) use ($periode) {
                $q->where('periode_debut', '<=', $periode)
                  ->where('periode_fin', '>=', $periode);
            });
        }

        $reversementsEffectues = (float) $reversementsQuery->sum('montant');
        $soldeRestant          = $netDu - $reversementsEffectues;

        return [
            'loyers_encaisses'       => (float) $loyersEncaisses,
            'commissions_deduites'   => (float) $commissionsDeduites,
            'brs_retenu'             => (float) $brsRetenu,
            'depenses_avancees'      => (float) $depensesAvancees,
            'net_du'                 => (float) $netDu,
            'reversements_effectues' => $reversementsEffectues,
            'solde_restant'          => $soldeRestant,
            'paiements'              => $paiements,
            'nb_biens'               => $paiements->pluck('contrat.bien_id')->unique()->count(),
        ];
    }

    /**
     * Données du graphe revenus/charges sur les 6 derniers mois.
     * 2 requêtes au lieu de 18 (vs 6 appels à compteResultat).
     */
    public function grapheSixMois(int $agencyId): \Illuminate\Support\Collection
    {
        $debut = now()->subMonths(5)->startOfMonth();

        $revenusMois = DB::table('paiements')
            ->where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->where('date_paiement', '>=', $debut)
            ->selectRaw("DATE_FORMAT(date_paiement, '%Y-%m') as mois,
                COALESCE(SUM(commission_agence), 0) + COALESCE(SUM(frais_agence_ht), 0) as revenus")
            ->groupBy('mois')
            ->get()
            ->pluck('revenus', 'mois');

        $chargesMois = DB::table('charges_agence')
            ->where('agency_id', $agencyId)
            ->where('date_charge', '>=', $debut)
            ->selectRaw('periode as mois, COALESCE(SUM(montant), 0) as charges')
            ->groupBy('periode')
            ->get()
            ->pluck('charges', 'mois');

        return collect(range(5, 0))->map(function ($i) use ($revenusMois, $chargesMois) {
            $d   = now()->subMonths($i);
            $cle = $d->format('Y-m');
            $r   = (float) ($revenusMois[$cle] ?? 0);
            $c   = (float) ($chargesMois[$cle] ?? 0);

            return [
                'mois'     => $d->locale('fr')->translatedFormat('M'),
                'revenus'  => $r,
                'charges'  => $c,
                'resultat' => $r - $c,
            ];
        });
    }

    /**
     * Soldes mandants de tous les propriétaires de l'agence.
     * 1 requête consolidée au lieu de 3 séparées.
     */
    public function soldesMandants(int $agencyId): \Illuminate\Support\Collection
    {
        $rows = DB::select("
            SELECT
                b.proprietaire_id,
                COALESCE(SUM(p.net_a_verser_proprietaire), 0)
                    - COALESCE(MAX(dep.total_depenses), 0)   AS net_du,
                COALESCE(MAX(rev.total_reverse), 0)           AS reverse_effectue
            FROM paiements p
            JOIN contrats c ON c.id = p.contrat_id
            JOIN biens b    ON b.id = c.bien_id
            LEFT JOIN (
                SELECT b2.proprietaire_id, SUM(d.montant) AS total_depenses
                FROM depenses_gestion d
                JOIN paiements p2  ON p2.id = d.paiement_id
                JOIN contrats c2   ON c2.id = p2.contrat_id
                JOIN biens b2      ON b2.id = c2.bien_id
                WHERE d.agency_id = ?
                GROUP BY b2.proprietaire_id
            ) dep ON dep.proprietaire_id = b.proprietaire_id
            LEFT JOIN (
                SELECT proprietaire_id, SUM(montant) AS total_reverse
                FROM reversements_proprietaires
                WHERE agency_id = ?
                GROUP BY proprietaire_id
            ) rev ON rev.proprietaire_id = b.proprietaire_id
            WHERE p.agency_id = ? AND p.statut = 'valide'
            GROUP BY b.proprietaire_id
        ", [$agencyId, $agencyId, $agencyId]);

        return collect($rows)
            ->map(fn($r) => [
                'proprietaire_id' => $r->proprietaire_id,
                'net_du'          => (float) $r->net_du,
                'reverse'         => (float) $r->reverse_effectue,
                'solde'           => (float) $r->net_du - (float) $r->reverse_effectue,
            ])
            ->filter(fn($s) => $s['solde'] != 0);
    }

    /**
     * Tableau de trésorerie du mois.
     */
    public function tresorerie(int $agencyId, int $annee, int $mois): array
    {
        $debut = \Carbon\Carbon::create($annee, $mois, 1)->startOfMonth();
        $fin   = $debut->copy()->endOfMonth();

        $encaisse = (float) Paiement::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->whereBetween('date_paiement', [$debut, $fin])
            ->sum('montant_encaisse');

        $reverseProprietaires = (float) ReversementProprietaire::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->whereBetween('date_reversement', [$debut, $fin])
            ->sum('montant');

        $chargesAgence = (float) ChargeAgence::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->whereBetween('date_charge', [$debut, $fin])
            ->sum('montant');

        $soldeMandant = $this->soldesMandants($agencyId)->sum('solde');
        $disponible   = $encaisse - $reverseProprietaires - $chargesAgence;

        return [
            'encaisse_locataires'     => $encaisse,
            'reverse_proprietaires'   => $reverseProprietaires,
            'charges_agence'          => $chargesAgence,
            'solde_mandant_total'     => (float) $soldeMandant,
            'disponible_agence'       => $disponible,
            'mois'                    => $debut->locale('fr')->translatedFormat('F Y'),
        ];
    }
}
