<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GenerateMonthlyRentTest — Tests de la commande rent:generate.
 *
 * Couvre :
 *  - Création correcte des paiements (statut unpaid, calcul fiscal)
 *  - Anti-doublon : un contrat déjà payé ce mois est ignoré
 *  - Isolation par période : seul le mois demandé est traité
 *  - Option --mois : génération sur un mois passé
 *  - Contrats résiliés ignorés
 *  - Snapshot fiscal persisté (regime_fiscal_snapshot non null)
 */
class GenerateMonthlyRentTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $this->agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helper
    // ────────────────────────────────────────────────────────────────────────

    private function creerContratActif(float $loyerNu = 200_000): Contrat
    {
        $proprio   = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $this->agency->id]);
        $locataire = User::factory()->create(['role' => 'locataire',    'agency_id' => $this->agency->id]);

        $bien = Bien::factory()->create([
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $proprio->id,
            'loyer_mensuel'   => $loyerNu,
            'taux_commission' => 10.0,
            'meuble'          => false,
            'statut'          => 'loue',
        ]);

        return Contrat::factory()->create([
            'agency_id'    => $this->agency->id,
            'bien_id'      => $bien->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'actif',
            'loyer_nu'     => $loyerNu,
            'type_bail'    => 'habitation',
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Création de paiements
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function la_commande_cree_un_paiement_par_contrat_actif(): void
    {
        $contrat = $this->creerContratActif(200_000);

        $this->artisan('rent:generate')->assertSuccessful();

        $this->assertDatabaseHas('paiements', [
            'contrat_id' => $contrat->id,
            'statut'     => 'unpaid',
        ]);
    }

    #[Test]
    public function le_paiement_a_le_bon_montant_fiscal(): void
    {
        // Loyer 200 000 FCFA, bail habitation (exonéré TVA), commission 10%
        // commission HT = 200 000 × 10% = 20 000
        // TVA commission = 20 000 × 18% = 3 600
        // commission TTC = 23 600
        // net proprio    = 200 000 - 23 600 = 176 400
        $this->creerContratActif(200_000);

        $this->artisan('rent:generate')->assertSuccessful();

        $paiement = Paiement::where('statut', 'unpaid')->first();
        $this->assertNotNull($paiement);
        $this->assertEquals(200_000.0, (float) $paiement->montant_encaisse);
        $this->assertEqualsWithDelta(20_000.0,  (float) $paiement->commission_agence, 1.0);
        $this->assertEqualsWithDelta(3_600.0,   (float) $paiement->tva_commission,    1.0);
        $this->assertEqualsWithDelta(176_400.0, (float) $paiement->net_proprietaire,  1.0);
    }

    #[Test]
    public function le_snapshot_fiscal_est_persiste(): void
    {
        $this->creerContratActif(200_000);

        $this->artisan('rent:generate')->assertSuccessful();

        $paiement = Paiement::where('statut', 'unpaid')->first();
        $this->assertNotNull($paiement);
        $this->assertNotNull($paiement->regime_fiscal_snapshot, 'regime_fiscal_snapshot ne doit pas être null');
    }

    #[Test]
    public function plusieurs_contrats_creent_plusieurs_paiements(): void
    {
        $this->creerContratActif(200_000);
        $this->creerContratActif(350_000);
        $this->creerContratActif(150_000);

        $this->artisan('rent:generate')->assertSuccessful();

        $this->assertEquals(3, Paiement::where('statut', 'unpaid')->count());
    }

    // ════════════════════════════════════════════════════════════════════════
    // Anti-doublon
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function un_contrat_deja_paye_ce_mois_est_ignore(): void
    {
        $contrat = $this->creerContratActif(200_000);
        $periode = now()->startOfMonth()->toDateString();

        // Paiement déjà existant pour ce mois
        Paiement::create([
            'agency_id'                => $this->agency->id,
            'contrat_id'               => $contrat->id,
            'periode'                  => $periode,
            'date_paiement'            => $periode,
            'loyer_nu'                 => 200_000,
            'loyer_ht'                 => 200_000,
            'tva_loyer'                => 0,
            'loyer_ttc'                => 200_000,
            'charges_amount'           => 0,
            'tom_amount'               => 0,
            'montant_encaisse'         => 200_000,
            'taux_commission_applique' => 10.0,
            'commission_agence'        => 20_000,
            'tva_commission'           => 3_600,
            'commission_ttc'           => 23_600,
            'net_proprietaire'         => 176_400,
            'brs_amount'               => 0,
            'taux_brs_applique'        => 0,
            'net_a_verser_proprietaire'=> 176_400,
            'mode_paiement'            => 'virement',
            'statut'                   => 'valide',
            'reference_paiement'       => 'TEST-EXIST-001',
            'est_premier_paiement'     => false,
            'caution_percue'           => 0,
        ]);

        $this->artisan('rent:generate')->assertSuccessful();

        // Aucun nouveau paiement créé (le existant compte)
        $this->assertEquals(1, Paiement::where('contrat_id', $contrat->id)->count());
    }

    #[Test]
    public function un_paiement_annule_ne_bloque_pas_la_regeneration(): void
    {
        $contrat = $this->creerContratActif(200_000);
        $periode = now()->startOfMonth()->toDateString();

        // Paiement annulé — ne doit pas compter comme doublon
        Paiement::create([
            'agency_id'                => $this->agency->id,
            'contrat_id'               => $contrat->id,
            'periode'                  => $periode,
            'date_paiement'            => $periode,
            'loyer_nu'                 => 200_000,
            'loyer_ht'                 => 200_000,
            'tva_loyer'                => 0,
            'loyer_ttc'                => 200_000,
            'charges_amount'           => 0,
            'tom_amount'               => 0,
            'montant_encaisse'         => 200_000,
            'taux_commission_applique' => 10.0,
            'commission_agence'        => 20_000,
            'tva_commission'           => 3_600,
            'commission_ttc'           => 23_600,
            'net_proprietaire'         => 176_400,
            'brs_amount'               => 0,
            'taux_brs_applique'        => 0,
            'net_a_verser_proprietaire'=> 176_400,
            'mode_paiement'            => 'virement',
            'statut'                   => 'annule',
            'reference_paiement'       => 'TEST-ANNULE-001',
            'est_premier_paiement'     => false,
            'caution_percue'           => 0,
        ]);

        $this->artisan('rent:generate')->assertSuccessful();

        // Un nouveau paiement 'unpaid' doit avoir été créé
        $this->assertEquals(1, Paiement::where('contrat_id', $contrat->id)->where('statut', 'unpaid')->count());
    }

    // ════════════════════════════════════════════════════════════════════════
    // Option --mois
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function option_mois_genere_les_loyers_pour_le_mois_specifie(): void
    {
        $contrat = $this->creerContratActif(200_000);

        $moisPasse = now()->subMonth()->format('Y-m');

        $this->artisan('rent:generate', ['--mois' => $moisPasse])->assertSuccessful();

        $periodeAttendue = now()->subMonth()->startOfMonth()->toDateString();

        $this->assertDatabaseHas('paiements', [
            'contrat_id' => $contrat->id,
            'periode'    => $periodeAttendue,
            'statut'     => 'unpaid',
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Contrats résiliés ignorés
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function les_contrats_resilies_ne_sont_pas_traites(): void
    {
        $proprio   = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $this->agency->id]);
        $locataire = User::factory()->create(['role' => 'locataire',    'agency_id' => $this->agency->id]);
        $bien      = Bien::factory()->create(['agency_id' => $this->agency->id, 'proprietaire_id' => $proprio->id]);

        $contratResilié = Contrat::factory()->create([
            'agency_id'    => $this->agency->id,
            'bien_id'      => $bien->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'résilié',
            'loyer_nu'     => 200_000,
        ]);

        $this->artisan('rent:generate')->assertSuccessful();

        $this->assertEquals(0, Paiement::where('contrat_id', $contratResilié->id)->count());
    }

    #[Test]
    public function aucun_contrat_actif_retourne_zero_paiement(): void
    {
        $this->artisan('rent:generate')->assertSuccessful();

        $this->assertEquals(0, Paiement::count());
    }
}
