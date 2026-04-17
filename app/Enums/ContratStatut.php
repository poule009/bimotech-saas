<?php

namespace App\Enums;

/**
 * ContratStatut — Statuts du cycle de vie d'un contrat de bail.
 *
 * Garantit qu'aucun statut invalide ne peut être écrit par erreur de frappe.
 * Utilisation dans ContratController :
 *   `$contrat->statut !== ContratStatut::Actif->value` → comparaison type-safe
 *   `$contrat->update(['statut' => ContratStatut::Resilie->value])` → écriture safe
 */
enum ContratStatut: string
{
    case Actif   = 'actif';
    case Resilie = 'resilié';
    case Expire  = 'expiré';

    /** Libellé lisible pour l'interface. */
    public function label(): string
    {
        return match($this) {
            self::Actif   => 'Actif',
            self::Resilie => 'Résilié',
            self::Expire  => 'Expiré',
        };
    }

    /** Vrai si le contrat peut être modifié ou encaissé. */
    public function estActif(): bool
    {
        return $this === self::Actif;
    }
}
