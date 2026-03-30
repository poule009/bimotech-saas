<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * SuperAdminSeeder — Compte BIMO-Tech permanent
 *
 * Utilise updateOrCreate pour être idempotent :
 * ce seeder peut être relancé après migrate:fresh sans créer de doublon.
 *
 * ⚠️  Le modèle User a le cast 'password' => 'hashed' (Laravel 10+).
 *     On passe donc le mot de passe EN CLAIR — le cast se charge du hachage.
 *
 * Variables .env requises :
 *   SUPER_ADMIN_EMAIL=superadmin@bimo-tech.sn
 *   SUPER_ADMIN_PASSWORD=VotreMotDePasseSecret
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('SUPER_ADMIN_EMAIL', 'superadmin@bimo-tech.sn');
        $password = env('SUPER_ADMIN_PASSWORD', 'oui');

        $user = User::updateOrCreate(
            // ── Critère de recherche (clé unique) ──
            ['email' => $email],
            // ── Valeurs à créer ou mettre à jour ──
            [
                'name'              => 'Super Admin BIMO-Tech',
                'password'          => $password,   // Le cast 'hashed' du modèle s'occupe du bcrypt
                'role'              => 'superadmin',
                'agency_id'         => null,         // Pas d'agence — accès global
                'email_verified_at' => now(),        // Compte vérifié d'emblée
            ]
        );

        $this->command->info("✅ Super Admin prêt : {$user->email} (rôle : {$user->role})");
    }
}
