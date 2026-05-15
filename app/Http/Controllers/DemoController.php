<?php

namespace App\Http\Controllers;

use App\Mail\DemoRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DemoController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'prenom'    => 'required|string|max:100',
            'nom'       => 'required|string|max:100',
            'agence'    => 'required|string|max:200',
            'telephone' => 'required|string|max:20',
            'email'     => 'required|email',
            'nb_biens'  => 'nullable|string',
            'ville'     => 'nullable|string',
        ]);

        try {
            Mail::to('contact@bimotech.sn')->send(new DemoRequestMail($validated));
        } catch (\Throwable $e) {
            Log::error('DemoRequestMail failed', [
                'email'   => $validated['email'],
                'message' => $e->getMessage(),
            ]);
        }

        return redirect()->route('demo')->with('success', 'Demande reçue !');
    }
}
