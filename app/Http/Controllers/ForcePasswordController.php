<?php

namespace App\Http\Controllers;

use App\Support\PasswordPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Écran de changement de mot de passe obligatoire à la 1ʳᵉ connexion d'un
 * collaborateur invité (drapeau must_change_password).
 */
class ForcePasswordController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        if (! Auth::user()->must_change_password) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.force-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', PasswordPolicy::rules()],
        ], [
            'password.required'  => 'Choisissez un nouveau mot de passe.',
            'password.confirmed' => 'La confirmation ne correspond pas.',
        ]);

        $user = Auth::user();
        $user->password             = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Votre mot de passe a été mis à jour. Bienvenue !');
    }
}
