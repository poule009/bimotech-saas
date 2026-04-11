<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\BilanFiscalProprietaire;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Subscription;
use App\Models\User;
use App\Services\FiscalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * BilanFiscalTest — Tests du calcul et de l'affichage des bilans fiscaux annuels.
 *
 * Couvre :
 *  - calculerBilanAnnuel() : aggrégation des paiements depuis la DB
 *  - BilanFiscalController : accès, calcul, affichage, isolation agence
 *  - Sécurité multi-tenant : un admin ne voit pas les bilans d'une autre agence
 */
class BilanFiscalTest extends TestCase
{
    use RefreshDatabase;

    private User   $admin;
    private Agency $agency;
    private User   $proprio;

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

        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'agency_id' => $this->agency->id,
        ]);

        $this->proprio = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $this->agency->id,
        ]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────────────────

    private function creerPaiementPourProprio(User $proprio, float $loyerNu, int $annee = 2025): Paiement
    {
        $locataire = User::factory()->create([
            'role'      => 'locataire',
            'agency_id' => $this->agency->id,
        ]);

        $bien = Bien::factory()->create([
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $proprio->id,
            'loyer_mensuel'   => $loyerNu,
            'taux_commission' => 10.0,
        ]);

        $contrat = Contrat::factory()->create([
            'agency_id'    => $this->agency->id,
            'bien_id'      => $bien->id,
            'locataire_id' => $locataire->id,
            'loyer_nu'     => $loyerNu,
            'statut'       => 'actif',
        ]);

        $commissionHt  = round($loyerNu * 0.10, 2);
        $tvaCommission = round($commissionHt * 0.18, 2);
        $commissionTtc = $commissionHt + $tvaCommission;
        $netProprio    = $loyerNu - $commissionTtc;

        return Paiement::create([
            'agency_id'                => $this->agency->id,
            'contrat_id'               => $contrat->id,
            'periode'                  => "{$annee}-01-01",
            'date_paiement'            => "{$annee}-01-15",
            'loyer_nu'                 => $loyerNu,
            'loyer_ht'                 => $loyerNu,
            'tva_loyer'                => 0,
            'loyer_ttc'                => $loyerNu,
            'charges_amount'           => 0,
            'tom_amount'               => 0,
            'montant_encaisse'         => $loyerNu,
            'taux_commission_applique' => 10.0,
            'commission_agence'        => $commissionHt,
            'tva_commission'           => $tvaCommission,
            'commission_ttc'           => $commissionTtc,
            'net_proprietaire'         => $netProprio,
            'brs_amount'               => 0,
            'taux_brs_applique'        => 0,
            'net_a_verser_proprietaire'=> $netProprio,
            'mode_paiement'            => 'virement',
            'statut'                   => 'valide',
            'reference_paiement'       => 'TEST-' . uniqid(),
            'est_premier_paiement'     => false,
            'caution_percue'           => 0,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // calculerBilanAnnuel() — Aggrégation DB
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function bilan_annuel_est_calcule_correctement_depuis_les_paiements(): void
    {
        // 3 paiements de 300 000 F pour ce proprio en 2025
        $this->creerPaiementPourProprio($this->proprio, 300_000, 2025);
        $this->creerPaiementPourProprio($this->proprio, 300_000, 2025);
        $this->creerPaiementPourProprio($this->proprio, 300_000, 2025);

        $data = FiscalService::calculerBilanAnnuel(
            $this->proprio->id,
            2025,
            $this->agency->id
        );

        // Revenus bruts = 3 × 300 000 = 900 000 F
        $this->assertEquals(900_000.0, (float) $data['revenus_bruts_loyers']);
        $this->assertEquals(3, $data['nb_paiements']);

        // Abattement 30% = 270 000 F
        $this->assertEquals(270_000.0, (float) $data['abattement_forfaitaire_30']);

        // Base imposable = 630 000 F < 1 500 000 → IRPP = 0
        $this->assertEquals(630_000.0, (float) $data['base_imposable']);
        $this->assertEquals(0.0, (float) $data['irpp_estime']);

        // CFPB = 900 000 × 5% = 45 000 F
        $this->assertEquals(45_000.0, (float) $data['cfpb_estimee']);
    }

    #[Test]
    public function bilan_ne_compte_que_les_paiements_valides(): void
    {
        // 1 paiement valide + 1 paiement annulé
        $this->creerPaiementPourProprio($this->proprio, 200_000, 2025);

        $locataire = User::factory()->create(['role' => 'locataire', 'agency_id' => $this->agency->id]);
        $bien      = Bien::factory()->create(['agency_id' => $this->agency->id, 'proprietaire_id' => $this->proprio->id]);
        $contrat   = Contrat::factory()->create(['agency_id' => $this->agency->id, 'bien_id' => $bien->id, 'locataire_id' => $locataire->id]);

        Paiement::create([
            'agency_id'                => $this->agency->id,
            'contrat_id'               => $contrat->id,
            'periode'                  => '2025-02-01',
            'date_paiement'            => '2025-02-15',
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
            'mode_paiement'            => 'especes',
            'statut'                   => 'annule', // ← annulé, ne doit pas compter
            'reference_paiement'       => 'TEST-ANNULE-' . uniqid(),
            'est_premier_paiement'     => false,
            'caution_percue'           => 0,
        ]);

        $data = FiscalService::calculerBilanAnnuel($this->proprio->id, 2025, $this->agency->id);

        // Seul le paiement valide doit être compté
        $this->assertEquals(1, $data['nb_paiements']);
        $this->assertEquals(200_000.0, (float) $data['revenus_bruts_loyers']);
    }

    #[Test]
    public function bilan_ne_compte_que_les_paiements_de_lannee_demandee(): void
    {
        $this->creerPaiementPourProprio($this->proprio, 300_000, 2024); // année passée
        $this->creerPaiementPourProprio($this->proprio, 300_000, 2025); // année cible

        $data = FiscalService::calculerBilanAnnuel($this->proprio->id, 2025, $this->agency->id);

        $this->assertEquals(1, $data['nb_paiements']);
        $this->assertEquals(300_000.0, (float) $data['revenus_bruts_loyers']);
    }

    #[Test]
    public function bilan_vide_si_aucun_paiement(): void
    {
        $data = FiscalService::calculerBilanAnnuel($this->proprio->id, 2025, $this->agency->id);

        $this->assertEquals(0, $data['nb_paiements']);
        $this->assertEquals(0.0, (float) $data['revenus_bruts_loyers']);
        $this->assertEquals(0.0, (float) $data['irpp_estime']);
        $this->assertEquals(0.0, (float) $data['cfpb_estimee']);
    }

    #[Test]
    public function bilan_ne_compte_pas_les_paiements_dun_autre_proprio(): void
    {
        $autreProprio = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $this->agency->id,
        ]);

        $this->creerPaiementPourProprio($this->proprio,   200_000, 2025);
        $this->creerPaiementPourProprio($autreProprio,    500_000, 2025);

        $data = FiscalService::calculerBilanAnnuel($this->proprio->id, 2025, $this->agency->id);

        $this->assertEquals(1, $data['nb_paiements']);
        $this->assertEquals(200_000.0, (float) $data['revenus_bruts_loyers']);
    }

    // ════════════════════════════════════════════════════════════════════════
    // BilanFiscalController — Accès et sécurité
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_peut_voir_la_liste_des_bilans(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.bilans-fiscaux.index'))
             ->assertOk()
             ->assertSee($this->proprio->name);
    }

    #[Test]
    public function proprietaire_ne_peut_pas_acceder_aux_bilans(): void
    {
        $this->actingAs($this->proprio)
             ->get(route('admin.bilans-fiscaux.index'))
             ->assertForbidden();
    }

    #[Test]
    public function admin_peut_calculer_un_bilan(): void
    {
        $this->creerPaiementPourProprio($this->proprio, 300_000, now()->year);

        $this->actingAs($this->admin)
             ->post(route('admin.bilans-fiscaux.calculate', $this->proprio), [
                 'annee' => now()->year,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('bilans_fiscaux_proprietaires', [
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $this->proprio->id,
            'annee'           => now()->year,
        ]);
    }

    #[Test]
    public function recalcul_met_a_jour_le_bilan_existant(): void
    {
        // Premier calcul
        $this->creerPaiementPourProprio($this->proprio, 200_000, now()->year);
        $this->actingAs($this->admin)
             ->post(route('admin.bilans-fiscaux.calculate', $this->proprio), ['annee' => now()->year]);

        // Deuxième paiement ajouté après
        $this->creerPaiementPourProprio($this->proprio, 200_000, now()->year);

        // Recalcul
        $this->actingAs($this->admin)
             ->post(route('admin.bilans-fiscaux.calculate', $this->proprio), ['annee' => now()->year]);

        // Un seul enregistrement en base (updateOrCreate)
        $this->assertEquals(
            1,
            BilanFiscalProprietaire::where('proprietaire_id', $this->proprio->id)
                ->where('annee', now()->year)
                ->count()
        );
    }

    #[Test]
    public function admin_peut_voir_le_detail_dun_bilan(): void
    {
        $this->creerPaiementPourProprio($this->proprio, 300_000, now()->year);

        $this->actingAs($this->admin)
             ->get(route('admin.bilans-fiscaux.show', [$this->proprio, 'annee' => now()->year]))
             ->assertOk()
             ->assertSee($this->proprio->name);
    }

    #[Test]
    public function affichage_bilan_calcule_automatiquement_si_absent(): void
    {
        $this->creerPaiementPourProprio($this->proprio, 300_000, now()->year);

        // Pas de bilan pré-calculé → doit être calculé à la volée
        $this->assertDatabaseMissing('bilans_fiscaux_proprietaires', [
            'proprietaire_id' => $this->proprio->id,
        ]);

        $this->actingAs($this->admin)
             ->get(route('admin.bilans-fiscaux.show', [$this->proprio, 'annee' => now()->year]))
             ->assertOk();

        // Le bilan doit maintenant exister en base
        $this->assertDatabaseHas('bilans_fiscaux_proprietaires', [
            'proprietaire_id' => $this->proprio->id,
            'annee'           => now()->year,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Isolation multi-tenant
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_ne_peut_pas_calculer_bilan_dun_proprio_etranger(): void
    {
        $autreAgence = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create(['agency_id' => $autreAgence->id, 'statut' => 'essai', 'date_debut_essai' => now(), 'date_fin_essai' => now()->addDays(30)]);

        $proprioEtranger = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $autreAgence->id,
        ]);

        // Le bilan calculé ne doit concerner que les paiements de l'agence de l'admin
        // (calculerBilanAnnuel filtre par agencyId)
        $data = FiscalService::calculerBilanAnnuel(
            $proprioEtranger->id,
            now()->year,
            $this->agency->id  // agence de l'admin, pas celle du proprio étranger
        );

        // Aucun paiement de cette agence pour ce proprio étranger
        $this->assertEquals(0, $data['nb_paiements']);
    }

    #[Test]
    public function bilan_calcule_est_isole_par_agence(): void
    {
        // Deux agences avec chacune un proprio et un paiement
        $autreAgence = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create(['agency_id' => $autreAgence->id, 'statut' => 'essai', 'date_debut_essai' => now(), 'date_fin_essai' => now()->addDays(30)]);
        $adminAutre  = User::factory()->create(['role' => 'admin', 'agency_id' => $autreAgence->id]);
        $proprioAutre = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $autreAgence->id]);

        $this->creerPaiementPourProprio($this->proprio, 300_000, now()->year);

        // L'admin de l'agence 1 calcule son bilan
        $this->actingAs($this->admin)
             ->post(route('admin.bilans-fiscaux.calculate', $this->proprio), ['annee' => now()->year]);

        // Seul le bilan de l'agence 1 existe
        $this->assertEquals(
            1,
            BilanFiscalProprietaire::where('agency_id', $this->agency->id)->count()
        );
        $this->assertEquals(
            0,
            BilanFiscalProprietaire::where('agency_id', $autreAgence->id)->count()
        );
    }
}
