<?php

namespace App\Services;

class NombreEnLettres
{
    private static array $unites = [
        0 => 'zéro', 1 => 'un', 2 => 'deux', 3 => 'trois', 4 => 'quatre',
        5 => 'cinq', 6 => 'six', 7 => 'sept', 8 => 'huit', 9 => 'neuf',
        10 => 'dix', 11 => 'onze', 12 => 'douze', 13 => 'treize',
        14 => 'quatorze', 15 => 'quinze', 16 => 'seize', 17 => 'dix-sept',
        18 => 'dix-huit', 19 => 'dix-neuf', 20 => 'vingt', 30 => 'trente',
        40 => 'quarante', 50 => 'cinquante', 60 => 'soixante',
        70 => 'soixante-dix', 80 => 'quatre-vingt', 90 => 'quatre-vingt-dix',
    ];

    /**
     * Convertit un montant en lettres françaises.
     * Ex: 250000 → "Deux cent cinquante mille FCFA"
     */
    public static function convertir(float $montant, string $devise = 'FCFA'): string
    {
        $montant = (int) round($montant);

        if ($montant === 0) return 'Zéro ' . $devise;

        $resultat = self::enLettres($montant);

        // Capitalise la première lettre
        $resultat = mb_strtoupper(mb_substr($resultat, 0, 1))
                  . mb_substr($resultat, 1);

        return $resultat . ' ' . $devise;
    }

    private static function enLettres(int $n): string
    {
        if ($n < 0)  return 'moins ' . self::enLettres(-$n);
        if ($n === 0) return '';
        if ($n <= 20) return self::$unites[$n];

        if ($n < 100) {
            $dizaine = (int)($n / 10) * 10;
            $unite   = $n % 10;

            // Cas spéciaux : 70-79, 90-99
            if ($dizaine === 70 || $dizaine === 90) {
                $base = $dizaine === 70 ? 60 : 80;
                $reste = $n - $base;
                $liaison = ($reste === 1 && $dizaine === 70) ? ' et ' : '-';
                return self::$unites[$base] . $liaison . self::$unites[$reste];
            }

            if ($unite === 0) {
                // "quatre-vingts" prend un s, "vingt" seul aussi
                return self::$unites[$dizaine] . ($dizaine === 80 ? 's' : '');
            }

            $liaison = ($unite === 1 && $dizaine !== 80) ? ' et ' : '-';
            return self::$unites[$dizaine] . $liaison . self::$unites[$unite];
        }

        if ($n < 1000) {
            $centaines = (int)($n / 100);
            $reste     = $n % 100;
            $prefix    = $centaines === 1 ? 'cent' : self::$unites[$centaines] . ' cent';
            // "cents" avec s si multiple exact de 100
            if ($reste === 0 && $centaines > 1) return $prefix . 's';
            return $reste === 0 ? $prefix : $prefix . ' ' . self::enLettres($reste);
        }

        if ($n < 1_000_000) {
            $milliers = (int)($n / 1000);
            $reste    = $n % 1000;
            $prefix   = $milliers === 1 ? 'mille' : self::enLettres($milliers) . ' mille';
            return $reste === 0 ? $prefix : $prefix . ' ' . self::enLettres($reste);
        }

        if ($n < 1_000_000_000) {
            $millions = (int)($n / 1_000_000);
            $reste    = $n % 1_000_000;
            $prefix   = self::enLettres($millions) . ' million' . ($millions > 1 ? 's' : '');
            return $reste === 0 ? $prefix : $prefix . ' ' . self::enLettres($reste);
        }

        // Milliards
        $milliards = (int)($n / 1_000_000_000);
        $reste     = $n % 1_000_000_000;
        $prefix    = self::enLettres($milliards) . ' milliard' . ($milliards > 1 ? 's' : '');
        return $reste === 0 ? $prefix : $prefix . ' ' . self::enLettres($reste);
    }
}