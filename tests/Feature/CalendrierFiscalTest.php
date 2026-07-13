<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Proprietaire;
use App\Models\Subscription;
use App\Models\User;
use App\Services\CalendrierFiscalService;
use App\Services\FiscalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * CalendrierFiscalTest — agrégation des échéances fiscales à venir.
 *
 * Couvre les 6 cas du brief : croisement proprios/biens/contrats, regroupement
 * CFPB+TEOM, exclusion CGF, apparition/disparition des Droits, 3 lignes agence
 * sans montant, filtrage par horizon. + endpoint JSON.
 */
class CalendrierFiscalTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User $admin;
    private CalendrierFiscalService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id' => $this->agency->id, 'statut' => 'actif', 'plan' => 'annuel',
            'date_debut_abonnement' => now()->subMonth(), 'date_fin_abonnement' => now()->addYear(),
        ]);
        $this->admin = User::factory()->create(['role' => 'admin', 'agency_id' => $this->agency->id]);
        $this->service = new CalendrierFiscalService();
    }

    private function proprio(array $profil = []): User
    {
        $u = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $this->agency->id]);
        Proprietaire::create(array_merge(['user_id' => $u->id, 'est_personne_morale_is' => false], $profil));
        return $u;
    }

    private function bienLoue(User $prop, int $loyer = 200_000): Bien
    {
        // Bien::create (pas factory) : la factory génère un propriétaire « fantôme »
        // via sa definition(), qui polluerait la liste des propriétaires de l'agence.
        return Bien::create([
            'agency_id' => $this->agency->id, 'proprietaire_id' => $prop->id,
            'reference' => Bien::generateReference($this->agency->id),
            'titre' => 'Villa test', 'type' => 'appartement', 'adresse' => 'x',
            'ville' => 'Dakar', 'loyer_mensuel' => $loyer, 'taux_commission' => 10, 'statut' => 'loue',
        ]);
    }

    private function types(array $items): array
    {
        return array_map(fn ($i) => $i['type'], $items);
    }

    #[Test]
    public function cas1_tva_brs_cfpbteom_irpp_sans_duplication(): void
    {
        $prop = $this->proprio(['assujetti_tva' => true, 'est_personne_morale_is' => false, 'brs_dispense' => false]);
        $this->bienLoue($prop);

        $items = $this->service->echeancesAVenir($this->agency->id, 365, Carbon::create(2026, 7, 13));
        $types = $this->types($items);

        $this->assertContains('tva', $types);
        $this->assertContains('brs_mensuel', $types);
        $this->assertContains('brs_annuel', $types);
        $this->assertContains('irpp', $types);
        // Regroupement CFPB+TEOM : une seule ligne par bien (pas deux).
        $this->assertEquals(1, count(array_filter($types, fn ($t) => $t === 'cfpb_teom')));
        $this->assertEquals(1, count(array_filter($types, fn ($t) => $t === 'tva')));

        $cfpb = collect($items)->firstWhere('type', 'cfpb_teom');
        $this->assertEquals(120_000 + 86_400, $cfpb['montant']); // CFPB 5% + TEOM 3,6% Dakar
        $this->assertEquals('2027-01-31', $cfpb['date_limite']);
        $this->assertEquals('estimation_structurelle', $cfpb['statut_calcul']);
    }

    #[Test]
    public function cas2_cgf_active_exclut_irpp_et_cfpbteom(): void
    {
        $ech = FiscalService::calculerEcheancierCgf(1_875_000, 'trois_versements', 2026);
        $prop = $this->proprio([
            'est_personne_morale_is' => false, 'cgf_active' => true, 'cgf_annee' => 2026,
            'cgf_revenu_brut_prevu' => 15_000_000, 'cgf_montant' => 1_875_000,
            'cgf_mode_paiement' => 'trois_versements', 'cgf_echeances' => $ech,
        ]);
        $this->bienLoue($prop);

        $items = $this->service->echeancesAVenir($this->agency->id, 365, Carbon::create(2026, 1, 15));
        $types = $this->types($items);

        $this->assertContains('cgf_declaration', $types);
        $this->assertEquals(3, count(array_filter($types, fn ($t) => $t === 'cgf_versement')));
        $this->assertNotContains('irpp', $types);
        $this->assertNotContains('cfpb_teom', $types);
    }

    #[Test]
    public function cas3et4_droit_enregistrement_apparait_puis_disparait(): void
    {
        $prop = $this->proprio();
        $bien = $this->bienLoue($prop);
        $loc  = User::factory()->create(['role' => 'locataire', 'agency_id' => $this->agency->id]);
        Locataire::create(['user_id' => $loc->id, 'type_locataire' => 'particulier']);

        $ref = Carbon::create(2026, 7, 13);
        $c = new Contrat([
            'bien_id' => $bien->id, 'locataire_id' => $loc->id, 'date_debut' => $ref->copy()->subWeeks(3)->toDateString(),
            'loyer_nu' => 200_000, 'charges_mensuelles' => 0, 'type_bail' => 'habitation', 'caution' => 0, 'statut' => 'actif',
        ]);
        $c->agency_id = $this->agency->id;
        $c->save();

        $items = $this->service->echeancesAVenir($this->agency->id, 30, $ref);
        $droit = collect($items)->firstWhere('type', 'droit_enregistrement');
        $this->assertNotNull($droit);
        $this->assertEquals($ref->copy()->subWeeks(3)->addMonthNoOverflow()->format('Y-m-d'), $droit['date_limite']);

        $c->update(['droit_enreg_effectue' => true]);
        $apres = $this->service->echeancesAVenir($this->agency->id, 30, $ref);
        $this->assertNotContains('droit_enregistrement', $this->types($apres));
    }

    #[Test]
    public function cas5_trois_echeances_agence_sans_montant(): void
    {
        $items = $this->service->echeancesAVenir($this->agency->id, 30, Carbon::create(2026, 7, 13));

        foreach (['is_agence', 'cel_vl_agence', 'cel_va_agence'] as $type) {
            $it = collect($items)->firstWhere('type', $type);
            $this->assertNotNull($it, "$type doit toujours être présent");
            $this->assertNull($it['montant'], "$type ne doit jamais avoir de montant");
            $this->assertNull($it['proprietaire']);
        }
        $this->assertNull(collect($items)->firstWhere('type', 'is_agence')['date_limite']);
    }

    #[Test]
    public function cas6_filtrage_horizon(): void
    {
        $prop = $this->proprio(['assujetti_tva' => true]);
        $this->bienLoue($prop);
        $ref = Carbon::create(2026, 7, 13);

        $court = $this->service->echeancesAVenir($this->agency->id, 7, $ref);
        $long  = $this->service->echeancesAVenir($this->agency->id, 365, $ref);

        $entites = fn ($items) => count(array_filter($items, fn ($i) => ! str_ends_with($i['type'], '_agence')));
        $this->assertLessThan($entites($long), $entites($court));
        // À 7 jours : seules les échéances du 15 (TVA + BRS mensuel) rentrent.
        $this->assertEquals(2, $entites($court));
    }

    #[Test]
    public function endpoint_json_repond_avec_la_structure_attendue(): void
    {
        $prop = $this->proprio(['assujetti_tva' => true]);
        $this->bienLoue($prop);

        $this->actingAs($this->admin)
            ->getJson(route('admin.echeances-fiscales.calendrier', ['horizon' => 365]))
            ->assertOk()
            ->assertJsonStructure([
                'horizon_jours', 'genere_le',
                'echeances' => [['type', 'libelle', 'proprietaire', 'proprietaire_id', 'date_limite', 'montant', 'statut_calcul']],
            ])
            ->assertJsonPath('horizon_jours', 365);
    }
}
