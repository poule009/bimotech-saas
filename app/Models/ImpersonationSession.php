<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Session d'impersonation « Super Admin → agence ».
 *
 * Pas de HasAgencyScope : c'est une table de supervision plateforme, consultée
 * uniquement par les super-admins, toutes agences confondues.
 */
class ImpersonationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'user_id',
        'agency_id',
        'admin_name',
        'agency_name',
        'started_at',
        'ended_at',
        'ended_by',
        'end_reason',
        'ip_address',
    ];

    /**
     * Au-delà de ce délai (heures) sans clôture, une session active est présumée
     * abandonnée (cookie expiré, onglet fermé) : le balayage la ferme et la vue la
     * traite comme telle. Aligné sur une demi-journée de travail.
     */
    public const STALE_AFTER_HOURS = 12;

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    /** Le super-admin qui a lancé l'impersonation. */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /** L'utilisateur agence impersonné. */
    public function impersonatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /** Le super-admin qui a clôturé la session (sortie normale ou coupure). */
    public function endedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ended_by');
    }

    /** Sessions encore ouvertes (personne connecté « en tant que »). */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('ended_at');
    }

    /** Sessions actives depuis trop longtemps → présumées abandonnées. */
    public function scopeStale(Builder $query): Builder
    {
        return $query->whereNull('ended_at')
            ->where('started_at', '<', now()->subHours(self::STALE_AFTER_HOURS));
    }

    public function isActive(): bool
    {
        return $this->ended_at === null;
    }

    /** Session active mais probablement abandonnée (affichage prudent). */
    public function isStale(): bool
    {
        return $this->isActive()
            && $this->started_at->lt(now()->subHours(self::STALE_AFTER_HOURS));
    }

    /**
     * Durée en secondes : figée si terminée, sinon écoulée jusqu'à maintenant
     * (le rendu temps réel côté client repart de started_at).
     */
    public function durationSeconds(): int
    {
        $end = $this->ended_at ?? now();

        return (int) max(0, $this->started_at->diffInSeconds($end));
    }
}
