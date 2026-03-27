<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        return match ($user->role) {
            'superadmin'    => redirect()->route('superadmin.dashboard'),
            'admin'         => redirect()->route('admin.dashboard'),
            'proprietaire'  => redirect()->route('proprietaire.dashboard'),
            'locataire'     => redirect()->route('locataire.dashboard'),
            default         => redirect()->route('login'),
        };
    }
}
// ```

// ---

// ## Test

// Connecte-toi avec le superadmin :

// **Email :** `superadmin@bimotech.sn`
// **Mot de passe :** `SuperAdmin@2025!`

// Tu dois être redirigé automatiquement vers :
// ```
// http://localhost:8000/superadmin/dashboard