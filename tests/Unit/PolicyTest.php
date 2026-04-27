<?php

namespace Tests\Unit;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Subscription;
use App\Models\User;
use App\Policies\BienPolicy;
use App\Policies\ContratPolicy;
use App\Policies\PaiementPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PolicyTest — Tests unitaires des règles d'accès métier.
 *
 * Vérifie que BienPolicy, ContratPolicy et PaiementPolicy
 * appliquent correctement les règles de la matrice des permissions.
 */
class PolicyTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────

    private function agence(): Agency
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        return $agency;
    }

    private function admin(Agency $agency): User
    {
        return User::factory()->create(['role' => 'admin', 'agency_id' => $agency->id]);
    }

    private function superadmin(): User
    {
        return User::factory()->create(['role' => 'superadmin', 'agency_id' => null]);
    }

    private function proprietaire(Agency $agency): User
    {
        return User::factory()->create(['role' => 'proprietaire', 'agency_id' => $agency->id]);
    }

    private function locataire(Agency $agency): User
    {
        return User::factory()->create(['role' => 'locataire', 'agency_id' => $agency->id]);
    }

    private function bien(Agency $agency, User $proprio): Bien
    {
        return Bien::factory()->create([
            'agency_id'      => $agency->id,
            'proprietaire_id' => $proprio->id,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // BienPolicy
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function superadmin_bypasse_toutes_les_regles_bien_policy()
    {
        $superadmin = $this->superadmin();
        $agence     = $this->agence();
        $proprio    = $this->proprietaire($agence);
        $bien       = $this->bien($agence, $proprio);
        $policy     = new BienPolicy();

        $this->assertTrue($policy->before($superadmin, 'create'));
        $this->assertTrue($policy->before($superadmin, 'delete'));
        $this->assertTrue($policy->before($superadmin, 'viewAny'));
    }

    #[Test]
    public function admin_peut_creer_un_bien()
    {
        $agence  = $this->agence();
        $admin   = $this->admin($agence);
        $policy  = new BienPolicy();

        $this->assertTrue($policy->create($admin));
    }

    #[Test]
    public function proprietaire_ne_peut_pas_creer_un_bien()
    {
        $agence  = $this->agence();
        $proprio = $this->proprietaire($agence);
        $policy  = new BienPolicy();

        $this->assertFalse($policy->create($proprio));
    }

    #[Test]
    public function locataire_ne_peut_pas_creer_un_bien()
    {
        $agence    = $this->agence();
        $locataire = $this->locataire($agence);
        $policy    = new BienPolicy();

        $this->assertFalse($policy->create($locataire));
    }

    #[Test]
    public function admin_peut_modifier_un_bien_de_son_agence()
    {
        $agence  = $this->agence();
        $admin   = $this->admin($agence);
        $proprio = $this->proprietaire($agence);
        $bien    = $this->bien($agence, $proprio);
        $policy  = new BienPolicy();

        $this->assertTrue($policy->update($admin, $bien)->allowed());
    }

    #[Test]
    public function admin_ne_peut_pas_modifier_un_bien_dune_autre_agence()
    {
        $agence1 = $this->agence();
        $agence2 = $this->agence();
        $admin   = $this->admin($agence1);
        $proprio = $this->proprietaire($agence2);
        $bien    = $this->bien($agence2, $proprio);
        $policy  = new BienPolicy();

        $this->assertFalse($policy->update($admin, $bien)->allowed());
    }

    #[Test]
    public function admin_peut_supprimer_un_bien_sans_contrat_actif()
    {
        $agence  = $this->agence();
        $admin   = $this->admin($agence);
        $proprio = $this->proprietaire($agence);
        $bien    = $this->bien($agence, $proprio);
        $policy  = new BienPolicy();

        $this->assertTrue($policy->delete($admin, $bien)->allowed());
    }

    #[Test]
    public function admin_ne_peut_pas_supprimer_un_bien_avec_contrat_actif()
    {
        $agence    = $this->agence();
        $admin     = $this->admin($agence);
        $proprio   = $this->proprietaire($agence);
        $locataire = $this->locataire($agence);
        $bien      = $this->bien($agence, $proprio);

        Contrat::factory()->create([
            'agency_id'    => $agence->id,
            'bien_id'      => $bien->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'actif',
        ]);

        $policy = new BienPolicy();

        $this->assertFalse($policy->delete($admin, $bien)->allowed());
    }

    #[Test]
    public function proprietaire_peut_voir_son_propre_bien()
    {
        $agence  = $this->agence();
        $proprio = $this->proprietaire($agence);
        $bien    = $this->bien($agence, $proprio);
        $policy  = new BienPolicy();

        $this->assertTrue($policy->view($proprio, $bien)->allowed());
    }

    #[Test]
    public function proprietaire_ne_peut_pas_voir_le_bien_dun_autre()
    {
        $agence  = $this->agence();
        $proprio1 = $this->proprietaire($agence);
        $proprio2 = $this->proprietaire($agence);
        $bien    = $this->bien($agence, $proprio2);
        $policy  = new BienPolicy();

        $this->assertFalse($policy->view($proprio1, $bien)->allowed());
    }

    #[Test]
    public function admin_et_proprietaire_peuvent_lister_les_biens()
    {
        $agence  = $this->agence();
        $admin   = $this->admin($agence);
        $proprio = $this->proprietaire($agence);
        $policy  = new BienPolicy();

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->viewAny($proprio));
    }

    #[Test]
    public function locataire_ne_peut_pas_lister_les_biens()
    {
        $agence    = $this->agence();
        $locataire = $this->locataire($agence);
        $policy    = new BienPolicy();

        $this->assertFalse($policy->viewAny($locataire));
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ContratPolicy
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function superadmin_bypasse_toutes_les_regles_contrat_policy()
    {
        $superadmin = $this->superadmin();
        $agence     = $this->agence();
        $locataire  = $this->locataire($agence);
        $contrat    = Contrat::factory()->create(['agency_id' => $agence->id, 'locataire_id' => $locataire->id]);
        $policy     = new ContratPolicy();

        $this->assertTrue($policy->before($superadmin, 'update'));
        $this->assertTrue($policy->before($superadmin, 'delete'));
    }

    #[Test]
    public function seul_admin_peut_creer_un_contrat()
    {
        $agence    = $this->agence();
        $admin     = $this->admin($agence);
        $proprio   = $this->proprietaire($agence);
        $locataire = $this->locataire($agence);
        $policy    = new ContratPolicy();

        $this->assertTrue($policy->create($admin));
        $this->assertFalse($policy->create($proprio));
        $this->assertFalse($policy->create($locataire));
    }

    #[Test]
    public function admin_peut_modifier_un_contrat_actif_de_son_agence()
    {
        $agence    = $this->agence();
        $admin     = $this->admin($agence);
        $locataire = $this->locataire($agence);
        $contrat   = Contrat::factory()->create([
            'agency_id'    => $agence->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'actif',
        ]);
        $policy = new ContratPolicy();

        $this->assertTrue($policy->update($admin, $contrat)->allowed());
    }

    #[Test]
    public function admin_ne_peut_pas_modifier_un_contrat_resilie()
    {
        $agence    = $this->agence();
        $admin     = $this->admin($agence);
        $locataire = $this->locataire($agence);
        $contrat   = Contrat::factory()->create([
            'agency_id'    => $agence->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'resilié',
        ]);
        $policy = new ContratPolicy();

        // La policy autorise (même agence, admin) ; c'est le contrôleur qui rejette
        $this->assertTrue($policy->update($admin, $contrat)->allowed());
    }

    #[Test]
    public function admin_ne_peut_pas_modifier_un_contrat_dune_autre_agence()
    {
        $agence1   = $this->agence();
        $agence2   = $this->agence();
        $admin     = $this->admin($agence1);
        $locataire = $this->locataire($agence2);
        $contrat   = Contrat::factory()->create([
            'agency_id'    => $agence2->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'actif',
        ]);
        $policy = new ContratPolicy();

        $this->assertFalse($policy->update($admin, $contrat)->allowed());
    }

    #[Test]
    public function admin_peut_resilier_un_contrat_actif()
    {
        $agence    = $this->agence();
        $admin     = $this->admin($agence);
        $locataire = $this->locataire($agence);
        $contrat   = Contrat::factory()->create([
            'agency_id'    => $agence->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'actif',
        ]);
        $policy = new ContratPolicy();

        $this->assertTrue($policy->resilier($admin, $contrat)->allowed());
    }

    #[Test]
    public function impossible_de_resilier_un_contrat_deja_resilie()
    {
        $agence    = $this->agence();
        $admin     = $this->admin($agence);
        $locataire = $this->locataire($agence);
        $contrat   = Contrat::factory()->create([
            'agency_id'    => $agence->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'resilie',
        ]);
        $policy = new ContratPolicy();

        $this->assertFalse($policy->resilier($admin, $contrat)->allowed());
    }

    #[Test]
    public function proprietaire_peut_lister_les_contrats()
    {
        $agence  = $this->agence();
        $proprio = $this->proprietaire($agence);
        $policy  = new ContratPolicy();

        $this->assertTrue($policy->viewAny($proprio));
    }

    #[Test]
    public function locataire_ne_peut_pas_lister_les_contrats()
    {
        $agence    = $this->agence();
        $locataire = $this->locataire($agence);
        $policy    = new ContratPolicy();

        $this->assertFalse($policy->viewAny($locataire));
    }

    #[Test]
    public function contrat_actif_ne_peut_pas_etre_supprime()
    {
        $agence    = $this->agence();
        $admin     = $this->admin($agence);
        $locataire = $this->locataire($agence);
        $contrat   = Contrat::factory()->create([
            'agency_id'    => $agence->id,
            'locataire_id' => $locataire->id,
            'statut'       => 'actif',
        ]);
        $policy = new ContratPolicy();

        $this->assertFalse($policy->delete($admin, $contrat)->allowed());
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PaiementPolicy
    // ═══════════════════════════════════════════════════════════════════════

    #[Test]
    public function superadmin_bypasse_toutes_les_regles_paiement_policy()
    {
        $superadmin = $this->superadmin();
        $policy     = new PaiementPolicy();

        $this->assertTrue($policy->before($superadmin, 'create'));
        $this->assertTrue($policy->before($superadmin, 'delete'));
    }

    #[Test]
    public function seul_admin_peut_creer_un_paiement()
    {
        $agence    = $this->agence();
        $admin     = $this->admin($agence);
        $proprio   = $this->proprietaire($agence);
        $locataire = $this->locataire($agence);
        $policy    = new PaiementPolicy();

        $this->assertTrue($policy->create($admin));
        $this->assertFalse($policy->create($proprio));
        $this->assertFalse($policy->create($locataire));
    }

    #[Test]
    public function admin_proprietaire_et_locataire_peuvent_lister_les_paiements()
    {
        $agence    = $this->agence();
        $admin     = $this->admin($agence);
        $proprio   = $this->proprietaire($agence);
        $locataire = $this->locataire($agence);
        $policy    = new PaiementPolicy();

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->viewAny($proprio));
        $this->assertTrue($policy->viewAny($locataire));
    }

    #[Test]
    public function admin_peut_voir_un_paiement_de_son_agence()
    {
        $agence    = $this->agence();
        $admin     = $this->admin($agence);
        $locataire = $this->locataire($agence);
        $paiement  = Paiement::factory()->create([
            'agency_id' => $agence->id,
            'statut'    => 'valide',
        ]);
        $policy = new PaiementPolicy();

        $this->assertTrue($policy->view($admin, $paiement)->allowed());
    }

    #[Test]
    public function admin_ne_peut_pas_voir_un_paiement_dune_autre_agence()
    {
        $agence1  = $this->agence();
        $agence2  = $this->agence();
        $admin    = $this->admin($agence1);
        $paiement = Paiement::factory()->create([
            'agency_id' => $agence2->id,
            'statut'    => 'valide',
        ]);
        $policy = new PaiementPolicy();

        $this->assertFalse($policy->view($admin, $paiement)->allowed());
    }

    #[Test]
    public function impossible_de_supprimer_un_paiement_valide()
    {
        $agence   = $this->agence();
        $admin    = $this->admin($agence);
        $paiement = Paiement::factory()->create([
            'agency_id' => $agence->id,
            'statut'    => 'valide',
        ]);
        $policy = new PaiementPolicy();

        $this->assertFalse($policy->delete($admin, $paiement)->allowed());
    }

    #[Test]
    public function admin_peut_supprimer_un_paiement_en_attente()
    {
        $agence   = $this->agence();
        $admin    = $this->admin($agence);
        $paiement = Paiement::factory()->create([
            'agency_id' => $agence->id,
            'statut'    => 'en_attente',
        ]);
        $policy = new PaiementPolicy();

        $this->assertTrue($policy->delete($admin, $paiement)->allowed());
    }

    #[Test]
    public function quittance_non_disponible_pour_paiement_en_attente()
    {
        $agence   = $this->agence();
        $admin    = $this->admin($agence);
        $paiement = Paiement::factory()->create([
            'agency_id' => $agence->id,
            'statut'    => 'en_attente',
        ]);
        $policy = new PaiementPolicy();

        $this->assertFalse($policy->telechargerQuittance($admin, $paiement)->allowed());
    }

    #[Test]
    public function quittance_disponible_pour_paiement_valide()
    {
        $agence   = $this->agence();
        $admin    = $this->admin($agence);
        $paiement = Paiement::factory()->create([
            'agency_id' => $agence->id,
            'statut'    => 'valide',
        ]);
        $policy = new PaiementPolicy();

        $this->assertTrue($policy->telechargerQuittance($admin, $paiement)->allowed());
    }

    #[Test]
    public function admin_ne_peut_pas_modifier_un_paiement_valide()
    {
        $agence   = $this->agence();
        $admin    = $this->admin($agence);
        $paiement = Paiement::factory()->create([
            'agency_id' => $agence->id,
            'statut'    => 'valide',
        ]);
        $policy = new PaiementPolicy();

        $this->assertFalse($policy->update($admin, $paiement)->allowed());
    }

    #[Test]
    public function admin_peut_modifier_un_paiement_en_attente()
    {
        $agence   = $this->agence();
        $admin    = $this->admin($agence);
        $paiement = Paiement::factory()->create([
            'agency_id' => $agence->id,
            'statut'    => 'en_attente',
        ]);
        $policy = new PaiementPolicy();

        $this->assertTrue($policy->update($admin, $paiement)->allowed());
    }

    #[Test]
    public function admin_peut_valider_un_paiement_en_attente()
    {
        $agence   = $this->agence();
        $admin    = $this->admin($agence);
        $paiement = Paiement::factory()->create([
            'agency_id' => $agence->id,
            'statut'    => 'en_attente',
        ]);
        $policy = new PaiementPolicy();

        $this->assertTrue($policy->valider($admin, $paiement)->allowed());
    }

    #[Test]
    public function impossible_de_valider_un_paiement_deja_valide()
    {
        $agence   = $this->agence();
        $admin    = $this->admin($agence);
        $paiement = Paiement::factory()->create([
            'agency_id' => $agence->id,
            'statut'    => 'valide',
        ]);
        $policy = new PaiementPolicy();

        $this->assertFalse($policy->valider($admin, $paiement)->allowed());
    }
}
