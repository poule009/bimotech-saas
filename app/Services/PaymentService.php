<?php

namespace App\Services;

use App\Models\Contrat;
use App\Models\Paiement;
use App\Services\FiscalService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * PaiementService — Orchestration des paiements de loyer.
 *
 * Responsabilités :
 *  - Générer le calendrier des échéances d'un bail
 *  - Enregistrer un paiement et calculer les retards
 *  - Identifier les impayés et les relances à effectuer
 *  - Préparer les données pour le rapport financier
 *
 * Ce service ne génère pas les quittances → voir QuittanceService.
 * Ce service ne calcule pas les montants fiscaux → voir FiscalService.
 */
class PaiementService
{
    public function __construct(
        private readonly FiscalService $fiscalService
    ) {}

    // ─── Génération des échéances ───────────────────────────────────────────

    /**
     * Génère le calendrier complet des échéances pour un contrat.
     * Appelé à la création du contrat pour matérialiser toutes les mensualités.
     *
     * @param  Contrat $contrat
     * @return Collection<Paiement>  Les paiements créés en base
     */
    public function genererEcheances(Contrat $contrat): Collection
    {
        $decomposition = $this->fiscalService->calculerDecompositionLoyer(
            $contrat->loyer_hors_charges,
            $contrat->charges,
            $contrat->tom_taux ?? FiscalService::TOM_TAUX_DEFAUT,
            $contrat->commission_taux ?? FiscalService::COMMISSION_TAUX
        );

        $echeances = collect();
        $dateDebut = Carbon::parse($contrat->date_debut);
        $dateFin   = Carbon::parse($contrat->date_fin);

        // Itération mois par mois sur la durée du bail
        $dateCourante = $dateDebut->copy()->startOfMonth();

        while ($dateCourante->lte($dateFin)) {
            $echeance = Paiement::create([
                'agency_id'          => $contrat->agency_id,
                'contrat_id'         => $contrat->id,
                'mois_concerne'      => $dateCourante->format('Y-m'),
                'date_echeance'      => $dateCourante->copy()->day($contrat->jour_echeance ?? 5),
                'loyer_hors_charges' => $decomposition['loyer_hors_charges'],
                'charges'            => $decomposition['charges'],
                'commission_ht'      => $decomposition['commission_ht'],
                'tva_montant'        => $decomposition['tva_montant'],
                'commission_ttc'     => $decomposition['commission_ttc'],
                'tom_montant'        => $decomposition['tom_montant'],
                'net_proprietaire'   => $decomposition['net_proprietaire'],
                'montant_total'      => $decomposition['loyer_brut'],
                'statut'             => 'en_attente',
                'penalite'           => 0,
            ]);

            $echeances->push($echeance);
            $dateCourante->addMonth();
        }

        return $echeances;
    }

    // ─── Enregistrement d'un paiement ──────────────────────────────────────

    /**
     * Enregistre le paiement effectif d'une échéance.
     * Aucune pénalité de retard n'est appliquée : le montant reçu est enregistré tel quel.
     *
     * @param  Paiement $paiement      L'échéance à solder
     * @param  float    $montantRecu   Montant réellement reçu
     * @param  string   $dateReglement Date du règlement (format Y-m-d)
     * @param  string   $modePaiement  especes | virement | wave | orange_money | cheque
     * @return Paiement
     */
    public function enregistrerReglement(
        Paiement $paiement,
        float    $montantRecu,
        string   $dateReglement,
        string   $modePaiement = 'especes'
    ): Paiement {
        if ($paiement->statut === 'valide') {
            throw new \LogicException("L'échéance #{$paiement->id} est déjà validée.");
        }

        $dateRegl = Carbon::parse($dateReglement);
        $retard   = $dateRegl->gt(Carbon::parse($paiement->date_echeance));

        DB::transaction(function () use ($paiement, $montantRecu, $dateRegl, $modePaiement, $retard) {
            $paiement->update([
                'montant_recu'   => round($montantRecu),
                'date_reglement' => $dateRegl->toDateString(),
                'mode_paiement'  => $modePaiement,
                'jours_retard'   => $retard ? $dateRegl->diffInDays(Carbon::parse($paiement->date_echeance)) : 0,
                'statut'         => 'valide',
                'valide_le'      => now(),
                'valide_par'     => Auth::id(),
            ]);
        });

        return $paiement->fresh();
    }

    // ─── Détection des impayés ──────────────────────────────────────────────

    /**
     * Retourne les paiements en retard pour une agence.
     * Un paiement est en retard si date_echeance est dépassée et statut = 'en_attente'.
     *
     * @param  int  $agencyId
     * @param  int  $joursGrace     Nombre de jours de grâce avant d'être "en retard"
     * @return Collection<Paiement>
     */
    public function getImpayes(int $agencyId, int $joursGrace = 3): Collection
    {
        return Paiement::with(['contrat.locataire', 'contrat.bien'])
            ->where('agency_id', $agencyId)
            ->where('statut', 'en_attente')
            ->where('date_echeance', '<', now()->subDays($joursGrace)->toDateString())
            ->orderBy('date_echeance')
            ->get();
    }

    /**
     * Retourne les échéances à venir dans les N prochains jours.
     * Utile pour les alertes proactives avant retard.
     *
     * @param  int $agencyId
     * @param  int $joursAvant
     * @return Collection<Paiement>
     */
    public function getEcheancesProchaines(int $agencyId, int $joursAvant = 7): Collection
    {
        return Paiement::with(['contrat.locataire', 'contrat.bien'])
            ->where('agency_id', $agencyId)
            ->where('statut', 'en_attente')
            ->whereBetween('date_echeance', [
                now()->toDateString(),
                now()->addDays($joursAvant)->toDateString(),
            ])
            ->orderBy('date_echeance')
            ->get();
    }

    // ─── Rapports financiers ────────────────────────────────────────────────

    /**
     * Calcule le tableau de bord financier mensuel pour une agence.
     *
     * @param  int    $agencyId
     * @param  int    $annee
     * @param  int    $mois
     * @return array
     */
    public function getTableauDeBordMensuel(int $agencyId, int $annee, int $mois): array
    {
        $periode = sprintf('%04d-%02d', $annee, $mois);

        $paiements = Paiement::where('agency_id', $agencyId)
            ->where('mois_concerne', $periode)
            ->get();

        $valides    = $paiements->where('statut', 'valide');
        $enAttente  = $paiements->where('statut', 'en_attente');

        return [
            'periode'               => $periode,
            'total_attendu'         => $paiements->sum('montant_total'),
            'total_recu'            => $valides->sum('montant_recu'),
            'total_commissions_ht'  => $valides->sum('commission_ht'),
            'total_tva'             => $valides->sum('tva_montant'),
            'total_tom'             => $valides->sum('tom_montant'),
            'total_net_proprietaires' => $valides->sum('net_proprietaire'),
            'nb_payes'              => $valides->count(),
            'nb_impayes'            => $enAttente->count(),
            'taux_recouvrement' => $paiements->count() > 0
                ? round(($valides->count() / $paiements->count()) * 100, 1)
                : 0,
        ];
    }
}