<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class AgencySettingsController extends Controller
{
    /** Disque utilisé pour les fichiers d'agence (logo + signature). */
    private const DISK = 'public';

    // ── Affiche le formulaire des paramètres ──────────────────────────────

    public function edit(): View
    {
        abort_unless(Auth::user()->isOwner() || Auth::user()->isSuperAdmin(), 403);

        $agency = Auth::user()->agency;

        return view('admin.agency-settings', compact('agency'));
    }

    // ── Sauvegarde les paramètres ─────────────────────────────────────────

    public function update(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->isOwner() || Auth::user()->isSuperAdmin(), 403);

        $agency = Auth::user()->agency;

        $validated = $request->validate([
            'name'             => ['required', 'string', 'min:2', 'max:100'],
            'email'            => ['required', 'email', 'max:255', 'unique:agencies,email,' . $agency->id],
            'telephone'        => ['nullable', 'string', 'max:20'],
            'whatsapp'         => ['nullable', 'string', 'max:20', 'regex:/^\+[0-9]{7,15}$/'],
            'adresse'          => ['nullable', 'string', 'max:255'],
            'ninea'            => ['nullable', 'string', 'max:30'],
            'rccm'             => ['nullable', 'string', 'max:50'],
            'couleur_primaire' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],

            /**
             * SÉCURITÉ — Upload logo / signature :
             *
             * SVG intentionnellement retiré des formats acceptés. Un fichier SVG
             * est du XML et peut contenir du JavaScript (<script>), des appels
             * externes (xlink:href), ou des attaques XSS. Même avec une validation
             * mimes:svg, Laravel ne parse pas le contenu du fichier — il se fie
             * uniquement à l'extension et au MIME type, qui peuvent être falsifiés.
             *
             * Formats sûrs acceptés : PNG, JPG, JPEG, WEBP (formats raster).
             */
            'logo'             => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'logo_dark'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'signature'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:1024'],
            'modele_contrat'   => ['nullable', 'string', 'max:10000'],
        ], [
            'name.required'          => "Le nom de l'agence est obligatoire.",
            'name.min'               => "Le nom doit contenir au moins 2 caractères.",
            'name.max'               => "Le nom ne doit pas dépasser 100 caractères.",
            'email.required'         => "L'email est obligatoire.",
            'email.email'            => "L'email n'est pas valide.",
            'email.unique'           => "Cet email est déjà utilisé par une autre agence.",
            'telephone.max'          => "Le téléphone ne doit pas dépasser 20 caractères.",
            'whatsapp.regex'         => "Format attendu : +221771234567 (commencer par + puis les chiffres, sans espaces).",
            'whatsapp.max'           => "Le numéro WhatsApp ne doit pas dépasser 20 caractères.",
            'adresse.max'            => "L'adresse ne doit pas dépasser 255 caractères.",
            'ninea.max'              => "Le NINEA ne doit pas dépasser 30 caractères.",
            'rccm.max'               => "Le RCCM ne doit pas dépasser 50 caractères.",
            'couleur_primaire.regex' => "La couleur doit être un code hexadécimal valide (ex: #1a3c5e).",
            'logo.image'             => "Le logo doit être une image.",
            'logo.mimes'             => "Formats acceptés pour le logo : PNG, JPG, JPEG, WEBP.",
            'logo.max'               => "Le logo ne doit pas dépasser 2 Mo.",
            'signature.image'        => "La signature doit être une image.",
            'signature.mimes'        => "Formats acceptés pour la signature : PNG, JPG, JPEG, WEBP.",
            'signature.max'          => "La signature ne doit pas dépasser 1 Mo.",
            'modele_contrat.max'     => "Le modèle de contrat ne doit pas dépasser 10 000 caractères.",
        ]);

        // ── Upload des fichiers AVANT la transaction DB ───────────────────
        // Si l'écriture DB échoue, on supprime les nouveaux fichiers uploadés
        // pour éviter les orphelins.

        $logoPath             = $agency->logo_path;
        $logoDarkPath         = $agency->logo_dark_path;
        $signaturePath        = $agency->signature_path;
        $newLogoUploaded      = null;
        $newLogoDarkUploaded  = null;
        $newSignatureUploaded = null;
        $oldLogoToDelete      = null;
        $oldLogoDarkToDelete  = null;
        $oldSignatureToDelete = null;

        if ($request->hasFile('logo')) {
            $newLogoUploaded = $request->file('logo')->store('logos', self::DISK);
            $oldLogoToDelete = $logoPath;
            $logoPath        = $newLogoUploaded;
        }

        if ($request->hasFile('logo_dark')) {
            $newLogoDarkUploaded = $request->file('logo_dark')->store('logos', self::DISK);
            $oldLogoDarkToDelete = $logoDarkPath;
            $logoDarkPath        = $newLogoDarkUploaded;
        }

        if ($request->hasFile('signature')) {
            $newSignatureUploaded = $request->file('signature')->store('signatures', self::DISK);
            $oldSignatureToDelete = $signaturePath;
            $signaturePath        = $newSignatureUploaded;
        }

        // ── Écriture DB sous transaction ──────────────────────────────────

        try {
            DB::transaction(function () use ($agency, $validated, $logoPath, $logoDarkPath, $signaturePath) {
                $agency->update([
                    'name'             => $validated['name'],
                    'email'            => $validated['email'],
                    'telephone'        => $validated['telephone'] ?? null,
                    'whatsapp'         => $validated['whatsapp']  ?? null,
                    'adresse'          => $validated['adresse']   ?? null,
                    'ninea'            => $validated['ninea']     ?? null,
                    'rccm'             => $validated['rccm']      ?? null,
                    'couleur_primaire' => $validated['couleur_primaire'] ?? $agency->couleur_primaire,
                    'logo_path'        => $logoPath,
                    'logo_dark_path'   => $logoDarkPath,
                    'signature_path'   => $signaturePath,
                    // array_key_exists (pas isset) car le champ peut valoir null (textarea vide
                    // converti par ConvertEmptyStringsToNull). isset(null) retournerait false
                    // et la valeur ne serait jamais mise à jour.
                    // strip_tags() retiré : le champ est rendu avec {{ }} (htmlspecialchars),
                    // donc aucun risque XSS. strip_tags() corrompait le contenu contenant < ou >.
                    'modele_contrat'   => array_key_exists('modele_contrat', $validated)
                        ? $validated['modele_contrat']
                        : $agency->modele_contrat,
                ]);
            });
        } catch (Throwable $e) {
            $this->safeDelete($newLogoUploaded);
            $this->safeDelete($newLogoDarkUploaded);
            $this->safeDelete($newSignatureUploaded);

            Log::error('Mise à jour paramètres agence échouée', [
                'agency_id' => $agency->id,
                'message'   => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', "La mise à jour a échoué. Veuillez réessayer dans quelques instants.");
        }

        // DB OK → suppression des anciens fichiers remplacés (best-effort, non bloquant).
        $this->safeDelete($oldLogoToDelete);
        $this->safeDelete($oldLogoDarkToDelete);
        $this->safeDelete($oldSignatureToDelete);

        $agency->refresh();
        $agency->checkOnboarding();

        return redirect()
            ->route('admin.agency.settings')
            ->with('success', "Paramètres de l'agence mis à jour ✓");
    }

    // ── Supprime le logo ──────────────────────────────────────────────────
    // Ordre : DB d'abord, disque ensuite.
    // Rationale : si la DB rollback, le fichier reste (orphelin tolérable).
    // L'inverse créerait un pointeur DB vers un fichier inexistant (erreur visible).

    public function deleteLogo(): RedirectResponse
    {
        $agency  = Auth::user()->agency;
        $oldPath = $agency->logo_path;

        if (! $oldPath) {
            return redirect()->route('admin.agency.settings');
        }

        try {
            DB::transaction(fn () => $agency->update(['logo_path' => null]));
        } catch (Throwable $e) {
            Log::error('Suppression logo échouée', [
                'agency_id' => $agency->id,
                'message'   => $e->getMessage(),
            ]);

            return back()->with('error', "La suppression du logo a échoué.");
        }

        $this->safeDelete($oldPath);

        return redirect()
            ->route('admin.agency.settings')
            ->with('success', 'Logo supprimé ✓');
    }

    // ── Supprime la signature ─────────────────────────────────────────────

    public function deleteSignature(): RedirectResponse
    {
        $agency  = Auth::user()->agency;
        $oldPath = $agency->signature_path;

        if (! $oldPath) {
            return redirect()->route('admin.agency.settings');
        }

        try {
            DB::transaction(fn () => $agency->update(['signature_path' => null]));
        } catch (Throwable $e) {
            Log::error('Suppression signature échouée', [
                'agency_id' => $agency->id,
                'message'   => $e->getMessage(),
            ]);

            return back()->with('error', "La suppression de la signature a échoué.");
        }

        $this->safeDelete($oldPath);

        return redirect()
            ->route('admin.agency.settings')
            ->with('success', 'Signature supprimée ✓');
    }

    // ── Supprime le logo fond sombre ─────────────────────────────────────

    public function deleteLogoDark(): RedirectResponse
    {
        $agency  = Auth::user()->agency;
        $oldPath = $agency->logo_dark_path;

        if (! $oldPath) {
            return redirect()->route('admin.agency.settings');
        }

        try {
            DB::transaction(fn () => $agency->update(['logo_dark_path' => null]));
        } catch (Throwable) {
            return back()->with('error', "La suppression du logo fond sombre a échoué.");
        }

        $this->safeDelete($oldPath);

        return redirect()
            ->route('admin.agency.settings')
            ->with('success', 'Logo fond sombre supprimé ✓');
    }

    // ── Masque la checklist d'onboarding ─────────────────────────────────

    public function dismissOnboarding(): RedirectResponse
    {
        $agency = Auth::user()->agency;
        if ($agency) {
            $agency->update(['onboarding_completed' => true]);
        }
        return redirect()->route('admin.dashboard');
    }

    /**
     * Supprime un fichier du disque public s'il existe.
     * Best-effort : n'échoue jamais (un fichier orphelin est tolérable, une
     * exception au milieu d'un workflow ne l'est pas).
     */
    private function safeDelete(?string $path): void
    {
        if (! $path) {
            return;
        }

        try {
            if (Storage::disk(self::DISK)->exists($path)) {
                Storage::disk(self::DISK)->delete($path);
            }
        } catch (Throwable $e) {
            Log::warning('Suppression fichier échouée (orphelin possible)', [
                'path'    => $path,
                'message' => $e->getMessage(),
            ]);
        }
    }
}