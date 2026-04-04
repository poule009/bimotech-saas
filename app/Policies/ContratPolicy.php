<?php

namespace App\Policies;

use App\Models\Contrat;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

/**
 * ContratPolicy — Règles d'accès sur les contrats de bail (loi 81-18).
 *
 * Sensibilité haute : un contrat contient les montants, la durée du bail,
 * les clauses de résiliation. L'accès locataire est strictement limité
 * au contrat dont il est partie prenante.
 */
class ContratPolicy
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
     * Voir la liste des contrats.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'proprietaire']);
    }

    /**
     * Voir un contrat spécifique.
     */
    public function view(User $user, Contrat $contrat): Response
    {
        return match ($user->role) {
            'admin' => $user->agency_id === $contrat->agency_id
                ? Response::allow()
                : Response::deny('Ce contrat n\'appartient pas à votre agence.'),

            'proprietaire' => $contrat->bien?->proprietaire_id === $user->id
                ? Response::allow()
                : Response::deny('Vous n\'êtes pas le propriétaire du bien concerné.'),

            'locataire' => $contrat->locataire_id === $user->locataire?->id
                ? Response::allow()
                : Response::deny('Ce contrat ne vous concerne pas.'),

            default => Response::deny('Accès refusé.'),
        };
    }

    /**
     * Créer un contrat.
     * Seul l'admin crée des contrats. Le locataire et le proprio ne peuvent pas.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Modifier un contrat.
     * Règle métier : un contrat avec statut 'actif' ne peut être modifié
     * que par l'admin. Un contrat 'résilié' est en lecture seule.
     */
    public function update(User $user, Contrat $contrat): Response
    {
        if ($user->role !== 'admin') {
            return Response::deny('Seul un administrateur peut modifier un contrat.');
        }

        if ($contrat->statut === 'resilie') {
            return Response::deny('Un contrat résilié ne peut plus être modifié.');
        }

        return $user->agency_id === $contrat->agency_id
            ? Response::allow()
            : Response::deny('Ce contrat n\'appartient pas à votre agence.');
    }

    /**
     * Résilier un contrat (action distincte de la suppression).
     */
    public function resilier(User $user, Contrat $contrat): Response
    {
        if ($user->role !== 'admin') {
            return Response::deny('Seul un administrateur peut résilier un contrat.');
        }

        if ($contrat->statut !== 'actif') {
            return Response::deny('Seul un contrat actif peut être résilié.');
        }

        return $user->agency_id === $contrat->agency_id
            ? Response::allow()
            : Response::deny('Ce contrat n\'appartient pas à votre agence.');
    }

    /**
     * Supprimer définitivement un contrat (rare, historique protégé).
     */
    public function delete(User $user, Contrat $contrat): Response
    {
        if ($user->role !== 'admin') {
            return Response::deny('Action non autorisée.');
        }

        // Un contrat actif ou résilié ne doit jamais être supprimé (historique légal)
        if (in_array($contrat->statut, ['actif', 'resilie'])) {
            return Response::deny('Les contrats actifs ou résiliés ne peuvent pas être supprimés (obligation légale de conservation).');
        }

        return $user->agency_id === $contrat->agency_id
            ? Response::allow()
            : Response::deny('Accès refusé.');
    }
}