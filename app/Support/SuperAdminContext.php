<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Contexte Super Admin — résout le PÉRIMÈTRE de données de l'utilisateur courant.
 *
 * Règle centrale du module « Équipe interne » (brief) :
 *  - Admin PRINCIPAL (Malick) : voit toute la plateforme (périmètre = null).
 *  - COLLABORATEUR restreint : ne voit que ses agences apportées (amenee_par = lui).
 *  - « Voir comme [collaborateur] » : l'admin principal bascule temporairement sur
 *    le périmètre filtré d'un collaborateur, pour vérification.
 *
 * Le périmètre résolu ici est appliqué automatiquement par le scope global
 * d'Agency — aucun écran n'a donc à « penser » au filtrage.
 */
class SuperAdminContext
{
    /** Clé de session portant l'id du collaborateur observé en « Voir comme ». */
    public const VIEW_AS_KEY = 'sa_view_as_id';

    /** Cache mémoire du collaborateur observé (évite de recharger à chaque requête Eloquent). */
    private ?User $viewAsCache = null;

    private bool $viewAsResolved = false;

    /**
     * Id de l'admin dont on doit filtrer les agences (amenee_par).
     * null = accès plateforme complet (principal sans « Voir comme »).
     */
    public function perimetreAdminId(): ?int
    {
        $user = Auth::user();

        if (! $user instanceof User || ! $user->isSuperAdmin()) {
            // Hors super-admin : pas de filtrage de périmètre (les autres rôles sont
            // cloisonnés ailleurs, par AgencyScope sur leurs propres modèles).
            return null;
        }

        // Admin principal : périmètre complet, sauf s'il observe un collaborateur.
        if ($user->estSuperAdminPrincipal()) {
            return $this->collaborateurObserve()?->id;
        }

        // Collaborateur restreint : toujours limité à ses propres agences.
        return $user->id;
    }

    /** Le super-admin dont le périmètre est effectivement affiché (principal, observé, ou soi-même). */
    public function collaborateurEffectif(): ?User
    {
        $id = $this->perimetreAdminId();

        return $id ? User::find($id) : null;
    }

    /** Contexte restreint : l'utilisateur ne voit qu'un périmètre d'agences (≠ plateforme). */
    public function estRestreint(): bool
    {
        return $this->perimetreAdminId() !== null;
    }

    /**
     * Ids des agences du périmètre courant — pour scoper les modèles qui ne sont pas
     * des Agency (sessions d'impersonation, journaux) selon l'asymétrie de visibilité.
     * null = plateforme complète (aucun filtre à appliquer).
     *
     * @return array<int>|null
     */
    public function perimetreAgencyIds(): ?array
    {
        if (! $this->estRestreint()) {
            return null;
        }

        // Agency est déjà filtré par le scope global de périmètre → ces ids sont
        // exactement les agences du collaborateur observé.
        return \App\Models\Agency::pluck('id')->all();
    }

    // ── « Voir comme » (réservé à l'admin principal) ────────────────────────

    /** Le collaborateur actuellement observé via « Voir comme », le cas échéant. */
    public function collaborateurObserve(): ?User
    {
        if ($this->viewAsResolved) {
            return $this->viewAsCache;
        }
        $this->viewAsResolved = true;

        $id = Session::get(self::VIEW_AS_KEY);
        if (! $id) {
            return $this->viewAsCache = null;
        }

        // Sécurité : seul l'admin principal peut observer, et seulement un collaborateur valide.
        $observateur = Auth::user();
        if (! $observateur instanceof User || ! $observateur->estSuperAdminPrincipal()) {
            return $this->viewAsCache = null;
        }

        $cible = User::collaborateursSa()->find($id);

        return $this->viewAsCache = ($cible && ! $cible->saAccesRevoque()) ? $cible : null;
    }

    public function enModeVoirComme(): bool
    {
        return $this->collaborateurObserve() !== null;
    }

    public function demarrerVoirComme(User $collaborateur): void
    {
        Session::put(self::VIEW_AS_KEY, $collaborateur->id);
        $this->viewAsResolved = false;
    }

    public function arreterVoirComme(): void
    {
        Session::forget(self::VIEW_AS_KEY);
        $this->viewAsResolved = false;
        $this->viewAsCache = null;
    }

    // ── Autorisations de sections (nav + gardes) ────────────────────────────

    /**
     * L'utilisateur courant peut-il accéder à une section du Super Admin ?
     *
     * Le principal passe partout. Un collaborateur restreint dépend de ses toggles ;
     * les sections de gouvernance (équipe, paramètres) restent réservées au principal.
     */
    public function peutVoirSection(string $section): bool
    {
        $user = Auth::user();
        if (! $user instanceof User || ! $user->isSuperAdmin()) {
            return false;
        }

        // En « Voir comme », on réplique fidèlement la vue du collaborateur observé
        // (mêmes sections visibles/accessibles que lui) — le principal ne doit pas
        // voir plus que ce que verrait réellement le collaborateur qu'il vérifie.
        $acteur = $this->collaborateurObserve() ?? $user;

        if ($acteur->estSuperAdminPrincipal()) {
            return true;
        }

        return match ($section) {
            'dashboard', 'agences', 'support' => $acteur->saPermission('voir_agences'),
            'facturation'                     => $acteur->saPermission('facturation'),
            'regles'                          => $acteur->saPermission('regles_fiscales'),
            // Gouvernance de la plateforme : principal uniquement.
            'equipe', 'parametres'            => false,
            default                           => false,
        };
    }
}
