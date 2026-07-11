<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Support\TeamAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // « Mes accès » = lecture seule des permissions déjà définies dans Mon équipe.
        // Directeur ou collaborateur « Administrateur » (tous droits) → bannière ;
        // sinon liste détaillée module par module.
        $niveaux = $user->role === 'admin' ? TeamAccess::niveauxUtilisateur($user) : [];
        $preset  = $user->is_owner ? 'directeur' : TeamAccess::detecterPreset($niveaux);
        $estAdminComplet = $user->is_owner || $preset === 'administrateur';

        $roleLabel = match ($preset) {
            'directeur'      => 'Directeur',
            'administrateur' => 'Administrateur',
            'secretaire'     => 'Secrétaire',
            'personnalise'   => 'Personnalisé',
            default          => ucfirst($user->role),
        };

        return view('profile.edit', [
            'user'            => $user,
            'roleLabel'       => $roleLabel,
            'estAdminComplet' => $estAdminComplet,
            'modules'         => TeamAccess::MODULES,
            'niveaux'         => $estAdminComplet ? [] : $niveaux,
        ]);
    }

    /** Préférences de notification (in-app uniquement). */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $request->user()->update([
            'notification_preferences' => [
                'alerte_retard'   => $request->boolean('alerte_retard'),
                'rappel_echeance' => $request->boolean('rappel_echeance'),
            ],
        ]);

        return Redirect::route('profile.edit')->with('status', 'notifications-updated');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Garde-fou : un superadmin (compte unique critique) ne peut pas s'auto-supprimer.
        if ($user->isSuperAdmin()) {
            return Redirect::route('profile.edit')
                ->with('error', 'Le compte superadmin ne peut pas être supprimé.');
        }

        // Garde-fou : le dernier directeur (is_owner) d'une agence ne peut pas s'auto-supprimer
        // sous peine de laisser l'agence orpheline (plus aucun responsable).
        if ($user->isOwner()) {
            $autresOwners = User::where('agency_id', $user->agency_id)
                ->where('is_owner', true)
                ->where('id', '!=', $user->id)
                ->exists();

            if (! $autresOwners) {
                return Redirect::route('profile.edit')
                    ->with('error', 'Vous êtes le dernier directeur de l\'agence : transférez d\'abord la direction à un collaborateur avant de supprimer votre compte.');
            }
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
