<?php

namespace App\Services;

use App\Models\Contrat;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Support\Collection;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * PaiementService — Orchestration des paiements de loyer.
 *
 * Responsabilités :
 *  - Enregistrer un paiement mensuel depuis un formulaire
 *  - Identifier les impayés
 *  - Préparer les données pour le rapport financier
 */
class PaiementService
{
    public function __construct(
        private readonly FiscalService $fiscalService
    ) {}

    // ─── Enregistrement d'un paiement ──────────────────────────────────────────

    /**
     * Enregistre un paiement mensuel avec calcul fiscal complet.
     * Toute la logique de calcul est déléguée à FiscalService via FiscalContext.
     *
     * @param  Contrat $contrat   Contrat chargé avec bien + locataire
     * @param  array   $data      Données du formulaire (periode, mode_paiement, etc.)
     * @return Paiement
     */
    public function enregistrerPaiement(Contrat $contrat, array $data): Paiement
    {
        // Charger les relations nécessaires au calcul fiscal
        $contrat->loadMissing([
            'bien',
            'locataire.locataire',
        ]);

        // Construire le contexte fiscal depuis le contrat
        $ctx    = \App\Services\FiscalContext::fromContrat($contrat);
        $result = FiscalService::calculer($ctx);

        return DB::transaction(function () use ($contrat, $data, $result) {
            $periode = Carbon::parse($data['periode'])->startOfMonth();

            $paiement = Paiement::create([
                'agency_id'   => $contrat->agency_id,
                'contrat_id'  => $contrat->id,
                'periode'     => $periode->toDateString(),
                'date_paiement' => $data['date_paiement'] ?? now()->toDateString(),
                'mode_paiement' => $data['mode_paiement'],
                'statut'      => 'valide',

                // Ventilation loyer
                'loyer_nu'         => $result->loyerHt,
                'loyer_ht'         => $result->loyerHt,
                'tva_loyer'        => $result->tvaLoyer,
                'loyer_ttc'        => $result->loyerTtc,
                'charges_amount'   => $result->chargesAmount,
                'tom_amount'       => $result->tomAmount,
                'montant_encaisse' => $result->montantEncaisse,

                // Commission
                'taux_commission_applique' => $ctx->tauxCommission,
                'commission_agence'        => $result->commissionHt,
                'tva_commission'           => $result->tvaCommission,
                'commission_ttc'           => $result->commissionTtc,

                // Nets
                'net_proprietaire'          => $result->netProprietaire,
                'brs_amount'                => $result->brsAmount,
                'taux_brs_applique'         => $result->tauxBrsApplique,
                'net_a_verser_proprietaire' => $result->netAVerserProprietaire,

                // Snapshot fiscal immuable
                'regime_fiscal_snapshot' => $result->toArray(),

                // Référence
                'reference_paiement' => $data['reference_paiement'] ?? $this->genererReference($contrat),
                'reference_bail'     => $contrat->reference_bail_affichee,

                'notes' => $data['notes'] ?? null,
            ]);

            // Mettre à jour le statut du bien → loué si ce n'est pas déjà fait
            if ($contrat->bien && $contrat->bien->statut === 'disponible') {
                $contrat->bien->update(['statut' => 'loue']);
            }

            return $paiement;
        });
    }

    // ─── Impayés ───────────────────────────────────────────────────────────────

    /**
     * Retourne les contrats actifs sans paiement validé pour une période donnée.
     */
    public function getImpayes(int $agencyId, ?Carbon $periode = null): Collection
    {
        $periode ??= now()->startOfMonth();

        // Contrats actifs de l'agence
        $contrats = Contrat::where('agency_id', $agencyId)
            ->where('statut', 'actif')
            ->with([
                'bien:id,agency_id,reference,adresse,ville,type',
                'locataire:id,name,email,telephone',
            ])
            ->get();

        // IDs des contrats ayant un paiement valide pour cette période
        $payes = Paiement::where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->where('periode', $periode->toDateString())
            ->pluck('contrat_id')
            ->toArray();

        return $contrats->filter(fn($c) => ! in_array($c->id, $payes))->values();
    }

    /**
     * Retourne les contrats actifs AVEC paiement validé pour une période donnée.
     */
    public function getPaiesPourPeriode(int $agencyId, Carbon $periode): Collection
    {
        return Paiement::where('agency_id', $agencyId)
            ->where('statut', 'valide')
            ->where('periode', $periode->toDateString())
            ->with([
                'contrat:id,bien_id,locataire_id',
                'contrat.bien:id,reference,adresse,ville',
                'contrat.locataire:id,name,telephone,email',
            ])
            ->get();
    }

    // ─── Utilitaires ───────────────────────────────────────────────────────────

    /**
     * Génère une référence unique pour un paiement.
     * Format : PAY-{CONTRAT_ID}-{YYYYMM}-{RANDOM}
     */
    private function genererReference(Contrat $contrat): string
    {
        return sprintf(
            'PAY-%s-%s-%s',
            str_pad($contrat->id, 5, '0', STR_PAD_LEFT),
            now()->format('Ym'),
            strtoupper(substr(md5(uniqid()), 0, 4))
        );
    }

    /**
     * Dernière période payée pour un contrat.
     * Utilisée pour pré-remplir le formulaire de paiement.
     */
    public function dernierePeriode(Contrat $contrat): ?Carbon
    {
        $dernier = Paiement::where('contrat_id', $contrat->id)
            ->where('statut', 'valide')
            ->orderByDesc('periode')
            ->value('periode');

        return $dernier ? Carbon::parse($dernier) : null;
    }

    /**
     * Prochaine période à payer pour un contrat.
     */
    public function prochainePeriode(Contrat $contrat): Carbon
    {
        $derniere = $this->dernierePeriode($contrat);

        if ($derniere) {
            return $derniere->addMonth()->startOfMonth();
        }

        return Carbon::parse($contrat->date_debut)->startOfMonth();
    }
}