<?php

namespace Tests\Unit;

use App\Models\Agency;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Quittance;
use App\Models\Subscription;
use App\Models\User;
use App\Services\QuittanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * QuittanceServiceTest — Tests unitaires du service de quittances.
 *
 * La quittance est un document légal (loi sénégalaise) :
 *  - immuable une fois créée
 *  - numérotée séquentiellement par agence et par année
 *  - générée uniquement pour les paiements validés
 */
class QuittanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private QuittanceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuittanceService();
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function paiementValide(): Paiement
    {
        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);
        $admin = User::factory()->create(['role' => 'admin', 'agency_id' => $agency->id]);
        Auth::login($admin);

        return Paiement::factory()->create([
            'agency_id' => $agency->id,
            'statut'    => 'valide',
        ]);
    }

    // ── Tests generer() ───────────────────────────────────────────────────

    #[Test]
    public function generer_cree_une_quittance_pour_paiement_valide()
    {
        $paiement = $this->paiementValide();

        $quittance = $this->service->generer($paiement);

        $this->assertInstanceOf(Quittance::class, $quittance);
        $this->assertDatabaseHas('quittances', [
            'paiement_id' => $paiement->id,
            'agency_id'   => $paiement->agency_id,
        ]);
    }

    #[Test]
    public function generer_lance_exception_si_paiement_non_valide()
    {
        $paiement = $this->paiementValide();
        $paiement->statut = 'en_attente';

        $this->expectException(\LogicException::class);
        $this->service->generer($paiement);
    }

    #[Test]
    public function generer_lance_exception_si_quittance_existe_deja()
    {
        $paiement = $this->paiementValide();

        // Première génération
        $this->service->generer($paiement);

        // Deuxième tentative → exception
        $this->expectException(\RuntimeException::class);
        $this->service->generer($paiement);
    }

    // ── Tests numérotation ────────────────────────────────────────────────

    #[Test]
    public function numero_quittance_suit_format_qt_agencyid_annee_sequence()
    {
        $paiement  = $this->paiementValide();
        $quittance = $this->service->generer($paiement);

        $annee    = now()->year;
        $agencyId = str_pad($paiement->agency_id, 2, '0', STR_PAD_LEFT);

        $this->assertStringStartsWith("QT-{$agencyId}-{$annee}-", $quittance->numero);
        $this->assertStringEndsWith('0001', $quittance->numero);
    }

    #[Test]
    public function deux_quittances_de_la_meme_agence_ont_des_numeros_sequentiels()
    {
        $paiement1 = $this->paiementValide();
        $paiement2 = Paiement::factory()->create([
            'agency_id' => $paiement1->agency_id,
            'statut'    => 'valide',
        ]);

        $q1 = $this->service->generer($paiement1);
        $q2 = $this->service->generer($paiement2);

        $this->assertStringEndsWith('0001', $q1->numero);
        $this->assertStringEndsWith('0002', $q2->numero);
    }

    #[Test]
    public function deux_agences_differentes_ont_des_sequences_independantes()
    {
        $paiement1 = $this->paiementValide();

        // Deuxième agence indépendante
        $agency2 = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'             => $agency2->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);
        $paiement2 = Paiement::factory()->create([
            'agency_id' => $agency2->id,
            'statut'    => 'valide',
        ]);

        $q1 = $this->service->generer($paiement1);
        $q2 = $this->service->generer($paiement2);

        // Les deux commencent à 0001 (séquences indépendantes)
        $this->assertStringEndsWith('0001', $q1->numero);
        $this->assertStringEndsWith('0001', $q2->numero);
        // Mais les préfixes sont différents
        $this->assertNotEquals($q1->numero, $q2->numero);
    }

    // ── Tests nomFichier() ────────────────────────────────────────────────

    #[Test]
    public function nom_fichier_suit_format_standard()
    {
        $paiement  = $this->paiementValide();
        $quittance = $this->service->generer($paiement);

        $nom = $this->service->nomFichier($quittance);

        $this->assertStringStartsWith('Quittance_', $nom);
        $this->assertStringEndsWith('.pdf', $nom);
        $this->assertStringContainsString($quittance->numero, $nom);
        $this->assertStringContainsString($quittance->mois_concerne, $nom);
    }

    // ── Tests existePourPaiement() ────────────────────────────────────────

    #[Test]
    public function existe_pour_paiement_retourne_false_si_aucune_quittance()
    {
        $paiement = $this->paiementValide();

        $this->assertFalse($this->service->existePourPaiement($paiement));
    }

    #[Test]
    public function existe_pour_paiement_retourne_true_apres_generation()
    {
        $paiement = $this->paiementValide();
        $this->service->generer($paiement);

        $this->assertTrue($this->service->existePourPaiement($paiement));
    }
}
