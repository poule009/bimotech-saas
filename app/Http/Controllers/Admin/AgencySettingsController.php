<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AgencySettingsController extends Controller
{
    // ── Affiche le formulaire des paramètres ──────────────────────────────

    public function edit(): View
    {
        $agency = Auth::user()->agency;

        return view('admin.agency-settings', compact('agency'));
    }

    // ── Sauvegarde les paramètres ─────────────────────────────────────────

    public function update(Request $request): RedirectResponse
    {
        $agency = Auth::user()->agency;

        $request->validate([
            'name'             => ['required', 'string', 'min:2', 'max:100'],
            'email'            => ['required', 'email', 'max:255', 'unique:agencies,email,' . $agency->id],
            'telephone'        => ['nullable', 'string', 'max:20'],
            'adresse'          => ['nullable', 'string', 'max:255'],
            'ninea'            => ['nullable', 'string', 'max:30'],
            'couleur_primaire' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],

            /**
             * SÉCURITÉ — Upload logo :
             *
             * SVG intentionnellement retiré des formats acceptés.
             * Un fichier SVG est du XML et peut contenir du JavaScript (<script>),
             * des appels externes (xlink:href), ou des attaques XSS.
             * Même avec une validation mimes:svg, Laravel ne parse pas le contenu
             * du fichier — il se fie uniquement à l'extension et au MIME type,
             * qui peuvent être falsifiés.
             *
             * Formats sûrs acceptés : PNG, JPG, JPEG, WEBP (formats raster)
             */
            'logo'      => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:1024'],
        ], [
            'name.required'          => "Le nom de l'agence est obligatoire.",
            'email.required'         => "L'email est obligatoire.",
            'email.unique'           => "Cet email est déjà utilisé par une autre agence.",
            'ninea.max'              => "Le NINEA ne doit pas dépasser 30 caractères.",
            'couleur_primaire.regex' => "La couleur doit être un code hexadécimal valide (ex: #1a3c5e).",
            'logo.image'             => "Le fichier doit être une image.",
            'logo.mimes'             => "Formats acceptés : PNG, JPG, JPEG, WEBP.",
            'logo.max'               => "Le logo ne doit pas dépasser 2 Mo.",
            'signature.image'        => "La signature doit être une image.",
            'signature.mimes'        => "Formats acceptés : PNG, JPG, JPEG, WEBP.",
            'signature.max'          => "La signature ne doit pas dépasser 1 Mo.",
        ]);

        // ── Gestion du logo ───────────────────────────────────────────────

        $logoPath = $agency->logo_path;

        if ($request->hasFile('logo')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        // ── Gestion de la signature ───────────────────────────────────────

        $signaturePath = $agency->signature_path;

        if ($request->hasFile('signature')) {
            if ($signaturePath && Storage::disk('public')->exists($signaturePath)) {
                Storage::disk('public')->delete($signaturePath);
            }
            $signaturePath = $request->file('signature')->store('signatures', 'public');
        }

        // ── Mise à jour de l'agence ───────────────────────────────────────

        $agency->update([
            'name'             => $request->name,
            'email'            => $request->email,
            'telephone'        => $request->telephone,
            'adresse'          => $request->adresse,
            'ninea'            => $request->ninea,
            'couleur_primaire' => $request->couleur_primaire ?? $agency->couleur_primaire,
            'logo_path'        => $logoPath,
            'signature_path'   => $signaturePath,
        ]);

        $agency->refresh();
        $agency->checkOnboarding();

        return redirect()
            ->route('admin.agency.settings')
            ->with('success', "Paramètres de l'agence mis à jour ✓");
    }

    // ── Supprime le logo ──────────────────────────────────────────────────

    public function deleteLogo(): RedirectResponse
    {
        $agency = Auth::user()->agency;

        if ($agency->logo_path && Storage::disk('public')->exists($agency->logo_path)) {
            Storage::disk('public')->delete($agency->logo_path);
        }

        $agency->update(['logo_path' => null]);

        return redirect()
            ->route('admin.agency.settings')
            ->with('success', 'Logo supprimé ✓');
    }

    // ── Supprime la signature ─────────────────────────────────────────────

    public function deleteSignature(): RedirectResponse
    {
        $agency = Auth::user()->agency;

        if ($agency->signature_path && Storage::disk('public')->exists($agency->signature_path)) {
            Storage::disk('public')->delete($agency->signature_path);
        }

        $agency->update(['signature_path' => null]);

        return redirect()
            ->route('admin.agency.settings')
            ->with('success', 'Signature supprimée ✓');
    }
}