<?php

namespace Database\Seeders;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BimoTechSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Admin ─────────────────────────────────────────────────────────
        $admin = User::updateOrCreate(
            ['email' => 'admin@bimotech.sn'],
            [
                'name'      => 'Administrateur BIMO',
                'password'  => Hash::make('Admin@2025!'),
                'role'      => 'admin',
                'telephone' => '+221 77 000 00 01',
                'adresse'   => 'Plateau, Dakar',
            ]
        );

        // ── 2. Propriétaire ───────────────────────────────────────────────────
        $proprietaire = User::updateOrCreate(
            ['email' => 'moussa.diallo@bimotech.sn'],
            [
                'name'      => 'Moussa Diallo',
                'password'  => Hash::make('Proprio@2025!'),
                'role'      => 'proprietaire',
                'telephone' => '+221 77 100 20 30',
                'adresse'   => 'Almadies, Dakar',
            ]
        );

        // ── 3. Locataire ──────────────────────────────────────────────────────
        $locataire = User::updateOrCreate(
            ['email' => 'fatou.sow@bimotech.sn'],
            [
                'name'      => 'Fatou Sow',
                'password'  => Hash::make('Locataire@2025!'),
                'role'      => 'locataire',
                'telephone' => '+221 76 200 30 40',
                'adresse'   => 'Medina, Dakar',
            ]
        );

        // ── 4. Bien immobilier (10% de commission) ────────────────────────────
        $bien = Bien::updateOrCreate(
            ['reference' => 'BIEN-2025-001'],
            [
                'proprietaire_id' => $proprietaire->id,
                'type'            => 'Appartement',
                'adresse'         => '25 Rue de Thiong',
                'ville'           => 'Dakar',
                'surface_m2'      => 85,
                'nombre_pieces'   => 3,
                'loyer_mensuel'   => 250000.00,   // 250 000 FCFA
                'taux_commission' => 10.00,        // 10% → TVA incluse = 11.8%
                'statut'          => 'loue',
                'description'     => 'Appartement F3 au 2ème étage, vue sur cour, climatisé.',
            ]
        );

        // ── 5. Contrat actif ──────────────────────────────────────────────────
        $contrat = Contrat::updateOrCreate(
            ['bien_id' => $bien->id, 'locataire_id' => $locataire->id, 'statut' => 'actif'],
            [
                'date_debut'          => Carbon::now()->startOfMonth()->subMonths(2),
                'date_fin'            => null,  // contrat ouvert (CDI locatif)
                'loyer_contractuel'   => 250000.00,
                'caution'             => 250000.00,  // 1 mois de caution
                'statut'              => 'actif',
                'observations'        => 'Contrat de bail habitation. Préavis 1 mois.',
            ]
        );

        // ── 6. Premier paiement avec caution ──────────────────────────────────
        $calcul1 = Paiement::calculerMontants(250000, 10);
        $periode1 = Carbon::now()->startOfMonth()->subMonths(2);

        Paiement::updateOrCreate(
            ['contrat_id' => $contrat->id, 'periode' => $periode1->toDateString()],
            [
                'montant_encaisse'         => 250000.00,
                'mode_paiement'            => 'virement',
                'taux_commission_applique' => 10.00,
                'commission_agence'        => $calcul1['commission_ht'],   // 25 000
                'tva_commission'           => $calcul1['tva'],              // 4 500
                'commission_ttc'           => $calcul1['commission_ttc'],   // 29 500
                'net_proprietaire'         => $calcul1['net_proprietaire'], // 220 500
                'caution_percue'           => 250000.00,
                'est_premier_paiement'     => true,
                'date_paiement'            => $periode1->copy()->addDays(2)->toDateString(),
                'reference_paiement'       => 'QUITT-' . $contrat->id . '-' . $periode1->format('Ym') . '-INIT',
                'statut'                   => 'valide',
                'notes'                    => 'Premier mois + caution encaissés.',
            ]
        );

        // ── 7. Deuxième paiement (mois suivant) ───────────────────────────────
        $calcul2 = Paiement::calculerMontants(250000, 10);
        $periode2 = Carbon::now()->startOfMonth()->subMonths(1);

        Paiement::updateOrCreate(
            ['contrat_id' => $contrat->id, 'periode' => $periode2->toDateString()],
            [
                'montant_encaisse'         => 250000.00,
                'mode_paiement'            => 'mobile_money',
                'taux_commission_applique' => 10.00,
                'commission_agence'        => $calcul2['commission_ht'],
                'tva_commission'           => $calcul2['tva'],
                'commission_ttc'           => $calcul2['commission_ttc'],
                'net_proprietaire'         => $calcul2['net_proprietaire'],
                'caution_percue'           => 0,
                'est_premier_paiement'     => false,
                'date_paiement'            => $periode2->copy()->addDays(3)->toDateString(),
                'reference_paiement'       => 'QUITT-' . $contrat->id . '-' . $periode2->format('Ym') . '-0002',
                'statut'                   => 'valide',
            ]
        );

        $this->command->info('✅  Seeder BIMO-Tech OK');
        $this->command->table(
            ['Rôle', 'Nom', 'Email', 'Mot de passe (test)'],
            [
                ['Admin',        $admin->name,        $admin->email,        'Admin@2025!'],
                ['Propriétaire', $proprietaire->name, $proprietaire->email, 'Proprio@2025!'],
                ['Locataire',    $locataire->name,    $locataire->email,    'Locataire@2025!'],
            ]
        );
        $this->command->info("💰 Bien créé : {$bien->reference} — Loyer : 250 000 FCFA — Commission : 10%");
        $this->command->info("📄 Contrat actif ID : {$contrat->id} — 2 paiements enregistrés");
    }
}