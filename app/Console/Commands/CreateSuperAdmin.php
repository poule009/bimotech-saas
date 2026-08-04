<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/**
 * Crée (ou remet à niveau) le compte super-admin principal de la plateforme.
 *
 * Utilisable en production depuis la console Laravel Cloud :
 *   php artisan superadmin:create --email=... --name="..."
 * Le mot de passe est demandé en saisie masquée s'il n'est pas fourni.
 *
 * Idempotente : relancée sur un email existant, elle met le compte à niveau
 * (rôle, principal, mot de passe si fourni) sans créer de doublon.
 *
 * ⚠️  role / agency_id / sa_est_principal ne sont pas dans $fillable du modèle
 *     (protection mass-assignment) → assignation directe obligatoire ici.
 */
class CreateSuperAdmin extends Command
{
    protected $signature = 'superadmin:create
                            {--email= : Email du compte (défaut : SUPER_ADMIN_EMAIL)}
                            {--password= : Mot de passe en clair (défaut : SUPER_ADMIN_PASSWORD, sinon saisie masquée)}
                            {--name= : Nom affiché du compte}
                            {--force : Ne pas demander confirmation lors de la promotion d\'un compte existant}';

    protected $description = 'Crée ou met à niveau le compte super-admin principal (BIMO-Tech)';

    public function handle(): int
    {
        $email = $this->option('email')
            ?: env('SUPER_ADMIN_EMAIL')
            ?: $this->ask('Email du super-admin');

        $password = $this->option('password')
            ?: env('SUPER_ADMIN_PASSWORD')
            ?: $this->secret('Mot de passe (laisser vide pour conserver celui du compte existant)');

        $existant = User::where('email', $email)->first();

        $regles = [
            'email'    => ['required', 'email', 'max:255'],
            'password' => [$existant ? 'nullable' : 'required', 'string', 'min:12'],
        ];

        $validation = Validator::make(compact('email', 'password'), $regles);

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $erreur) {
                $this->error($erreur);
            }

            return self::FAILURE;
        }

        // Garde-fou : ne jamais promouvoir en silence un compte métier existant
        // (locataire, propriétaire, admin d'agence) vers un accès plateforme total.
        if ($existant && ! $existant->isSuperAdmin()) {
            $this->warn("⚠️  Un compte existe déjà pour {$email} — rôle actuel : {$existant->role}"
                . ($existant->agency_id ? " (agence #{$existant->agency_id})" : ''));

            if (! $this->option('force') && ! $this->confirm('Le promouvoir en super-admin principal ?', false)) {
                $this->info('Annulé — aucun changement.');

                return self::FAILURE;
            }
        }

        $user = $existant ?? new User();

        // Assignation directe : ces colonnes sont volontairement hors $fillable.
        $user->name              = $this->option('name') ?: ($existant->name ?? 'Super Admin BIMO-Tech');
        $user->email             = $email;
        $user->role              = UserRole::SuperAdmin->value;
        $user->agency_id         = null;   // accès global, hors agence
        $user->sa_est_principal  = true;   // admin principal : accès total, non filtré
        $user->email_verified_at = $user->email_verified_at ?? now();
        $user->must_change_password = false;

        if (filled($password)) {
            $user->password = $password;   // cast 'hashed' du modèle → bcrypt
        }

        $user->save();

        $this->info(($existant ? '✅ Super Admin mis à niveau : ' : '✅ Super Admin créé : ') . $user->email);
        $this->line("   rôle : {$user->role} · principal : " . ($user->sa_est_principal ? 'oui' : 'non'));
        $this->line('   Prochaine étape : se connecter puis enrôler la 2FA (obligatoire par défaut).');

        return self::SUCCESS;
    }
}
