<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\User;

class PaiementPolicy
{
    // Voir la liste — admin voit tout, locataire voit les siens
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isLocataire() || $user->isProprietaire();
    }

    // Voir un paiement spécifique
    public function view(User $user, Paiement $paiement): bool
    {
        if ($user->isAdmin()) return true;

        // Locataire — ne voit que ses propres reçus
        if ($user->isLocataire()) {
            return $paiement->contrat->locataire_id === $user->id;
        }

        // Propriétaire — voit les paiements de ses biens
        if ($user->isProprietaire()) {
            return $paiement->contrat->bien->proprietaire_id === $user->id;
        }

        return false;
    }

    // Créer — admin seulement
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    // Modifier — admin seulement
    public function update(User $user, Paiement $paiement): bool
    {
        return $user->isAdmin();
    }

    // Supprimer — admin seulement
    public function delete(User $user, Paiement $paiement): bool
    {
        return $user->isAdmin();
    }

    // Télécharger PDF — admin, propriétaire du bien, locataire concerné
    public function downloadPdf(User $user, Paiement $paiement): bool
    {
        if ($user->isAdmin()) return true;

        if ($user->isLocataire()) {
            return $paiement->contrat->locataire_id === $user->id;
        }

        if ($user->isProprietaire()) {
            return $paiement->contrat->bien->proprietaire_id === $user->id;
        }

        return false;
    }
}