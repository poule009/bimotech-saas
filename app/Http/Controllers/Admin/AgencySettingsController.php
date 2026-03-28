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
            'couleur_primaire' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo'             => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ], [
            'name.required'          => "Le nom de l'agence est obligatoire.",
            'email.required'         => "L'email est obligatoire.",
            'email.unique'           => "Cet email est déjà utilisé par une autre agence.",
            'couleur_primaire.regex' => "La couleur doit être un code hexadécimal valide (ex: #1a3c5e).",
            'logo.image'             => "Le fichier doit être une image.",
            'logo.mimes'             => "Formats acceptés : PNG, JPG, JPEG, SVG, WEBP.",
            'logo.max'               => "Le logo ne doit pas dépasser 2 Mo.",
        ]);

        // ── Gestion du logo ───────────────────────────────────────────────

        $logoPath = $agency->logo_path;

        if ($request->hasFile('logo')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        // ── Mise à jour de l'agence ───────────────────────────────────────

        $agency->update([
            'name'             => $request->name,
            'email'            => $request->email,
            'telephone'        => $request->telephone,
            'adresse'          => $request->adresse,
            'couleur_primaire' => $request->couleur_primaire ?? $agency->couleur_primaire,
            'logo_path'        => $logoPath,
        ]);

        return redirect()
            ->route('admin.agency.settings')
            ->with('success', "Paramètres de l'agence mis à jour avec succès ✓");
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
            ->with('success', 'Logo supprimé avec succès ✓');
    }
}