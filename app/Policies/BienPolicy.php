<?php

namespace App\Policies;

use App\Models\Bien;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

/**
 * BienPolicy — Règles d'accès et d'action sur les biens immobiliers.
 *
 * Matrice des permissions :
 * ┌─────────────────┬────────────┬──────────┬──────────────┬────────────┐
 * │ Action          │ Superadmin │ Admin    │ Proprietaire │ Locataire  │
 * ├─────────────────┼────────────┼──────────┼──────────────┼────────────┤
 * │ viewAny (index) │ ✓ tout     │ ✓ agence │ ✓ ses biens  │ ✗          │
 * │ view (show)     │ ✓          │ ✓ agence │ ✓ ses biens  │ ✓ son bien │
 * │ create          │ ✓          │ ✓        │ ✗            │ ✗          │
 * │ update          │ ✓          │ ✓        │ ✗            │ ✗          │
 * │ delete          │ ✓          │ ✓        │ ✗            │ ✗          │
 * └─────────────────┴────────────┴──────────┴──────────────┴────────────┘
 */
class BienPolicy
{
    use HandlesAuthorization;

    /**
     * Le superadmin bypasse toutes les vérifications.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }

        return null; // Délègue aux méthodes ci-dessous
    }

    /**
     * Voir la liste des biens (index).
     */
    public function viewAny(User $user): bool
    {
        // Admins d'agence et propriétaires peuvent lister
        return in_array($user->role, ['admin', 'proprietaire']);
    }

    /**
     * Voir un bien spécifique (show).
     */
    public function view(User $user, Bien $bien): Response
    {
        // L'AgencyScope garantit déjà l'isolation agence.
        // On affine ici : un propriétaire ne voit que ses propres biens.
        if ($user->role === 'proprietaire') {
            return $user->id === $bien->proprietaire_id
                ? Response::allow()
                : Response::deny('Vous n\'êtes pas le propriétaire de ce bien.');
        }

        // Le locataire peut voir le bien associé à son contrat actif
        if ($user->role === 'locataire') {
            $contratActif = $user->locataire?->contrats()
                ->where('statut', 'actif')
                ->where('bien_id', $bien->id)
                ->exists();

            return $contratActif
                ? Response::allow()
                : Response::deny('Vous n\'avez pas accès à ce bien.');
        }

        // Admin de l'agence : accès complet à tous les biens de son agence
        return $user->agency_id === $bien->agency_id
            ? Response::allow()
            : Response::deny('Ce bien n\'appartient pas à votre agence.');
    }

    /**
     * Créer un nouveau bien.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Modifier un bien existant.
     */
    public function update(User $user, Bien $bien): Response
    {
        if ($user->role !== 'admin') {
            return Response::deny('Seul un administrateur d\'agence peut modifier un bien.');
        }

        return $user->agency_id === $bien->agency_id
            ? Response::allow()
            : Response::deny('Ce bien n\'appartient pas à votre agence.');
    }

    /**
     * Supprimer un bien.
     * Règle métier : un bien avec un contrat actif ne peut pas être supprimé.
     */
    public function delete(User $user, Bien $bien): Response
    {
        if ($user->role !== 'admin') {
            return Response::deny('Seul un administrateur peut supprimer un bien.');
        }

        if ($bien->contrats()->where('statut', 'actif')->exists()) {
            return Response::deny('Impossible de supprimer un bien avec un contrat actif.');
        }

        return $user->agency_id === $bien->agency_id
            ? Response::allow()
            : Response::deny('Ce bien n\'appartient pas à votre agence.');
    }

    /**
     * Uploader des photos sur un bien.
     */
    public function uploadPhotos(User $user, Bien $bien): Response
    {
        return $this->update($user, $bien);
    }
}