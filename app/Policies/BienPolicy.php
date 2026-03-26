<?php

namespace App\Policies;

use App\Models\Bien;
use App\Models\User;

class BienPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Tous les connectés voient la liste
    }

    public function view(User $user, Bien $bien): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isProprietaire()) return $bien->proprietaire_id === $user->id;

        // Locataire — voit seulement le bien qu'il loue
        if ($user->isLocataire()) {
            return $bien->contratActif?->locataire_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isProprietaire();
    }

    public function update(User $user, Bien $bien): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isProprietaire() && $bien->proprietaire_id === $user->id;
    }

    public function delete(User $user, Bien $bien): bool
    {
        return $user->isAdmin();
    }
}