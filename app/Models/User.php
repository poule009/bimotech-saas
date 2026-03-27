<?php

namespace App\Models;

use App\Models\Scopes\AgencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'name',
        'email',
        'password',
        'role',
        'telephone',
        'adresse',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Global Scope ──────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->agency_id) && Auth::check() && ! Auth::user()->isSuperAdmin()) {
                $user->agency_id = Auth::user()->agency_id;
            }
        });
    }

    // ── Relations ─────────────────────────────────────────────────────────

    public function agency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function proprietaire(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Proprietaire::class);
    }

    public function locataire(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Locataire::class);
    }

    public function biens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Bien::class, 'proprietaire_id');
    }

    public function contrats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contrat::class, 'locataire_id');
    }

    // ── Helpers rôles ─────────────────────────────────────────────────────

    public function isSuperAdmin(): bool   { return $this->role === 'superadmin'; }
    public function isAdmin(): bool        { return $this->role === 'admin'; }
    public function isProprietaire(): bool { return $this->role === 'proprietaire'; }
    public function isLocataire(): bool    { return $this->role === 'locataire'; }

    // ── Profil selon le rôle ──────────────────────────────────────────────

    public function profil(): Proprietaire|Locataire|null
    {
        return match($this->role) {
            'proprietaire' => $this->proprietaire,
            'locataire'    => $this->locataire,
            default        => null,
        };
    }
}