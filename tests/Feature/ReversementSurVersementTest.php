<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\ReversementProprietaire;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Garde-fou anti sur-versement (#2 module 5) : on ne peut pas reverser plus que
 * le solde réellement dû au propriétaire (StoreReversementRequest).
 */
class ReversementSurVersementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $proprio;

    /** Crée une agence (plan agence → feature comptabilité) + un propriétaire dont
     *  le solde mandant vaut exactement 100 000 F. Retourne l'admin owner. */
    protected function setUp(): void
    {
        parent::setUp();

        $agency = Agency::factory()->create(['actif' => true]);
        Subscription::factory()->create([
            'agency_id'             => $agency->id,
            'statut'                => 'actif',
            'plan'                  => 'annuel',
            'plan_niveau'           => 'agence',
            'date_debut_abonnement' => now()->subMonth(),
            'date_fin_abonnement'   => now()->addYear(),
        ]);

        // Owner : passe tous les agency.can (dont comptabilite.modifier).
        $this->admin = User::factory()->create([
            'role' => 'admin', 'agency_id' => $agency->id, 'is_owner' => true,
        ]);

        $this->proprio = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $agency->id]);
        $locataire     = User::factory()->create(['role' => 'locataire', 'agency_id' => $agency->id]);
        $bien = Bien::factory()->create(['agency_id' => $agency->id, 'proprietaire_id' => $this->proprio->id]);
        $contrat = Contrat::factory()->create([
            'agency_id' => $agency->id, 'bien_id' => $bien->id, 'locataire_id' => $locataire->id, 'statut' => 'actif',
        ]);

        // Solde dû = montant_net_bailleur (aucune dépense, aucun reversement) = 100 000.
        Paiement::factory()->create([
            'agency_id'                 => $agency->id,
            'contrat_id'                => $contrat->id,
            'periode'                   => now()->startOfMonth()->toDateString(),
            'date_paiement'             => now()->toDateString(),
            'statut'                    => 'valide',
            'montant_encaisse'          => 100_000,
            'net_a_verser_proprietaire' => 100_000,
            'montant_net_bailleur'      => 100_000,
        ]);
    }

    #[Test]
    public function reverser_plus_que_le_solde_du_est_bloque(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.reversements.store'), [
                'proprietaire_id'  => $this->proprio->id,
                'montant'          => 150_000, // > solde (100 000)
                'date_reversement' => now()->toDateString(),
                'mode_paiement'    => 'virement',
            ])
            ->assertSessionHasErrors('montant');

        $this->assertDatabaseCount('reversements_proprietaires', 0);
    }

    #[Test]
    public function reverser_dans_la_limite_du_solde_est_accepte(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.reversements.store'), [
                'proprietaire_id'  => $this->proprio->id,
                'montant'          => 80_000, // <= solde (100 000)
                'date_reversement' => now()->toDateString(),
                'mode_paiement'    => 'virement',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('reversements_proprietaires', [
            'proprietaire_id' => $this->proprio->id,
            'montant'         => 80_000,
        ]);
    }
}
