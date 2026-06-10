<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
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
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
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
