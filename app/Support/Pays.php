<?php

namespace App\Support;

/**
 * Pays — accès au référentiel config/pays.php.
 *
 * Point d'entrée unique pour tout ce qui touche au pays d'une agence : liste
 * proposée à l'inscription, libellé d'affichage, devise par défaut. Évite que
 * `config('pays.liste.…')` se disperse dans les contrôleurs et les vues.
 *
 * Rappel d'architecture : la liste des pays SÉLECTIONNABLES (`ouverts`) est
 * volontairement plus courte que le catalogue (`liste`). Un pays n'est ouvert
 * que lorsque son parcours complet est livré — voir config/pays.php.
 */
final class Pays
{
    /** Code du pays historique du produit. Sert de socle au backfill et aux seeders. */
    public const DEFAUT = 'SN';

    /**
     * Codes ISO alpha-2 réellement proposables à l'inscription.
     *
     * @return list<string>
     */
    public static function ouverts(): array
    {
        return array_values(config('pays.ouverts', [self::DEFAUT]));
    }

    /**
     * Pays ouverts sous forme [code => nom], triés par nom — prêt pour un <select>.
     *
     * @return array<string, string>
     */
    public static function optionsInscription(): array
    {
        $options = [];

        foreach (self::ouverts() as $code) {
            $options[$code] = self::nom($code);
        }

        asort($options);

        return $options;
    }

    /** Libellé lisible d'un pays. Retombe sur le code si le pays n'est pas au catalogue. */
    public static function nom(string $code): string
    {
        return config("pays.liste.{$code}.nom", $code);
    }

    /**
     * Devise par défaut d'un pays (ISO 4217).
     *
     * Sert UNIQUEMENT à pré-remplir `agencies.devise` à la création. La devise
     * reste une colonne autonome : un pays peut en changer, et le pré-remplissage
     * ne doit jamais être relu comme une source de vérité a posteriori.
     */
    public static function devise(string $code): string
    {
        return config("pays.liste.{$code}.devise")
            ?? config('pays.liste.' . self::DEFAUT . '.devise', 'XOF');
    }

    /** Le pays est-il ouvert à l'inscription ? Utilisé par la validation. */
    public static function estOuvert(string $code): bool
    {
        return in_array($code, self::ouverts(), true);
    }
}
