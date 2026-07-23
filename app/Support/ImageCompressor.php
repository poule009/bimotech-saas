<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * ImageCompressor — Recompresse une image du disque `public` en JPEG ≤ 1200px.
 *
 * Partagé entre l'upload à la création du bien (BienController::store) et
 * l'upload dédié (BienPhotoController::store) : mêmes règles, même rendu.
 * Sans l'extension GD (ou sur un fichier illisible), ne fait rien — l'image
 * d'origine reste servie telle quelle.
 */
class ImageCompressor
{
    public static function comprimer(string $chemin): void
    {
        if (! extension_loaded('gd')) {
            return;
        }

        $cheminAbsolu = Storage::disk('public')->path($chemin);
        $info = @getimagesize($cheminAbsolu);

        if (! $info) {
            return;
        }

        [$largeurOriginale, $hauteurOriginale, $typeImage] = $info;

        if ($largeurOriginale <= 1200) {
            $largeur = $largeurOriginale;
            $hauteur = $hauteurOriginale;
        } else {
            $largeur = 1200;
            $hauteur = (int) round($hauteurOriginale * 1200 / $largeurOriginale);
        }

        $source = match ($typeImage) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($cheminAbsolu),
            IMAGETYPE_PNG  => imagecreatefrompng($cheminAbsolu),
            IMAGETYPE_WEBP => imagecreatefromwebp($cheminAbsolu),
            default        => null,
        };

        if (! $source) {
            return;
        }

        $destination = imagecreatetruecolor($largeur, $hauteur);

        // PNG/WEBP peuvent être transparents → fond blanc avant conversion JPEG
        $blanc = imagecolorallocate($destination, 255, 255, 255);
        imagefill($destination, 0, 0, $blanc);

        imagecopyresampled(
            $destination, $source,
            0, 0, 0, 0,
            $largeur, $hauteur,
            $largeurOriginale, $hauteurOriginale
        );

        imagejpeg($destination, $cheminAbsolu, 82);

        imagedestroy($source);
        imagedestroy($destination);
    }
}
