<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'agency_id',
        'plan',
        'montant',
        'statut',
        'reference',
        'methode',
        'periode_debut',
        'periode_fin',
        'notes',
    ];

    protected $casts = [
        'montant'       => 'decimal:2',
        'periode_debut' => 'datetime',
        'periode_fin'   => 'datetime',
    ];

    // ── Labels ────────────────────────────────────────────────────────────

    public const METHODE_LABELS = [
        'paydunya'     => 'PayDunya',
        'wave'         => 'Wave',
        'orange_money' => 'Orange Money',
        'virement'     => 'Virement bancaire',
        'manuel'       => 'Manuel (SuperAdmin)',
    ];

    public const STATUT_LABELS = [
        'en_attente' => 'En attente',
        'payé'       => 'Payé',
        'échoué'     => 'Échoué',
        'remboursé'  => 'Remboursé',
    ];

    // ── Relations ─────────────────────────────────────────────────────────

    public function subscription(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function agency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}