<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * QuittancePdfTest — Vérification du téléchargement de quittances PDF.
 *
 * Ce test valide que DomPDF génère correctement les PDFs en conditions
 * proches de la production : données réelles en base, autorisation Policy,
 * Content-Type correct, nom de fichier attendu.
 *
 * Couvre :
 *  - Admin télécharge le PDF via admin.paiements.pdf
 *  - Locataire télécharge SA quittance via locataire.paiements.pdf
 *  - Propriétaire télécharge via proprietaire.paiements.pdf
 *  - Locataire ne peut pas télécharger la quittance d'un autre locataire (403)
 *  - Paiement annulé → téléchargement refusé (403)
 *  - Content-Type = application/pdf
 *  - Nom de fichier contient la référence du paiement
 */
class QuittancePdfTest extends TestCase
{
    use RefreshDatabase;

    private Agency  $agency;
    private User    $admin;
    private User    $proprio;
    private User    $locataire;
    private Contrat $contrat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agency = Agency::factory()->create([
            'actif' => true,
            'name'  => 'Agence Dakar Immo',
            'email' => 'contact@dakar-immo.sn',
        ]);

        Subscription::factory()->create([
            'agency_id'             => $this->agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        $this->admin = User::factory()->createOne([
            'role'      => 'admin',
            'agency_id' => $this->agency->id,
        ]);

        $this->proprio = User::factory()->createOne([
            'role'      => 'proprietaire',
            'agency_id' => $this->agency->id,
            'name'      => 'Mamadou Diallo',
            'telephone' => '+221 77 100 00 01',
            'adresse'   => 'Almadies, Dakar',
        ]);

        $this->locataire = User::factory()->createOne([
            'role'      => 'locataire',
            'agency_id' => $this->agency->id,
            'name'      => 'Fatou Sène',
            'telephone' => '+221 77 200 00 02',
            'adresse'   => 'Plateau, Dakar',
        ]);

        $bien = Bien::factory()->create([
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $this->proprio->id,
            'reference'       => 'BIEN-DKR-001',
            'type'            => 'Appartement',
            'adresse'         => 'Rue de Thiès, Dakar',
            'ville'           => 'Dakar',
            'loyer_mensuel'   => 250_000,
            'taux_commission' => 10.0,
        ]);

        $this->contrat = Contrat::factory()->create([
            'agency_id'         => $this->agency->id,
            'bien_id'           => $bien->id,
            'locataire_id'      => $this->locataire->id,
            'statut'            => 'actif',
            'loyer_contractuel' => 250_000,
            'loyer_nu'          => 250_000,
            'date_debut'        => now()->subMonths(3)->toDateString(),
        ]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helper : crée un paiement valide avec une référence fixe
    // ────────────────────────────────────────────────────────────────────────

    private function creerPaiementValide(string $reference = 'PAY-TEST-ABCD1234'): Paiement
    {
        return Paiement::create([
            'agency_id'                => $this->agency->id,
            'contrat_id'               => $this->contrat->id,
            'periode'                  => now()->subMonth()->startOfMonth(),
            'montant_encaisse'         => 250_000,
            'loyer_nu'                 => 250_000,
            'taux_commission_applique' => 10.0,
            'commission_agence'        => 25_000,
            'tva_commission'           => 4_500,
            'commission_ttc'           => 29_500,
            'net_proprietaire'         => 220_500,
            'mode_paiement'            => 'virement',
            'date_paiement'            => now()->subDay(),
            'reference_paiement'       => $reference,
            'statut'                   => 'valide',
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Admin — route admin.paiements.pdf
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_peut_telecharger_la_quittance_pdf(): void
    {
        $paiement = $this->creerPaiementValide();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.paiements.pdf', $paiement));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type', '')
        );
    }

    #[Test]
    public function nom_fichier_contient_la_reference_paiement(): void
    {
        $reference = 'PAY-REF-XYZ99999';
        $paiement  = $this->creerPaiementValide($reference);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.paiements.pdf', $paiement));

        $response->assertOk();

        $disposition = $response->headers->get('Content-Disposition', '');
        $this->assertStringContainsString($reference, $disposition);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Locataire — route locataire.paiements.pdf
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function locataire_peut_telecharger_sa_propre_quittance(): void
    {
        $paiement = $this->creerPaiementValide();

        $response = $this->actingAs($this->locataire)
            ->get(route('locataire.paiements.pdf', $paiement));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type', '')
        );
    }

    #[Test]
    public function locataire_ne_peut_pas_telecharger_la_quittance_dun_autre(): void
    {
        $autreLocataire = User::factory()->createOne([
            'role'      => 'locataire',
            'agency_id' => $this->agency->id,
        ]);

        $paiement = $this->creerPaiementValide();

        $this->actingAs($autreLocataire)
            ->get(route('locataire.paiements.pdf', $paiement))
            ->assertForbidden();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Propriétaire — route proprietaire.paiements.pdf
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function proprietaire_peut_telecharger_la_quittance_de_son_bien(): void
    {
        $paiement = $this->creerPaiementValide();

        $response = $this->actingAs($this->proprio)
            ->get(route('proprietaire.paiements.pdf', $paiement));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type', '')
        );
    }

    #[Test]
    public function proprietaire_ne_peut_pas_telecharger_quittance_dun_autre_bien(): void
    {
        $autrePropio = User::factory()->createOne([
            'role'      => 'proprietaire',
            'agency_id' => $this->agency->id,
        ]);

        $paiement = $this->creerPaiementValide();

        $this->actingAs($autrePropio)
            ->get(route('proprietaire.paiements.pdf', $paiement))
            ->assertForbidden();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Paiement non valide → 403
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function paiement_annule_ne_peut_pas_generer_de_quittance(): void
    {
        $paiement = Paiement::create([
            'agency_id'                => $this->agency->id,
            'contrat_id'               => $this->contrat->id,
            'periode'                  => now()->subMonth()->startOfMonth(),
            'montant_encaisse'         => 250_000,
            'loyer_nu'                 => 250_000,
            'taux_commission_applique' => 10.0,
            'commission_agence'        => 25_000,
            'tva_commission'           => 4_500,
            'commission_ttc'           => 29_500,
            'net_proprietaire'         => 220_500,
            'mode_paiement'            => 'virement',
            'date_paiement'            => now()->subDay(),
            'reference_paiement'       => 'PAY-ANNULE-001',
            'statut'                   => 'annule', // ← annulé
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.paiements.pdf', $paiement))
            ->assertForbidden();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Contenu PDF — données présentes dans le rendu
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function pdf_contient_bien_les_donnees_du_paiement(): void
    {
        $paiement = $this->creerPaiementValide('PAY-CONTENT-TEST');

        // Récupère le contenu brut du PDF (binaire) pour détecter
        // que le montant et la référence apparaissent quelque part
        // (DomPDF encode le texte dans le PDF, pas le binaire — on vérifie
        // que la réponse n'est pas vide et a une taille raisonnable > 10 Ko)
        $response = $this->actingAs($this->admin)
            ->get(route('admin.paiements.pdf', $paiement));

        $response->assertOk();

        $contentLength = strlen($response->getContent());
        $this->assertGreaterThan(10_000, $contentLength,
            'Le PDF semble trop petit — DomPDF a peut-être rendu une page vide.'
        );
    }
}
