<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\BienPhoto;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BienPhotoController extends Controller
{
    use AuthorizesRequests;

    // ── Upload de photos ──────────────────────────────────────────────────

    public function store(Request $request, Bien $bien): RedirectResponse
    {
        $this->authorize('update', $bien);

        $request->validate([
            'photos'   => ['required', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'photos.*.image' => 'Chaque fichier doit être une image.',
            'photos.*.mimes' => 'Formats acceptés : JPG, PNG, WEBP.',
            'photos.*.max'   => 'Chaque image ne doit pas dépasser 3 MB.',
        ]);

        // NOTE SÉCURITÉ : SVG intentionnellement exclu car il peut contenir
        // du JavaScript (attaque XSS). On n'accepte que les formats raster.

        $ordre = $bien->photos()->max('ordre') ?? 0;

        foreach ($request->file('photos') as $fichier) {
            $chemin = $fichier->store('biens/' . $bien->id, 'public');

            $estPremiere = $bien->photos()->count() === 0;

            BienPhoto::create([
                'bien_id'        => $bien->id,
                'chemin'         => $chemin,
                'nom_original'   => $fichier->getClientOriginalName(),
                'est_principale' => $estPremiere,
                'ordre'          => ++$ordre,
            ]);
        }

        return back()->with('success',
            count($request->file('photos')) . ' photo(s) ajoutée(s) ✓'
        );
    }

    // ── Suppression d'une photo ───────────────────────────────────────────

    public function destroy(Bien $bien, BienPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $bien);

        /**
         * SÉCURITÉ — Vérification d'appartenance :
         *
         * Sans cette vérification, un admin malveillant pourrait appeler :
         *   DELETE /biens/1/photos/99
         * où la photo 99 appartient au bien 5 d'une autre agence.
         * Laravel injecterait quand même la photo 99 sans erreur.
         *
         * findOrFail() sur la relation garantit que la photo appartient
         * bien à CE bien (et donc à cette agence, via AgencyScope sur Bien).
         */
        $photo = $bien->photos()->findOrFail($photo->id);

        Storage::disk('public')->delete($photo->chemin);

        $etaitPrincipale = $photo->est_principale;
        $photo->delete();

        if ($etaitPrincipale) {
            $bien->photos()->first()?->update(['est_principale' => true]);
        }

        return back()->with('success', 'Photo supprimée ✓');
    }

    // ── Définir comme photo principale ───────────────────────────────────

    public function setPrincipale(Bien $bien, BienPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $bien);

        // Même protection : findOrFail sur la relation
        $photo = $bien->photos()->findOrFail($photo->id);

        $bien->photos()->update(['est_principale' => false]);
        $photo->update(['est_principale' => true]);

        return back()->with('success', 'Photo principale mise à jour ✓');
    }
}