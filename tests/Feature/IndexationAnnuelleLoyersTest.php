<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * IndexationAnnuelleLoyersTest — Tests de la commande loyers:indexation.
 *
 * Couvre :
 *  - Revalorisation correcte du loyer_nu selon le taux contractuel
 *  - Mise à jour de loyer_contractuel et bien.loyer_mensuel
 *  - Idempotence : un contrat déjà indexé cette année est ignoré
 *  - Option --force : ré-indexe même si déjà fait
 *  - Option --dry-run : aucune modification en base
 *  - Contrats sans indexation (taux = 0) ignorés
 *  - Contrats résiliés ignorés
 */
class IndexationAnnuelleLoyersTest extends TestCase
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

    private function creerContratAvecIndexation(
        float $loyerNu,
        float $tauxIndexation,
        ?int  $anneeDejaIndexee = null,
        float $charges = 0,
        string $statut = 'actif'
    ): Contrat {
        $proprio   = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $this->agency->id]);
        $locataire = User::factory()->create(['role' => 'locataire',    'agency_id' => $this->agency->id]);

        $bien = Bien::factory()->create([
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $proprio->id,
            'loyer_mensuel'   => $loyerNu,
            'taux_commission' => 10.0,
        ]);

        return Contrat::factory()->create([
            'agency_id'                 => $this->agency->id,
            'bien_id'                   => $bien->id,
            'locataire_id'              => $locataire->id,
            'statut'                    => $statut,
            'loyer_nu'                  => $loyerNu,
            'loyer_contractuel'         => $loyerNu + $charges,
            'charges_mensuelles'        => $charges,
            'indexation_annuelle'       => $tauxIndexation,
            'annee_derniere_indexation' => $anneeDejaIndexee,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Calcul de la revalorisation
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function loyer_nu_est_revalorise_selon_le_taux(): void
    {
        // 200 000 FCFA + 5% = 210 000 FCFA
        $contrat = $this->creerContratAvecIndexation(200_000, 5.0);

        $this->artisan('loyers:indexation')->assertSuccessful();

        $this->assertEqualsWithDelta(
            210_000.0,
            (float) $contrat->fresh()->loyer_nu,
            1.0
        );
    }

    #[Test]
    public function loyer_contractuel_est_recalcule_avec_les_charges(): void
    {
        // loyer_nu = 200 000, charges = 15 000, taux = 5%
        // nouveau loyer_nu = 210 000
        // nouveau loyer_contractuel = 210 000 + 15 000 = 225 000
        $contrat = $this->creerContratAvecIndexation(200_000, 5.0, charges: 15_000);

        $this->artisan('loyers:indexation')->assertSuccessful();

        $this->assertEqualsWithDelta(225_000.0, (float) $contrat->fresh()->loyer_contractuel, 1.0);
    }

    #[Test]
    public function bien_loyer_mensuel_est_synchronise(): void
    {
        $contrat = $this->creerContratAvecIndexation(300_000, 3.0);

        $this->artisan('loyers:indexation')->assertSuccessful();

        // bien.loyer_mensuel doit refléter le nouveau loyer_nu (309 000)
        $this->assertEqualsWithDelta(
            309_000.0,
            (float) $contrat->fresh()->bien->loyer_mensuel,
            1.0
        );
    }

    #[Test]
    public function annee_derniere_indexation_est_mise_a_jour(): void
    {
        $contrat = $this->creerContratAvecIndexation(200_000, 5.0);
        $annee   = now()->year;

        $this->artisan('loyers:indexation')->assertSuccessful();

        $this->assertEquals($annee, $contrat->fresh()->annee_derniere_indexation);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Idempotence
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function contrat_deja_indexe_cette_annee_est_ignore(): void
    {
        $annee   = now()->year;
        $contrat = $this->creerContratAvecIndexation(200_000, 5.0, anneeDejaIndexee: $annee);

        $this->artisan('loyers:indexation')->assertSuccessful();

        // Loyer ne doit pas avoir changé
        $this->assertEqualsWithDelta(200_000.0, (float) $contrat->fresh()->loyer_nu, 1.0);
    }

    #[Test]
    public function option_force_reindexe_meme_si_deja_fait(): void
    {
        $annee   = now()->year;
        $contrat = $this->creerContratAvecIndexation(200_000, 5.0, anneeDejaIndexee: $annee);

        $this->artisan('loyers:indexation', ['--force' => true])->assertSuccessful();

        // Avec --force, le loyer DOIT être mis à jour
        $this->assertEqualsWithDelta(210_000.0, (float) $contrat->fresh()->loyer_nu, 1.0);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Option --dry-run
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function dry_run_naffecte_pas_la_base_de_donnees(): void
    {
        $contrat = $this->creerContratAvecIndexation(200_000, 5.0);

        $this->artisan('loyers:indexation', ['--dry-run' => true])->assertSuccessful();

        // Aucune modification en base
        $this->assertEqualsWithDelta(200_000.0, (float) $contrat->fresh()->loyer_nu, 1.0);
        $this->assertNull($contrat->fresh()->annee_derniere_indexation);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Contrats ignorés
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function contrat_sans_taux_indexation_est_ignore(): void
    {
        $contrat = $this->creerContratAvecIndexation(200_000, 0.0);

        $this->artisan('loyers:indexation')->assertSuccessful();

        $this->assertEqualsWithDelta(200_000.0, (float) $contrat->fresh()->loyer_nu, 1.0);
    }

    #[Test]
    public function contrat_resilie_nest_pas_indexe(): void
    {
        $contrat = $this->creerContratAvecIndexation(200_000, 5.0, statut: 'résilié');

        $this->artisan('loyers:indexation')->assertSuccessful();

        $this->assertEqualsWithDelta(200_000.0, (float) $contrat->fresh()->loyer_nu, 1.0);
    }

    #[Test]
    public function aucun_contrat_retourne_succes_sans_modification(): void
    {
        $this->artisan('loyers:indexation')->assertSuccessful();
    }
}
