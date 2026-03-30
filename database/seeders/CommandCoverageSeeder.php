<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommandCoverageSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Nettoyage ciblé des données de couverture
            Paiement::where('notes', 'like', '[TEST_COVERAGE]%')->delete();

            Subscription::where('statut', '!=', 'expiré')
                ->whereHas('agency', function ($q) {
                    $q->where('name', 'like', 'TEST_COVERAGE_%');
                })
                ->delete();

            $agencyExpired = Agency::firstOrCreate(
                ['slug' => 'test-coverage-agency-expired'],
                [
                    'name' => 'TEST_COVERAGE_AGENCY_EXPIRED',
                    'email' => 'agency_expired_coverage@test.local',
                    'actif' => true,
                ]
            );

            $agencyValid = Agency::firstOrCreate(
                ['slug' => 'test-coverage-agency-valid'],
                [
                    'name' => 'TEST_COVERAGE_AGENCY_VALID',
                    'email' => 'agency_valid_coverage@test.local',
                    'actif' => true,
                ]
            );

            $ownerExpired = User::firstOrCreate(
                ['email' => 'owner_expired_coverage@test.local'],
                [
                    'name' => 'Owner Expired Coverage',
                    'password' => bcrypt('password'),
                    'role' => 'proprietaire',
                    'agency_id' => $agencyExpired->id,
                ]
            );

            $tenantExpired = User::firstOrCreate(
                ['email' => 'tenant_expired_coverage@test.local'],
                [
                    'name' => 'Tenant Expired Coverage',
                    'password' => bcrypt('password'),
                    'role' => 'locataire',
                    'agency_id' => $agencyExpired->id,
                ]
            );

            $ownerValid = User::firstOrCreate(
                ['email' => 'owner_valid_coverage@test.local'],
                [
                    'name' => 'Owner Valid Coverage',
                    'password' => bcrypt('password'),
                    'role' => 'proprietaire',
                    'agency_id' => $agencyValid->id,
                ]
            );

            $tenantValid = User::firstOrCreate(
                ['email' => 'tenant_valid_coverage@test.local'],
                [
                    'name' => 'Tenant Valid Coverage',
                    'password' => bcrypt('password'),
                    'role' => 'locataire',
                    'agency_id' => $agencyValid->id,
                ]
            );

            $proprietaireExpired = Proprietaire::firstOrCreate(
                ['user_id' => $ownerExpired->id],
                []
            );

            $locataireExpired = Locataire::firstOrCreate(
                ['user_id' => $tenantExpired->id],
                []
            );

            $proprietaireValid = Proprietaire::firstOrCreate(
                ['user_id' => $ownerValid->id],
                []
            );

            $locataireValid = Locataire::firstOrCreate(
                ['user_id' => $tenantValid->id],
                []
            );

            $bienEligible = Bien::firstOrCreate(
                ['reference' => 'TEST_COVERAGE_BIEN_ELIGIBLE'],
                [
                    'agency_id' => $agencyExpired->id,
                    'proprietaire_id' => $ownerExpired->id,
                    'type' => 'appartement',
                    'adresse' => 'Adresse test eligible',
                    'ville' => 'Dakar',
                    'loyer_mensuel' => 150000,
                    'taux_commission' => 10,
                    'statut' => 'loue',
                ]
            );

            $bienWithPayment = Bien::firstOrCreate(
                ['reference' => 'TEST_COVERAGE_BIEN_WITH_PAYMENT'],
                [
                    'agency_id' => $agencyExpired->id,
                    'proprietaire_id' => $ownerExpired->id,
                    'type' => 'appartement',
                    'adresse' => 'Adresse test doublon',
                    'ville' => 'Dakar',
                    'loyer_mensuel' => 160000,
                    'taux_commission' => 10,
                    'statut' => 'loue',
                ]
            );

            $bienInactive = Bien::firstOrCreate(
                ['reference' => 'TEST_COVERAGE_BIEN_INACTIVE'],
                [
                    'agency_id' => $agencyValid->id,
                    'proprietaire_id' => $ownerValid->id,
                    'type' => 'appartement',
                    'adresse' => 'Adresse test non actif',
                    'ville' => 'Dakar',
                    'loyer_mensuel' => 170000,
                    'taux_commission' => 10,
                    'statut' => 'loue',
                ]
            );

            $period = Carbon::now()->startOfMonth();

            $contratEligible = Contrat::updateOrCreate(
                ['bien_id' => $bienEligible->id, 'locataire_id' => $locataireExpired->id],
                [
                    'date_debut' => $period->copy()->subMonths(2)->toDateString(),
                    'date_fin' => null,
                    'loyer_contractuel' => 150000,
                    'caution' => 0,
                    'statut' => 'actif',
                    'observations' => '[TEST_COVERAGE] contrat actif éligible',
                ]
            );

            $contratWithPayment = Contrat::updateOrCreate(
                ['bien_id' => $bienWithPayment->id, 'locataire_id' => $locataireExpired->id],
                [
                    'date_debut' => $period->copy()->subMonths(3)->toDateString(),
                    'date_fin' => null,
                    'loyer_contractuel' => 160000,
                    'caution' => 0,
                    'statut' => 'actif',
                    'observations' => '[TEST_COVERAGE] contrat avec paiement existant',
                ]
            );

            $contratInactive = Contrat::updateOrCreate(
                ['bien_id' => $bienInactive->id, 'locataire_id' => $locataireValid->id],
                [
                    'date_debut' => $period->copy()->subMonths(6)->toDateString(),
                    'date_fin' => $period->copy()->subMonth()->endOfMonth()->toDateString(),
                    'loyer_contractuel' => 170000,
                    'caution' => 0,
                    'statut' => 'resilié',
                    'observations' => '[TEST_COVERAGE] contrat non actif/expiré',
                ]
            );

            $tauxCommission = 10.0;

            $montants = Paiement::calculerMontants((float) $contratWithPayment->loyer_contractuel, $tauxCommission);

            Paiement::firstOrCreate(
                [
                    'contrat_id' => $contratWithPayment->id,
                    'periode' => $period->toDateString(),
                ],
                [
                    'agency_id' => $agencyExpired->id,
                    'montant_encaisse' => $contratWithPayment->loyer_contractuel,
                    'mode_paiement' => 'virement',
                    'taux_commission_applique' => $tauxCommission,
                    'commission_agence' => $montants['commission_agence'],
                    'tva_commission' => $montants['tva_commission'],
                    'commission_ttc' => $montants['commission_ttc'],
                    'net_proprietaire' => $montants['net_proprietaire'],
                    'caution_percue' => 0,
                    'est_premier_paiement' => false,
                    'date_paiement' => now()->toDateString(),
                    'reference_paiement' => 'TEST-COVERAGE-PAID-' . $period->format('Ym'),
                    'statut' => 'valide',
                    'notes' => '[TEST_COVERAGE] paiement déjà existant pour anti-doublon',
                ]
            );

            Subscription::updateOrCreate(
                ['agency_id' => $agencyExpired->id],
                [
                    'plan' => 'mensuel',
                    'statut' => 'actif',
                    'date_fin_essai' => now()->subDays(20),
                    'date_fin_abonnement' => now()->subDay(),
                    'montant' => 10000,
                ]
            );

            Subscription::updateOrCreate(
                ['agency_id' => $agencyValid->id],
                [
                    'plan' => 'mensuel',
                    'statut' => 'actif',
                    'date_fin_essai' => now()->subDays(5),
                    'date_fin_abonnement' => now()->addDays(15),
                    'montant' => 10000,
                ]
            );
        });
    }
}
