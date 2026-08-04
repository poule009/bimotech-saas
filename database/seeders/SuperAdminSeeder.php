<?php

namespace Database\Seeders;

use App\Enums\UserRole;
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
        $password = env('SUPER_ADMIN_PASSWORD')
            ?? throw new \RuntimeException('SUPER_ADMIN_PASSWORD must be set in .env');

        $user = User::firstOrNew(['email' => $email]);

        // ⚠️  role, agency_id et sa_est_principal sont volontairement HORS $fillable
        //     (protection mass-assignment) : passés à updateOrCreate ils seraient
        //     ignorés en silence et le compte retomberait sur le défaut DB
        //     (role = 'locataire'). D'où l'assignation directe ci-dessous.
        $user->name                 = 'Super Admin BIMO-Tech';
        $user->password             = $password;   // Le cast 'hashed' du modèle s'occupe du bcrypt
        $user->role                 = UserRole::SuperAdmin->value;
        $user->agency_id            = null;        // Pas d'agence — accès global
        $user->sa_est_principal     = true;        // Admin principal : accès total, non filtré
        $user->must_change_password = false;
        $user->email_verified_at    = $user->email_verified_at ?? now();
        $user->save();

        $this->command->info("✅ Super Admin prêt : {$user->email} (rôle : {$user->role}, principal)");
    }
}
