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
 * FiscaliteGlobaleTest — vue globale du module Fiscalité (écran 1).
 *
 * Consomme l'agrégation CalendrierFiscalService : groupes d'urgence, tuiles de
 * résumé, filtres, états vides.
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

    #[Test]
    public function vue_globale_affiche_groupes_tuiles_et_lignes(): void
    {
        $prop = $this->proprio(['assujetti_tva' => true]);
        $bien = $this->bienLoue($prop);

        // Bail en retard : signé le 1er mai → limite 1er juin < aujourd'hui (13 juil).
        $loc = User::factory()->create(['role' => 'locataire', 'agency_id' => $this->agency->id]);
        Locataire::create(['user_id' => $loc->id, 'type_locataire' => 'particulier']);
        $c = new Contrat([
            'bien_id' => $bien->id, 'locataire_id' => $loc->id, 'date_debut' => '2026-05-01',
            'loyer_nu' => 200_000, 'charges_mensuelles' => 0, 'type_bail' => 'habitation', 'caution' => 0, 'statut' => 'actif',
        ]);
        $c->agency_id = $this->agency->id;
        $c->save();

        $res = $this->actingAs($this->admin)->get(route('admin.echeances-fiscales.index'));

        $res->assertOk()
            ->assertSee('En retard')
            ->assertSee('Dans les 7 jours')
            ->assertSee('Plus tard cette année')
            ->assertSee('Enregistrement du bail', false)      // ligne droits en retard
            ->assertSee('TVA — déclaration mensuelle', false) // ligne à venir
            ->assertSee('Voir comptable')                     // ligne agence IS, sans montant
            ->assertSee('fisc-chip')                          // chips de filtre présents
            ->assertSee('Estimation');                        // badge CFPB+TEOM
    }

    #[Test]
    public function agence_sans_entite_affiche_etat_vide(): void
    {
        // Aucun propriétaire / bien / contrat → seules les lignes agence existent → état vide.
        $this->actingAs($this->admin)
            ->get(route('admin.echeances-fiscales.index'))
            ->assertOk()
            ->assertSee('Rien à signaler')
            ->assertDontSee('Voir comptable'); // les lignes agence ne sont pas rendues dans l'état vide
    }

    #[Test]
    public function tuile_retard_affiche_le_nombre_en_retard(): void
    {
        $prop = $this->proprio();
        $bien = $this->bienLoue($prop);
        $loc = User::factory()->create(['role' => 'locataire', 'agency_id' => $this->agency->id]);
        Locataire::create(['user_id' => $loc->id, 'type_locataire' => 'particulier']);
        $c = new Contrat([
            'bien_id' => $bien->id, 'locataire_id' => $loc->id, 'date_debut' => '2026-05-01',
            'loyer_nu' => 200_000, 'charges_mensuelles' => 0, 'type_bail' => 'habitation', 'caution' => 0, 'statut' => 'actif',
        ]);
        $c->agency_id = $this->agency->id;
        $c->save();

        $this->actingAs($this->admin)
            ->get(route('admin.echeances-fiscales.index'))
            ->assertOk()
            ->assertSee('à régulariser'); // sous-texte de la tuile retard (montant > 0)
    }
}
