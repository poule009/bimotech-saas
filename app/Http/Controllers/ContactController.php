<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Mail;
 
class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'prenom'    => 'required|string|max:100',
            'nom'       => 'required|string|max:100',
            'agence'    => 'required|string|max:200',
            'email'     => 'required|email',
            'telephone' => 'nullable|string|max:20',
            'objet'     => 'required|string',
            'message'   => 'required|string|min:20|max:2000',
        ]);
 
        // Mail::to('contact@bimotech.sn')->send(new ContactMail($validated));
 
        return back()->with('success', 'Message envoyé !');
    }
}