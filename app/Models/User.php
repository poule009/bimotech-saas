<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'telephone', 'adresse',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Relations profils ─────────────────────────────────────────────────

    public function proprietaire()
    {
        return $this->hasOne(Proprietaire::class);
    }

    public function locataire()
    {
        return $this->hasOne(Locataire::class);
    }

    public function biens()
    {
        return $this->hasMany(Bien::class, 'proprietaire_id');
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'locataire_id');
    }

    // ── Helpers rôles ─────────────────────────────────────────────────────

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