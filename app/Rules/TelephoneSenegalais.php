<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valide un numéro de téléphone sénégalais.
 *
 * Formats acceptés :
 *  - 7X XXX XX XX  (mobile : 70, 75, 76, 77, 78)
 *  - 33 XXX XX XX  (fixe Dakar)
 *  - 30 XXX XX XX  (fixe régions)
 *  - +221 7X XXX XX XX  (avec indicatif international)
 *  - 00221 7X XXX XX XX (avec indicatif international)
 */
class TelephoneSenegalais implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Nettoyer : supprimer espaces, tirets, points
        $numero = preg_replace('/[\s\-\.]/', '', (string) $value);

        // Supprimer l'indicatif international si présent
        $numero = preg_replace('/^(\+221|00221)/', '', $numero);

        // Doit être exactement 9 chiffres
        if (! preg_match('/^\d{9}$/', $numero)) {
            $fail("Le numéro de téléphone :attribute n'est pas valide (format sénégalais attendu).");
            return;
        }

        // Préfixes valides au Sénégal
        $prefixesValides = [
            '70', // Expresso
            '75', // Free
            '76', // Tigo/Free
            '77', // Orange
            '78', // Wave / Orange
            '33', // Fixe Dakar (Sonatel)
            '30', // Fixe régions
        ];

        $prefixe = substr($numero, 0, 2);

        if (! in_array($prefixe, $prefixesValides)) {
            $fail("Le numéro :attribute ne correspond pas à un opérateur sénégalais reconnu (Orange: 77/78, Free: 75/76, Expresso: 70, Fixe: 33/30).");
        }
    }
}
