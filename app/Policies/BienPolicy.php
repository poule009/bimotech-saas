<?php

namespace App\Policies;

use App\Models\Bien;
use App\Models\User;

class BienPolicy
{
    /**
     * Règle métier : seul l'admin gère les biens (CRUD complet).
     * Le proprietaire peut uniquement CONSULTER ses propres biens.
     * Le superadmin gère la plateforme, pas les biens des agences.
     * → Pas de before() ici : chaque méthode est explicite.
     */

    /**
     * Voir la liste des biens.
     * Admin : voit tous les biens de son agence.
     * Proprietaire : voit ses propres biens (BienController::index() filtre par proprietaire_id).
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProprietaire();
    }

    /**
     * Voir le détail d'un bien.
     * Admin : tous les biens.
     * Proprietaire : uniquement ses propres biens.
     */
    public function view(User $user, Bien $bien): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isProprietaire()) return $bien->proprietaire_id === $user->id;

        return false;
    }

    /**
     * Créer un bien — admin uniquement.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Modifier un bien — admin uniquement.
     */
    public function update(User $user, Bien $bien): bool
    {
        return $user->isAdmin();
    }

    /**
     * Supprimer un bien — admin uniquement.
     */
    public function delete(User $user, Bien $bien): bool
    {
        return $user->isAdmin();
    }
}
