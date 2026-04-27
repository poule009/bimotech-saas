<?php

namespace App\Services;

use App\Enums\BienStatut;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * BailleurPortfolioService — Agrégation du portefeuille bailleur.
 *
 * Centralise la logique de chargement et d'agrégation des données
 * bailleur, extraite de BailleurController pour respecter le SRP.
 *
 * Toutes les méthodes garantissent l'isolation multi-tenant via agencyId.
 */
class BailleurPortfolioService
{
    // ─────────────────────────────────────────────────────────────────────
    // INDEX — Résumé de tous les bailleurs d'une agence
    // 5 requêtes SQL au total, quelle que soit la taille du portefeuille.
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return Collection<int, array> Tableau de résumés par bailleur.
     */
    public function getPortfolioIndex(int $agencyId): Collection
    {
        // 1 — IDs des propriétaires ayant des biens dans cette agence
        $proprietaireIds = Bien::where('agency_id', $agencyId)
            ->distinct()
            ->pluck('proprietaire_id');

        if ($proprietaireIds->isEmpty()) {
            return collect();
        }

        // 2 — Tous les biens de ces propriétaires
        $biensParProprietaire = Bien::where('agency_id', $agencyId)
            ->whereIn('proprietaire_id', $proprietaireIds)
            ->select(['id', 'proprietaire_id', 'statut'])
            ->withCount([
                'contrats as contrats_actifs_count' => fn($q) => $q->where('statut', 'actif'),
            ])
            ->get()
            ->groupBy('proprietaire_id');

        // 3 — Tous les contrats (id + bien_id) en une seule requête
        $tousLesBienIds  = $biensParProprietaire->flatten()->pluck('id');
        $contratsParBien = Contrat::whereIn('bien_id', $tousLesBienIds)
            ->select(['id', 'bien_id'])
            ->get()
            ->groupBy('bien_id');

        $tousLesContratIds = $contratsParBien->flatten()->pluck('id');

        // 4 — Tous les paiements + dépenses de l'année courante
        $paiementsParContrat = collect();
        if ($tousLesContratIds->isNotEmpty()) {
            $paiementsParContrat = Paiement::where('agency_id', $agencyId)
                ->whereIn('contrat_id', $tousLesContratIds)
                ->where('statut', 'valide')
                ->whereYear('periode', now()->year)
                ->select(['id', 'contrat_id', 'montant_encaisse', 'commission_ttc'])
                ->with(['depenses:id,paiement_id,montant'])
                ->get()
                ->groupBy('contrat_id');
        }

        // 5 — Utilisateurs + profil Proprietaire (agrégation 100% en PHP)
        return User::whereIn('id', $proprietaireIds)
            ->where('role', 'proprietaire')
            ->with('proprietaire')
            ->orderBy('name')
            ->get()
            ->map(fn(User $user) => $this->buildResume(
                $user,
                $biensParProprietaire,
                $contratsParBien,
                $paiementsParContrat,
            ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // SHOW — Détail financier d'un bailleur pour une période donnée
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return array{
     *   biens: Collection,
     *   paiements: Collection,
     *   dashboard: array,
     *   anneesDisponibles: Collection,
     * }
     */
    public function getPortfolioDetail(
        int $userId,
        int $agencyId,
        int $annee,
        ?string $mois = null,
    ): array {
        $biens = Bien::where('agency_id', $agencyId)
            ->where('proprietaire_id', $userId)
            ->with(['contratActif.locataire'])
            ->orderBy('reference')
            ->get();

        if ($biens->isEmpty()) {
            abort(403, 'Ce propriétaire n\'a aucun bien géré par votre agence.');
        }

        $bienIds    = $biens->pluck('id');
        $contratIds = Contrat::whereIn('bien_id', $bienIds)->pluck('id');

        $query = Paiement::where('agency_id', $agencyId)
            ->whereIn('contrat_id', $contratIds)
            ->where('statut', 'valide')
            ->whereYear('periode', $annee)
            ->with([
                'depenses',
                'contrat:id,bien_id,reference_bail,type_bail',
                'contrat.bien:id,reference,adresse,ville',
            ])
            ->orderByDesc('periode');

        if ($mois) {
            $query->whereMonth('periode', (int) $mois);
        }

        $paiements = $query->get();

        $dashboard = $this->buildDashboard($biens, $paiements);

        $anneesDisponibles = Paiement::where('agency_id', $agencyId)
            ->whereIn('contrat_id', $contratIds)
            ->selectRaw('YEAR(periode) as annee')
            ->distinct()
            ->orderByDesc('annee')
            ->pluck('annee');

        return compact('biens', 'paiements', 'dashboard', 'anneesDisponibles');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers privés
    // ─────────────────────────────────────────────────────────────────────

    private function buildResume(
        User $user,
        Collection $biensParProprietaire,
        Collection $contratsParBien,
        Collection $paiementsParContrat,
    ): array {
        $biens   = $biensParProprietaire->get($user->id, collect());
        $bienIds = $biens->pluck('id');

        $paiements = $bienIds->flatMap(function ($bienId) use ($contratsParBien, $paiementsParContrat) {
            $contratIds = $contratsParBien->get($bienId, collect())->pluck('id');
            return $contratIds->flatMap(fn($cid) => $paiementsParContrat->get($cid, collect()));
        });

        $totalLoyers      = (float) $paiements->sum('montant_encaisse');
        $totalCommissions = (float) $paiements->sum('commission_ttc');
        $totalBrs         = (float) $paiements->sum('brs_amount');
        $totalDepenses    = (float) $paiements->flatMap->depenses->sum('montant');

        return [
            'user'              => $user,
            'nb_biens'          => $biens->count(),
            'nb_biens_loues'    => $biens->where('statut', BienStatut::Loue->value)->count(),
            'total_loyers'      => $totalLoyers,
            'total_commissions' => $totalCommissions,
            'total_brs'         => $totalBrs,
            'total_depenses'    => $totalDepenses,
            'net_final'         => round($totalLoyers - $totalCommissions - $totalBrs - $totalDepenses, 2),
            'nb_paiements'      => $paiements->count(),
        ];
    }

    private function buildDashboard(Collection $biens, Collection $paiements): array
    {
        $totalLoyers      = (float) $paiements->sum('montant_encaisse');
        $totalCommissions = (float) $paiements->sum('commission_ttc');
        $totalBrs         = (float) $paiements->sum('brs_amount');
        $totalDgid        = (float) $paiements->sum('dgid_total');
        $totalDepenses    = (float) $paiements->flatMap->depenses->sum('montant');

        return [
            'total_loyers'      => $totalLoyers,
            'total_commissions' => $totalCommissions,
            'total_brs'         => $totalBrs,
            'total_dgid'        => $totalDgid,
            'total_depenses'    => $totalDepenses,
            'net_final'         => round($totalLoyers - $totalCommissions - $totalBrs - $totalDepenses, 2),
            'nb_paiements'      => $paiements->count(),
            'nb_biens'          => $biens->count(),
            'nb_biens_loues'    => $biens->where('statut', BienStatut::Loue->value)->count(),
        ];
    }
}
