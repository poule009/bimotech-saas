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
            'rccm'             => ['nullable', 'string', 'max:50'],
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
            'logo'             => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'signature'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:1024'],
            'modele_contrat'   => ['nullable', 'string', 'max:10000'],
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
        // Upload avant la mise à jour DB. En cas d'échec DB, on supprime le nouveau
        // fichier et on restaure l'ancien chemin (évite les fichiers orphelins).

        $logoPath          = $agency->logo_path;
        $newLogoUploaded   = null;
        $oldLogoToDelete   = null;

        if ($request->hasFile('logo')) {
            $newLogoUploaded = $request->file('logo')->store('logos', 'public');
            $oldLogoToDelete = $logoPath;
            $logoPath        = $newLogoUploaded;
        }

        // ── Gestion de la signature ───────────────────────────────────────

        $signaturePath           = $agency->signature_path;
        $newSignatureUploaded    = null;
        $oldSignatureToDelete    = null;

        if ($request->hasFile('signature')) {
            $newSignatureUploaded = $request->file('signature')->store('signatures', 'public');
            $oldSignatureToDelete = $signaturePath;
            $signaturePath        = $newSignatureUploaded;
        }

        // ── Mise à jour de l'agence ───────────────────────────────────────
        // Strip tags sur modele_contrat pour éviter le XSS si rendu sans échappement dans les PDFs

        try {
            $agency->update([
                'name'             => $request->name,
                'email'            => $request->email,
                'telephone'        => $request->telephone,
                'adresse'          => $request->adresse,
                'ninea'            => $request->ninea,
                'rccm'             => $request->rccm,
                'couleur_primaire' => $request->couleur_primaire ?? $agency->couleur_primaire,
                'logo_path'        => $logoPath,
                'signature_path'   => $signaturePath,
                'modele_contrat'   => $request->modele_contrat
                    ? strip_tags($request->modele_contrat)
                    : $agency->modele_contrat,
            ]);
        } catch (\Throwable $e) {
            // DB a échoué → supprimer les nouveaux fichiers uploadés (évite les orphelins)
            if ($newLogoUploaded) {
                Storage::disk('public')->delete($newLogoUploaded);
            }
            if ($newSignatureUploaded) {
                Storage::disk('public')->delete($newSignatureUploaded);
            }
            throw $e;
        }

        // DB OK → supprimer les anciens fichiers remplacés
        if ($oldLogoToDelete && Storage::disk('public')->exists($oldLogoToDelete)) {
            Storage::disk('public')->delete($oldLogoToDelete);
        }
        if ($oldSignatureToDelete && Storage::disk('public')->exists($oldSignatureToDelete)) {
            Storage::disk('public')->delete($oldSignatureToDelete);
        }

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