<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubscriptionPayment extends Model
{
    use HasFactory, HasAgencyScope;

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
        'paytech'      => 'PayTech',
        'wave'         => 'Wave',
        'orange_money' => 'Orange Money',
        'free_money'   => 'Free Money',
        'virement'     => 'Virement bancaire',
        'manuel'       => 'Manuel (SuperAdmin)',
        'simulation'   => 'Simulation (Test)',
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