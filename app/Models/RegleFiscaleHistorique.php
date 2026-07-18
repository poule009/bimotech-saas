<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RegleFiscaleHistorique — une modification d'un champ d'une règle fiscale.
 *
 * Alimentée par SuperAdminController::updateRegleFiscale : à chaque
 * enregistrement, une ligne par champ effectivement modifié. Lecture seule
 * côté interface (pas de restauration en v1).
 */
class RegleFiscaleHistorique extends Model
{
    protected $table = 'regle_fiscale_historiques';

    protected $fillable = [
        'regle_fiscale_id',
        'admin_id',
        'admin_nom',
        'champ',
        'ancienne_valeur',
        'nouvelle_valeur',
    ];

    public function regle(): BelongsTo
    {
        return $this->belongsTo(RegleFiscale::class, 'regle_fiscale_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /** Libellé lisible du champ modifié, pour l'historique. */
    public function getChampLabelAttribute(): string
    {
        return match ($this->champ) {
            'statut'            => 'Statut de fiabilité',
            'titre'             => 'Nom de la règle',
            'description'       => 'Description',
            'note'              => 'Note / réserve',
            'date_verification' => 'Date de vérification',
            'sources'           => 'Sources',
            default             => $this->champ,
        };
    }
}
