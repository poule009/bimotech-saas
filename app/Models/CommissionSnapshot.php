<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Point d'historique mensuel de la commission d'un collaborateur (voir migration).
 *
 * Immuable par principe (brief) : un mois passé n'est jamais modifié ni recalculé —
 * il garde le nb d'agences, le MRR, le taux et la commission figés à la capture,
 * pour servir de justificatif dans les échanges de rémunération.
 *
 * Donnée niveau plateforme → PAS de HasAgencyScope.
 */
class CommissionSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'collaborateur_id',
        'mois',
        'nb_agences',
        'mrr_total',
        'taux',
        'commission',
    ];

    protected $casts = [
        'mois'       => 'date',
        'nb_agences' => 'integer',
        'mrr_total'  => 'integer',
        'taux'       => 'decimal:2',
        'commission' => 'integer',
    ];

    public function collaborateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collaborateur_id');
    }
}
