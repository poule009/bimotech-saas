<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * BailleurControllerTest — Tests de la fiche bailleur et de la protection IDOR.
 *
 * Le BailleurController expose les biens et paiements d'un propriétaire.
 * La protection IDOR est critique : un admin d'agence A ne doit jamais
 * pouvoir accéder aux données d'un propriétaire d'agence B via l'URL.
 */
class BailleurControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────

    private function adminAvecAgence(): User
    {
        $agency = Agency::factory()->create(['actif' => true]);

        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        return User::factory()->create([
            'role'      => 'admin',
            'agency_id' => $agency->id,
        ]);
    }

    private function proprietaireDansAgence(User $admin): User
    {
        return User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $admin->agency_id,
        ]);
    }

    // ── Tests index ───────────────────────────────────────────────────────

    #[Test]
    public function admin_peut_voir_la_liste_des_bailleurs()
    {
        $admin = $this->adminAvecAgence();

        $this->actingAs($admin)
             ->get(route('admin.bailleurs.index'))
             ->assertOk();
    }

    #[Test]
    public function proprietaire_ne_peut_pas_voir_la_liste_des_bailleurs()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaireDansAgence($admin);

        $this->actingAs($proprio)
             ->get(route('admin.bailleurs.index'))
             ->assertForbidden();
    }

    #[Test]
    public function locataire_ne_peut_pas_voir_la_liste_des_bailleurs()
    {
        $admin     = $this->adminAvecAgence();
        $locataire = User::factory()->create([
            'role'      => 'locataire',
            'agency_id' => $admin->agency_id,
        ]);

        $this->actingAs($locataire)
             ->get(route('admin.bailleurs.index'))
             ->assertForbidden();
    }

    #[Test]
    public function invite_ne_peut_pas_voir_la_liste_des_bailleurs()
    {
        $this->get(route('admin.bailleurs.index'))
             ->assertRedirect(route('login'));
    }

    // ── Tests show ────────────────────────────────────────────────────────

    #[Test]
    public function admin_peut_voir_la_fiche_dun_bailleur_de_son_agence()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaireDansAgence($admin);

        // BailleurPortfolioService::getPortfolioDetail() fait abort(403) si aucun bien
        Bien::factory()->create([
            'agency_id'      => $admin->agency_id,
            'proprietaire_id' => $proprio->id,
        ]);

        $this->actingAs($admin)
             ->get(route('admin.bailleurs.show', $proprio->id))
             ->assertOk();
    }

    #[Test]
    public function admin_ne_peut_pas_voir_la_fiche_dun_bailleur_dune_autre_agence()
    {
        $admin1          = $this->adminAvecAgence();
        $autreAgence     = Agency::factory()->create();
        $proprioEtranger = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $autreAgence->id,
        ]);

        // La protection IDOR doit retourner 404 (filtre agency_id + firstOrFail)
        $this->actingAs($admin1)
             ->get(route('admin.bailleurs.show', $proprioEtranger->id))
             ->assertNotFound();
    }

    #[Test]
    public function fiche_bailleur_inexistant_retourne_404()
    {
        $admin = $this->adminAvecAgence();

        $this->actingAs($admin)
             ->get(route('admin.bailleurs.show', 99999))
             ->assertNotFound();
    }

    #[Test]
    public function admin_ne_peut_pas_voir_la_fiche_dun_locataire_via_url()
    {
        $admin     = $this->adminAvecAgence();
        $locataire = User::factory()->create([
            'role'      => 'locataire',
            'agency_id' => $admin->agency_id,
        ]);

        // L'URL accepte un userId mais le contrôleur filtre role='proprietaire'
        $this->actingAs($admin)
             ->get(route('admin.bailleurs.show', $locataire->id))
             ->assertNotFound();
    }

    #[Test]
    public function proprietaire_ne_peut_pas_voir_une_fiche_bailleur()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaireDansAgence($admin);

        $this->actingAs($proprio)
             ->get(route('admin.bailleurs.show', $proprio->id))
             ->assertForbidden();
    }

    // ── Tests export PDF ─────────────────────────────────────────────────

    #[Test]
    public function export_pdf_bloque_pour_bailleur_dune_autre_agence()
    {
        $admin1          = $this->adminAvecAgence();
        $autreAgence     = Agency::factory()->create();
        $proprioEtranger = User::factory()->create([
            'role'      => 'proprietaire',
            'agency_id' => $autreAgence->id,
        ]);

        $this->actingAs($admin1)
             ->get(route('admin.bailleurs.export-pdf', $proprioEtranger->id))
             ->assertNotFound();
    }

    #[Test]
    public function export_pdf_retourne_403_si_bailleur_sans_bien()
    {
        $admin  = $this->adminAvecAgence();
        $proprio = $this->proprietaireDansAgence($admin);

        // Aucun bien pour ce propriétaire → abort(403) dans le contrôleur
        $this->actingAs($admin)
             ->get(route('admin.bailleurs.export-pdf', $proprio->id))
             ->assertForbidden();
    }
}
