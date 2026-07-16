<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use App\Services\ComptabiliteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * CompteMandantTest — le décompte du compte propriétaire doit se réconcilier :
 * loyers + caution − commission − BRS − dépenses = net à reverser.
 */
class CompteMandantTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function le_net_du_reconcilie_avec_la_caution_incluse(): void
    {
        $agency    = Agency::factory()->create(['actif' => true]);
        $proprio   = User::factory()->create(['role' => 'proprietaire', 'agency_id' => $agency->id]);
        $locataire = User::factory()->create(['role' => 'locataire', 'agency_id' => $agency->id]);
        $bien = Bien::factory()->create(['agency_id' => $agency->id, 'proprietaire_id' => $proprio->id]);
        $contrat = Contrat::factory()->create([
            'agency_id' => $agency->id, 'bien_id' => $bien->id, 'locataire_id' => $locataire->id, 'statut' => 'actif',
        ]);

        $montantEncaisse = 664_500;
        $commissionTtc   = 45_760;
        $brs             = 10_000;
        $caution         = 1_000_000;
        $netAVerser      = $montantEncaisse - $commissionTtc - $brs;       // 608 740
        $netBailleur     = $netAVerser + $caution;                        // 1 608 740

        Paiement::factory()->create([
            'agency_id'                 => $agency->id,
            'contrat_id'                => $contrat->id,
            'periode'                   => now()->startOfMonth()->toDateString(),
            'date_paiement'             => now()->toDateString(),
            'statut'                    => 'valide',
            'montant_encaisse'          => $montantEncaisse,
            'commission_ttc'            => $commissionTtc,
            'brs_amount'                => $brs,
            'net_a_verser_proprietaire' => $netAVerser,
            'montant_net_bailleur'      => $netBailleur,
        ]);

        $compte = app(ComptabiliteService::class)->compteMandant($agency->id, $proprio->id);

        // La caution est exposée séparément
        $this->assertSame((float) $caution, $compte['caution_incluse']);

        // loyers + caution − commission − BRS − dépenses = net_du
        $reconcilie = $compte['loyers_encaisses']
            + $compte['caution_incluse']
            - $compte['commissions_deduites']
            - $compte['brs_retenu']
            - $compte['depenses_avancees'];

        $this->assertSame($compte['net_du'], round($reconcilie, 2));
        $this->assertSame((float) $netBailleur, $compte['net_du']);
        // Sans reversement, le solde restant égale le net dû
        $this->assertSame($compte['net_du'], $compte['solde_restant']);
    }
}
