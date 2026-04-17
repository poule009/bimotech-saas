<?php

namespace App\Enums;

/**
 * BienStatut — Statuts du cycle de vie d'un bien immobilier.
 *
 * Remplace le tableau STATUTS dans le modèle Bien tout en garantissant
 * la type-safety : impossible d'assigner un statut inexistant via le code.
 *
 * Utilisation :
 *   - `$bien->statut`                         → string 'loue' (pas de cast Eloquent)
 *   - `{{ $bien->statut }}`                   → Blade : affiche 'loue'
 *   - `$bien->statut === BienStatut::Loue->value` → comparaison type-safe
 *   - `$collection->where('statut', BienStatut::Loue->value)` → Collection filtering
 *   - `BienStatut::tryFrom($bien->statut)`    → obtenir l'enum depuis une string
 */
enum BienStatut: string
{
    case Disponible = 'disponible';
    case Loue       = 'loue';
    case EnTravaux  = 'en_travaux';
    case Archive    = 'archive';

    /** Libellé lisible pour l'interface. */
    public function label(): string
    {
        return match($this) {
            self::Disponible => 'Disponible',
            self::Loue       => 'Loué',
            self::EnTravaux  => 'En travaux',
            self::Archive    => 'Archivé',
        };
    }

    /** Vrai si le bien génère un loyer actif. */
    public function estOccupe(): bool
    {
        return $this === self::Loue;
    }
}
