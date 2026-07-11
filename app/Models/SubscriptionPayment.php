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
        'plan_niveau',
        'montant',
        'statut',
        'reference',
        'justificatif',
        'methode',
        'periode_debut',
        'periode_fin',
        'notes',
        'motif_rejet',
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

    // Statuts du flux manuel (déclaration → confirmation/rejet côté BIMO-tech).
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_CONFIRME   = 'confirme';
    public const STATUT_REJETE     = 'rejete';

    public const STATUT_LABELS = [
        'en_attente' => 'En attente',
        'confirme'   => 'Confirmé',
        'rejete'     => 'Rejeté',
        // Anciens statuts (flux PayTech dormant) — conservés pour l'historique.
        'payé'       => 'Confirmé',
        'échoué'     => 'Rejeté',
        'remboursé'  => 'Remboursé',
    ];

    public function getStatutLabelAttribute(): string
    {
        return self::STATUT_LABELS[$this->statut] ?? ucfirst($this->statut);
    }

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