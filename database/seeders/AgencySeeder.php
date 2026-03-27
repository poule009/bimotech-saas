<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
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

        // ── 2. Créer le SuperAdmin de la plateforme ───────────────────────
        // Ce compte n'appartient à aucune agence — il supervise tout
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

        // ── 3. Rattacher tous les users existants à l'agence BIMO-Tech ────
        // Tous les users qui n'ont pas encore d'agency_id
        $usersCount = User::whereNull('agency_id')
            ->where('role', '!=', 'superadmin')
            ->update(['agency_id' => $agency->id]);

        $this->command->info("✓ {$usersCount} utilisateur(s) rattaché(s) à BIMO-Tech");

        // ── 4. Rattacher tous les biens existants ─────────────────────────
        $biensCount = Bien::withoutGlobalScopes()
            ->whereNull('agency_id')
            ->update(['agency_id' => $agency->id]);

        $this->command->info("✓ {$biensCount} bien(s) rattaché(s) à BIMO-Tech");

        // ── 5. Rattacher tous les contrats existants ──────────────────────
        $contratsCount = Contrat::withoutGlobalScopes()
            ->whereNull('agency_id')
            ->update(['agency_id' => $agency->id]);

        $this->command->info("✓ {$contratsCount} contrat(s) rattaché(s) à BIMO-Tech");

        // ── 6. Rattacher tous les paiements existants ─────────────────────
        $paiementsCount = Paiement::withoutGlobalScopes()
            ->whereNull('agency_id')
            ->update(['agency_id' => $agency->id]);

        $this->command->info("✓ {$paiementsCount} paiement(s) rattaché(s) à BIMO-Tech");

        // ── 7. Mettre à jour l'admin BIMO-Tech existant ───────────────────
        // L'ancien admin hardcodé devient un admin normal rattaché à l'agence
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
        $this->command->info("  Agence     : {$agency->name}");
        $this->command->info("  Admin      : admin@bimotech.sn");
        $this->command->info("  SuperAdmin : superadmin@bimotech.sn");
        $this->command->info('════════════════════════════════════════');
    }
}