<?php

namespace App\Enums;

/**
 * UserRole — Rôles utilisateur du SaaS BimoTech.
 *
 * Backed enum (string) + Stringable :
 *   - `$user->role`                    → string 'admin', 'superadmin'…
 *   - `{{ $user->role }}`             → Blade : affiche la valeur string
 *   - `$user->isSuperAdmin()`         → helper qui compare via ->value
 *   - `UserRole::from($user->role)`   → obtenir l'instance enum si besoin
 *   - `UserRole::Admin->value`        → 'admin' (comparaison type-safe)
 */
enum UserRole: string
{
    case Admin        = 'admin';
    case SuperAdmin   = 'superadmin';
    case Proprietaire = 'proprietaire';
    case Locataire    = 'locataire';

    /** Libellé lisible pour l'interface. */
    public function label(): string
    {
        return match($this) {
            self::Admin       => 'Administrateur',
            self::SuperAdmin  => 'Super-administrateur',
            self::Proprietaire => 'Propriétaire',
            self::Locataire   => 'Locataire',
        };
    }

    /** Vérifie si le rôle a des droits d'administration. */
    public function isStaff(): bool
    {
        return in_array($this, [self::Admin, self::SuperAdmin]);
    }
}
