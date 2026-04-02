<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\User;

class PaiementPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    // ✅ CORRECTION M5 : on ne recharge la relation que si elle n'est pas déjà en mémoire
    public function view(User $user, Paiement $paiement): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isLocataire()) {
            $this->chargerContrat($paiement);
            return $paiement->contrat?->locataire_id === $user->id;
        }

        if ($user->isProprietaire()) {
            $this->chargerContratAvecBien($paiement);
            return $paiement->contrat?->bien?->proprietaire_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Paiement $paiement): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Paiement $paiement): bool
    {
        return $user->isAdmin();
    }

    public function downloadPdf(User $user, Paiement $paiement): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isLocataire()) {
            $this->chargerContrat($paiement);
            return $paiement->contrat?->locataire_id === $user->id;
        }

        if ($user->isProprietaire()) {
            $this->chargerContratAvecBien($paiement);
            return $paiement->contrat?->bien?->proprietaire_id === $user->id;
        }

        return false;
    }

    // Charge le contrat seulement s'il n'est pas déjà en mémoire
    private function chargerContrat(Paiement $paiement): void
    {
        if (! $paiement->relationLoaded('contrat')) {
            $paiement->load('contrat:id,locataire_id,bien_id');
        }
    }

    // Charge le contrat + le bien seulement si nécessaire
    private function chargerContratAvecBien(Paiement $paiement): void
    {
        if (! $paiement->relationLoaded('contrat')) {
            $paiement->load([
                'contrat:id,locataire_id,bien_id',
                'contrat.bien:id,proprietaire_id',
            ]);
        } elseif (! $paiement->contrat->relationLoaded('bien')) {
            $paiement->contrat->load('bien:id,proprietaire_id');
        }
    }
}