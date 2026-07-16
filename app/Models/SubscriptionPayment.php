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

    /**
     * Regroupement des statuts pour la facturation Super Admin.
     *
     * Les anciens statuts PayTech ('payé'/'échoué') coexistent en base avec les
     * statuts du flux manuel : filtrer sur la seule valeur courante masquerait
     * l'historique. Toute requête sur un statut doit passer par ces buckets.
     */
    public const BUCKETS = [
        'confirme'   => ['confirme', 'payé'],
        'rejete'     => ['rejete', 'échoué'],
        'en_attente' => ['en_attente'],
        'rembourse'  => ['remboursé'],
    ];

    /** Valeurs SQL correspondant à un bucket ([] si bucket inconnu). */
    public static function statutsDuBucket(string $bucket): array
    {
        return self::BUCKETS[$bucket] ?? [];
    }

    /** Bucket d'appartenance d'une ligne (pour choisir la pastille). */
    public function bucket(): ?string
    {
        foreach (self::BUCKETS as $bucket => $statuts) {
            if (in_array($this->statut, $statuts, true)) {
                return $bucket;
            }
        }

        return null;
    }

    public function estConfirme(): bool  { return $this->bucket() === 'confirme'; }
    public function estEnAttente(): bool { return $this->bucket() === 'en_attente'; }

    public function scopeConfirmes($query)
    {
        return $query->whereIn('statut', self::BUCKETS['confirme']);
    }

    public function scopeDuBucket($query, string $bucket)
    {
        return $query->whereIn('statut', self::statutsDuBucket($bucket));
    }

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