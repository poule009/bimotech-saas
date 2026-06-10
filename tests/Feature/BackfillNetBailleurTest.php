<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Services\ComptabiliteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Garde-fou C2 (audit) — backfill de paiements.montant_net_bailleur.
 *
 * Reproduit le scénario d'un paiement « historique » écrit avant la migration
 * 2026_04_12_000002 : montant_net_bailleur resté à 0 (défaut) alors que
 * net_a_verser_proprietaire porte le vrai montant. Vérifie que la logique de
 * backfill restaure la valeur attendue, caution comprise selon la politique du
 * contrat, et que ComptabiliteService::soldesMandants() compte alors le bon solde.
 */
class BackfillNetBailleurTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Réexécute la requête de backfill (identique à la migration
     * 2026_06_10_120000) sur les lignes courantes.
     */
    private function runBackfill(): void
    {
        DB::statement("
            UPDATE paiements p
            JOIN contrats c ON c.id = p.contrat_id
            SET p.montant_net_bailleur =
                    p.net_a_verser_proprietaire
                  + CASE
                        WHEN COALESCE(c.caution_gardee_par_agence, 0) = 0
                            THEN COALESCE(p.caution_montant, 0)
                        ELSE 0
                    END
            WHERE p.montant_net_bailleur = 0
              AND p.net_a_verser_proprietaire <> 0
        ");
    }

    private function paiementHistorique(Contrat $contrat, float $netAVerser, float $caution): Paiement
    {
        $paiement = Paiement::factory()->create([
            'contrat_id' => $contrat->id,
            'agency_id'  => $contrat->agency_id,
            'statut'     => 'valide',
        ]);

        // Simule l'état « pré-backfill » directement en base (bypass fillable).
        DB::table('paiements')->where('id', $paiement->id)->update([
            'net_a_verser_proprietaire' => $netAVerser,
            'caution_montant'           => $caution,
            'montant_net_bailleur'      => 0, // défaut hérité de la migration
        ]);

        return $paiement->refresh();
    }

    #[Test]
    public function backfill_inclut_la_caution_quand_elle_est_remise_au_bailleur()
    {
        $bien    = Bien::factory()->create();
        $contrat = Contrat::factory()->create([
            'bien_id'                   => $bien->id,
            'agency_id'                 => $bien->agency_id,
            'caution_gardee_par_agence' => false, // caution remise au bailleur
        ]);

        $paiement = $this->paiementHistorique($contrat, netAVerser: 200000, caution: 400000);

        $this->assertEquals(0, (float) $paiement->montant_net_bailleur);

        $this->runBackfill();

        // 200 000 (net après BRS) + 400 000 (caution remise) = 600 000
        $this->assertEquals(600000, (float) $paiement->refresh()->montant_net_bailleur);
    }

    #[Test]
    public function backfill_exclut_la_caution_quand_lagence_la_garde()
    {
        $bien    = Bien::factory()->create();
        $contrat = Contrat::factory()->create([
            'bien_id'                   => $bien->id,
            'agency_id'                 => $bien->agency_id,
            'caution_gardee_par_agence' => true, // séquestre agence
        ]);

        $paiement = $this->paiementHistorique($contrat, netAVerser: 200000, caution: 400000);

        $this->runBackfill();

        // L'agence garde la caution → seul le net après BRS est dû au bailleur.
        $this->assertEquals(200000, (float) $paiement->refresh()->montant_net_bailleur);
    }

    #[Test]
    public function backfill_est_idempotent_et_solde_mandant_correct()
    {
        $bien    = Bien::factory()->create();
        $contrat = Contrat::factory()->create([
            'bien_id'                   => $bien->id,
            'agency_id'                 => $bien->agency_id,
            'caution_gardee_par_agence' => false,
        ]);

        $this->paiementHistorique($contrat, netAVerser: 200000, caution: 400000);

        $this->runBackfill();
        $premiere = (float) Paiement::where('contrat_id', $contrat->id)->value('montant_net_bailleur');

        // Rejouer ne doit pas re-compter (prédicat montant_net_bailleur = 0).
        $this->runBackfill();
        $seconde = (float) Paiement::where('contrat_id', $contrat->id)->value('montant_net_bailleur');

        $this->assertEquals($premiere, $seconde);
        $this->assertEquals(600000, $seconde);

        // Le solde mandant reflète bien la valeur backfillée (aucun reversement).
        $soldes = app(ComptabiliteService::class)->soldesMandants($bien->agency_id);
        $solde  = $soldes->firstWhere('proprietaire_id', $bien->proprietaire_id);

        $this->assertNotNull($solde, 'Le propriétaire doit apparaître dans les soldes mandants.');
        $this->assertEquals(600000, $solde['net_du']);
    }
}
