<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Créer l'agence BIMO-Tech ───────────────────────────────────
        $agency = Agency::updateOrCreate(
            ['slug' => 'bimo-tech'],
            [
                'name'             => 'BIMO-Tech',
                'email'            => 'contact@bimotech.sn',
                'telephone'        => '+221 33 800 00 01',
                'adresse'          => 'Plateau, Dakar',
                'couleur_primaire' => '#1a3c5e',
                'taux_tva'         => 18.00,
                'actif'            => true,
            ]
        );

        $this->command->info("✓ Agence créée : {$agency->name} (ID: {$agency->id})");

        // ── 2. Subscription BIMO-Tech — actif 1 an ────────────────────────
        Subscription::updateOrCreate(
            ['agency_id' => $agency->id],
            [
                'statut'                => 'actif',
                'plan'                  => 'annuel',
                'montant_paye'          => 240000,
                'date_debut_essai'      => now(),
                'date_fin_essai'        => now()->addDays(30),
                'date_debut_abonnement' => now(),
                'date_fin_abonnement'   => now()->addYear(),
            ]
        );

        $this->command->info("✓ Subscription BIMO-Tech créée (annuel, valide 1 an)");

        // ── 3. Créer le SuperAdmin ────────────────────────────────────────
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@bimotech.sn'],
            [
                'agency_id' => null,
                'name'      => 'Super Administrateur Plateforme',
                'password'  => bcrypt('SuperAdmin@2025!'),
                'role'      => 'superadmin',
                'telephone' => '+221 33 800 00 00',
                'adresse'   => 'Dakar, Sénégal',
            ]
        );

        $this->command->info("✓ SuperAdmin créé : {$superAdmin->email}");

        // ── 4. Rattacher tous les users existants ─────────────────────────
        $usersCount = User::whereNull('agency_id')
            ->where('role', '!=', 'superadmin')
            ->update(['agency_id' => $agency->id]);

        $this->command->info("✓ {$usersCount} utilisateur(s) rattaché(s) à BIMO-Tech");

        // ── 5. Rattacher tous les biens existants ─────────────────────────
        $biensCount = Bien::withoutGlobalScopes()
            ->whereNull('agency_id')
            ->update(['agency_id' => $agency->id]);

        $this->command->info("✓ {$biensCount} bien(s) rattaché(s) à BIMO-Tech");

        // ── 6. Rattacher tous les contrats existants ──────────────────────
        $contratsCount = Contrat::withoutGlobalScopes()
            ->whereNull('agency_id')
            ->update(['agency_id' => $agency->id]);

        $this->command->info("✓ {$contratsCount} contrat(s) rattaché(s) à BIMO-Tech");

        // ── 7. Rattacher tous les paiements existants ─────────────────────
        $paiementsCount = Paiement::withoutGlobalScopes()
            ->whereNull('agency_id')
            ->update(['agency_id' => $agency->id]);

        $this->command->info("✓ {$paiementsCount} paiement(s) rattaché(s) à BIMO-Tech");

        // ── 8. Mettre à jour l'admin BIMO-Tech ───────────────────────────
        User::where('email', 'admin@bimotech.sn')
            ->update([
                'agency_id' => $agency->id,
                'role'      => 'admin',
            ]);

        $this->command->info("✓ Admin BIMO-Tech rattaché à l'agence");

        $this->command->newLine();
        $this->command->info('════════════════════════════════════════');
        $this->command->info('  Migration SaaS terminée avec succès !');
        $this->command->info('════════════════════════════════════════');
    }
}