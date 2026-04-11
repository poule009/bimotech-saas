<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

/**
 * PaiementPolicy — Règles d'accès sur les paiements de loyer.
 *
 * Sensibilité maximale : les paiements impliquent des montants réels,
 * des quittances légales et des rapports financiers.
 * Le locataire peut VOIR ses paiements mais jamais les créer/modifier.
 * Seul l'admin peut enregistrer et valider un paiement.
 */
class PaiementPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }

        return null;
    }

    /**
     * Voir la liste des paiements.
     */
    public function viewAny(User $user): bool
    {
        // Locataires : accès à leur propre historique via une route dédiée
        // Propriétaires : accès à l'historique de leurs biens
        return in_array($user->role, ['admin', 'proprietaire', 'locataire']);
    }

    /**
     * Voir un paiement spécifique.
     */
    public function view(User $user, Paiement $paiement): Response
    {
        return match ($user->role) {
            'admin' => $user->agency_id === $paiement->agency_id
                ? Response::allow()
                : Response::deny('Ce paiement n\'appartient pas à votre agence.'),

            'proprietaire' => $paiement->contrat?->bien?->proprietaire_id === $user->id
                ? Response::allow()
                : Response::deny('Ce paiement ne concerne pas l\'un de vos biens.'),

            'locataire' => $paiement->contrat?->locataire_id === $user->id
                ? Response::allow()
                : Response::deny('Ce paiement ne vous concerne pas.'),

            default => Response::deny('Accès refusé.'),
        };
    }

    /**
     * Enregistrer un nouveau paiement.
     * Action réservée à l'admin. Un locataire ne s'auto-valide pas.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Modifier un paiement existant.
     * Règle métier : un paiement 'validé' est immuable (quittance émise).
     */
    public function update(User $user, Paiement $paiement): Response
    {
        if ($user->role !== 'admin') {
            return Response::deny('Seul un administrateur peut modifier un paiement.');
        }

        if ($paiement->statut === 'valide') {
            return Response::deny('Un paiement validé avec quittance émise ne peut pas être modifié.');
        }

        return $user->agency_id === $paiement->agency_id
            ? Response::allow()
            : Response::deny('Ce paiement n\'appartient pas à votre agence.');
    }

    /**
     * Valider un paiement (déclenche la génération de quittance).
     */
    public function valider(User $user, Paiement $paiement): Response
    {
        if ($user->role !== 'admin') {
            return Response::deny('Seul un administrateur peut valider un paiement.');
        }

        if ($paiement->statut !== 'en_attente') {
            return Response::deny('Seul un paiement en attente peut être validé.');
        }

        return $user->agency_id === $paiement->agency_id
            ? Response::allow()
            : Response::deny('Accès refusé.');
    }

    /**
     * Télécharger une quittance PDF.
     * Le locataire peut télécharger SA quittance. Le proprio aussi.
     */
    public function telechargerQuittance(User $user, Paiement $paiement): Response
    {
        if ($paiement->statut !== 'valide') {
            return Response::deny('La quittance n\'est disponible que pour les paiements validés.');
        }

        return $this->view($user, $paiement);
    }

    /**
     * Supprimer un paiement (quasi-impossible en prod).
     */
    public function delete(User $user, Paiement $paiement): Response
    {
        if ($user->role !== 'admin') {
            return Response::deny('Action non autorisée.');
        }

        if ($paiement->statut === 'valide') {
            return Response::deny('Impossible de supprimer un paiement avec quittance émise.');
        }

        return $user->agency_id === $paiement->agency_id
            ? Response::allow()
            : Response::deny('Accès refusé.');
    }
}