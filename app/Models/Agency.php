<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'telephone',
        'logo_path',
        'couleur_primaire',
        'adresse',
        'taux_tva',
        'actif',
    ];

    protected $casts = [
        'actif'    => 'boolean',
        'taux_tva' => 'decimal:2',
    ];

    // ── Relations ─────────────────────────────────────────────────────────

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function biens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Bien::class);
    }

    public function contrats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contrat::class);
    }

    public function paiements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function subscription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    public function isActif(): bool
    {
        return $this->actif === true;
    }

    public function aAccesAbonnement(): bool
    {
        return $this->subscription && $this->subscription->aAcces();
    }

    /**
     * Détermine si la couleur primaire est claire ou sombre.
     */
    public function couleurEstSombre(): bool
    {
        $hex = ltrim($this->couleur_primaire ?? '#1a3c5e', '#');

        if (strlen($hex) !== 6) {
            return true;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $luminosite = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminosite < 0.5;
    }

    public function classeTexteNav(): string
    {
        return $this->couleurEstSombre() ? 'text-white' : 'text-gray-800';
    }

    public function classeTexteNavHover(): string
    {
        return $this->couleurEstSombre() ? 'hover:text-gray-200' : 'hover:text-gray-600';
    }
}