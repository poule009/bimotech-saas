<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Point d'historique mensuel du MRR de la plateforme (voir migration).
 * Donnée niveau plateforme → PAS de HasAgencyScope.
 */
class MrrSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'mois',
        'mrr',
        'agences_actives',
    ];

    protected $casts = [
        'mois' => 'date',
        'mrr' => 'integer',
        'agences_actives' => 'integer',
    ];
}
