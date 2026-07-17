<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Proprietaire;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * FiscaliteGlobaleTest — module Fiscalité organisé par propriétaire.
 *
 * Écran 1 (index) : tuiles globales + liste des propriétaires (statut, montant).
 * Écran 2 (proprietaire) : fiche d'un propriétaire, cartes de taxe + registre de calcul.
 * Consomme l'agrégation CalendrierFiscalService.
 */
class FiscaliteGlobaleTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 7, 13, 9));

        $this->agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id' => $this->agency->id, 'statut' => 'actif', 'plan' => 'annuel',
            'date_debut_abonnement' => now()->subMonth(), 'date_fin_abonnement' => now()->addYear(),
        ]);
        $this->admin = User::factory()->create(['role' => 'admin', 'agency_id' => $this->agency->id]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function proprio(array $profil = []): User
    {
        $u = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $this->agency->id]);
        Proprietaire::create(array_merge(['user_id' => $u->id, 'est_personne_morale_is' => false], $profil));
        return $u;
    }

    private function bienLoue(User $prop, int $loyer = 200_000): Bien
    {
        return Bien::create([
            'agency_id' => $this->agency->id, 'proprietaire_id' => $prop->id,
            'reference' => Bien::generateReference($this->agency->id),
            'titre' => 'Villa test', 'type' => 'appartement', 'adresse' => 'x',
            'ville' => 'Dakar', 'loyer_mensuel' => $loyer, 'taux_commission' => 10, 'statut' => 'loue',
        ]);
    }

    private function bailEnRetard(Bien $bien): Contrat
    {
        // Bail signé le 1er mai → limite droits 1er juin < aujourd'hui (13 juil).
        $loc = User::factory()->create(['role' => 'locataire', 'agency_id' => $this->agency->id]);
        Locataire::create(['user_id' => $loc->id, 'type_locataire' => 'particulier']);
        $c = new Contrat([
            'bien_id' => $bien->id, 'locataire_id' => $loc->id, 'date_debut' => '2026-05-01',
            'loyer_nu' => 200_000, 'charges_mensuelles' => 0, 'type_bail' => 'habitation', 'caution' => 0, 'statut' => 'actif',
        ]);
        $c->agency_id = $this->agency->id;
        $c->save();
        return $c;
    }

    #[Test]
    public function ecran1_liste_les_proprietaires_avec_tuiles_et_statut(): void
    {
        $prop = $this->proprio(['assujetti_tva' => true]);
        $bien = $this->bienLoue($prop);
        $this->bailEnRetard($bien);

        $res = $this->actingAs($this->admin)->get(route('admin.echeances-fiscales.index'));

        $res->assertOk()
            ->assertSee('En retard')
            ->assertSee('Dans les 7 jours')
            ->assertSee('Dans les 30 jours')
            ->assertSee($prop->name)                              // carte propriétaire
            ->assertSee('à régulariser')                          // tuile retard (montant droits > 0)
            ->assertSee('fiscaliteProprietaires');                // composant de recherche
    }

    #[Test]
    public function ecran1_agence_sans_proprietaire_affiche_etat_vide(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.echeances-fiscales.index'))
            ->assertOk()
            ->assertSee('Aucun propriétaire à afficher');
    }

    #[Test]
    public function ecran1_proprietaire_sans_echeance_apparait_a_jour(): void
    {
        // Propriétaire physique avec un bien mais sans loyer encaissé : ses échéances
        // BRS/IRPP existent à 0 F → aucun montant dû → statut « À jour » (le statut de
        // triage ne compte que ce qui est réellement dû).
        $prop = $this->proprio();
        Bien::create([
            'agency_id' => $this->agency->id, 'proprietaire_id' => $prop->id,
            'reference' => Bien::generateReference($this->agency->id),
            'titre' => 'Villa libre', 'type' => 'appartement', 'adresse' => 'x',
            'ville' => 'Dakar', 'loyer_mensuel' => 200_000, 'taux_commission' => 10, 'statut' => 'disponible',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.echeances-fiscales.index'))
            ->assertOk()
            ->assertSee($prop->name)
            ->assertSee('À jour');
    }

    #[Test]
    public function ecran2_fiche_affiche_les_cartes_de_taxe_et_le_detail(): void
    {
        $prop = $this->proprio(['assujetti_tva' => true]);
        $bien = $this->bienLoue($prop);
        $this->bailEnRetard($bien);

        $res = $this->actingAs($this->admin)
            ->get(route('admin.echeances-fiscales.proprietaire', $prop));

        $res->assertOk()
            ->assertSee($prop->name)
            ->assertSee('Enregistrement du bail', false)   // carte droits d'enregistrement
            ->assertSee('Timbre fiscal', false)            // ligne du registre de calcul
            ->assertSee('Fiable')                          // badge d'une taxe fiable
            ->assertSee('taxCard');                        // composant de dépliage
    }

    #[Test]
    public function ecran2_refuse_un_proprietaire_d_une_autre_agence(): void
    {
        $autre = Agency::factory()->create(['actif' => true]);
        $prop  = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $autre->id]);
        Proprietaire::create(['user_id' => $prop->id, 'est_personne_morale_is' => false]);

        $this->actingAs($this->admin)
            ->get(route('admin.echeances-fiscales.proprietaire', $prop))
            ->assertNotFound();
    }
}
