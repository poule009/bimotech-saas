<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    /**
     * Redirige l'utilisateur vers son dashboard selon son rôle
     * dès la connexion réussie.
     */
    public function index()
    {
       $user = auth()->user();

        return match($user->role) {
            'admin'        => redirect()->route('admin.dashboard'),
            'proprietaire' => redirect()->route('proprietaire.dashboard'),
            'locataire'    => redirect()->route('locataire.dashboard'),
            default        => redirect()->route('login'),
        };
    }
}



