<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'prenom'    => ['required', 'string', 'max:100'],
            'nom'       => ['required', 'string', 'max:100'],
            'agence'    => ['required', 'string', 'max:200'],
            'email'     => ['required', 'email'],
            'telephone' => ['required', 'string', 'max:20'],
            'objet'     => ['required', 'in:demo,tarif,technique,reseau,autre'],
            'message'   => ['required', 'string', 'max:2000'],
        ]);

        Mail::to('contact@bimotech.sn')->send(new ContactMail($validated));

        return back()->with('success', 'Votre message a bien été envoyé. Nous vous répondrons sous 24h.');
    }
}
