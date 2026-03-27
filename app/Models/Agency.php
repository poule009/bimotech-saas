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

    // ── Helpers ───────────────────────────────────────────────────────────

    public function isActif(): bool
    {
        return $this->actif === true;
    }
}