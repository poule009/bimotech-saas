<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    /**
     * L'inscription individuelle est désactivée dans ce SaaS.
     * Les agences s'inscrivent via /register/agency (AgencyRegistrationController).
     * Les utilisateurs (locataires, propriétaires) sont créés par les admins.
     */
    public function create(): never
    {
        abort(404);
    }

    public function store(Request $request): never
    {
        abort(404);
    }
}
