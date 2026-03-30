<?php

namespace App\Policies;

use App\Models\Contrat;
use App\Models\User;

class ContratPolicy
{
    /**
     * BUG 2 FIX : le superadmin bypasse toutes les vérifications de policy.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * BUG 7 FIX : suppression du code mort pour proprietaire et locataire.
     * Toutes les routes contrats sont sous middleware isAdmin → seul admin/superadmin y accède.
     * Le superadmin est géré par before() ci-dessus.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Contrat $contrat): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Contrat $contrat): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Contrat $contrat): bool
    {
        return $user->isAdmin();
    }
}
