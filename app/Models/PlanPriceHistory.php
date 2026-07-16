<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Une ligne = un champ de plan modifié par un superadmin, à une date donnée.
 * Écrit uniquement par PlanService::update(). Jamais modifié ni supprimé :
 * c'est une piste d'audit.
 */
class PlanPriceHistory extends Model
{
    protected $table = 'plan_price_history';

    protected $fillable = [
        'plan_id',
        'user_id',
        'champ',
        'ancienne_valeur',
        'nouvelle_valeur',
    ];

    public const LABELS_CHAMPS = [
        'prix_mensuel'  => 'Prix mensuel',
        'prix_annuel'   => 'Prix annuel',
        'limite_unites' => 'Limite de biens',
        'limite_admins' => "Limite d'utilisateurs",
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getChampLabelAttribute(): string
    {
        return self::LABELS_CHAMPS[$this->champ] ?? $this->champ;
    }
}
