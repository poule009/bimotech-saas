<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\RelanceImpayeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ImpayeControllerTest — Tests de la gestion des impayés.
 *
 * Couvre :
 *  - index : liste les contrats actifs + stats
 *  - relance : envoie une notification + met à jour observations
 *  - accès refusé pour non-staff
 */
class ImpayeControllerTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User   $admin;
    private User   $proprio;
    private User   $locataire;
    private Bien   $bien;
    private Contrat $contrat;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->agency = Agency::factory()->create(['actif' => true]);

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
        ]);

        $this->locataire = User::factory()->createOne([
            'role'      => 'locataire',
            'agency_id' => $this->agency->id,
        ]);

        $this->bien = Bien::factory()->create([
            'agency_id'       => $this->agency->id,
            'proprietaire_id' => $this->proprio->id,
            'loyer_mensuel'   => 200_000,
        ]);

        $this->contrat = Contrat::factory()->create([
            'agency_id'         => $this->agency->id,
            'bien_id'           => $this->bien->id,
            'locataire_id'      => $this->locataire->id,
            'statut'            => 'actif',
            'loyer_contractuel' => 200_000,
            'date_debut'        => now()->subMonths(3)->toDateString(),
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // Index impayés
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function admin_peut_voir_la_liste_des_impayes(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.impayes.index'))
            ->assertOk()
            ->assertViewIs('impayes.index');
    }

    #[Test]
    public function index_filtre_par_mois_et_annee(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.impayes.index', [
                'mois'  => now()->month,
                'annee' => now()->year,
            ]))
            ->assertOk()
            ->assertViewHasAll(['impayes', 'payes', 'stats']);
    }

    #[Test]
    public function locataire_ne_peut_pas_acceder_a_la_liste_impayes(): void
    {
        $this->actingAs($this->locataire)
            ->get(route('admin.impayes.index'))
            ->assertForbidden();
    }

    // ════════════════════════════════════════════════════════════════════════
    // Relance impayé
    // ════════════════════════════════════════════════════════════════════════

    #[Test]
    public function relance_envoie_notification_au_locataire(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.impayes.relance', $this->contrat))
            ->assertRedirect();

        Notification::assertSentTo(
            $this->locataire,
            RelanceImpayeNotification::class
        );
    }

    #[Test]
    public function relance_ajoute_note_dans_observations(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.impayes.relance', $this->contrat));

        $this->assertStringContainsString(
            'Relance envoyée le',
            $this->contrat->fresh()->observations ?? ''
        );
    }

    #[Test]
    public function relance_peut_preciser_mois_et_annee(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.impayes.relance', $this->contrat), [
                'mois'  => now()->subMonth()->month,
                'annee' => now()->subMonth()->year,
            ])
            ->assertRedirect();

        Notification::assertSentTo($this->locataire, RelanceImpayeNotification::class);
    }

    #[Test]
    public function locataire_ne_peut_pas_envoyer_une_relance(): void
    {
        $this->actingAs($this->locataire)
            ->post(route('admin.impayes.relance', $this->contrat))
            ->assertForbidden();
    }
}
