<?php

namespace App\Policies;

use App\Models\Contrat;
use App\Models\User;

class ContratPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProprietaire() || $user->isLocataire();
    }

    public function view(User $user, Contrat $contrat): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isProprietaire()) return $contrat->bien->proprietaire_id === $user->id;
        if ($user->isLocataire())   return $contrat->locataire_id === $user->id;
        return false;
    }

    public function create(User $user): bool  { return $user->isAdmin(); }
    public function update(User $user, Contrat $contrat): bool { return $user->isAdmin(); }
    public function delete(User $user, Contrat $contrat): bool { return $user->isAdmin(); }
}