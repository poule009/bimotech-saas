<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\BienPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BienPhotoController extends Controller
{
    // ── Upload de photos ──────────────────────────────────────────────
    public function store(Request $request, Bien $bien)
    {
        $this->authorize('update', $bien);

        $request->validate([
            'photos'   => ['required', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'], // 3MB max
        ], [
            'photos.*.image'    => 'Chaque fichier doit être une image.',
            'photos.*.mimes'    => 'Formats acceptés : JPG, PNG, WEBP.',
            'photos.*.max'      => 'Chaque image ne doit pas dépasser 3 MB.',
        ]);

        $ordre = $bien->photos()->max('ordre') ?? 0;

        foreach ($request->file('photos') as $fichier) {
            $chemin = $fichier->store(
                'biens/' . $bien->id,
                'public'
            );

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

    // ── Suppression d'une photo ───────────────────────────────────────
    public function destroy(Bien $bien, BienPhoto $photo)
    {
        $this->authorize('update', $bien);

        // Supprime le fichier du disque
        Storage::disk('public')->delete($photo->chemin);

        $etaitPrincipale = $photo->est_principale;
        $photo->delete();

        // Si c'était la principale, définit la prochaine comme principale
        if ($etaitPrincipale) {
            $prochaine = $bien->photos()->first();
            $prochaine?->update(['est_principale' => true]);
        }

        return back()->with('success', 'Photo supprimée ✓');
    }

    // ── Définir comme photo principale ───────────────────────────────
    public function setPrincipale(Bien $bien, BienPhoto $photo)
    {
        $this->authorize('update', $bien);

        // Retire l'ancienne principale
        $bien->photos()->update(['est_principale' => false]);

        // Définit la nouvelle
        $photo->update(['est_principale' => true]);

        return back()->with('success', 'Photo principale mise à jour ✓');
    }
}