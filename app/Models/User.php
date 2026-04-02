<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * SÉCURITÉ — Mass Assignment :
     *
     * `role` et `agency_id` sont intentionnellement ABSENTS de $fillable.
     * Ces deux colonnes sont critiques : les laisser ici permettrait à n'importe
     * quel formulaire mal protégé de changer le rôle d'un utilisateur ou de le
     * rattacher à une autre agence.
     *
     * → Pour les assigner, utilisez TOUJOURS l'assignation directe :
     *     $user->role = 'admin';
     *     $user->agency_id = Auth::user()->agency_id;
     *     $user->save();
     *
     * OU passez par forceFill() uniquement dans des contextes contrôlés
     * (seeders, migrations de données, commandes artisan).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'adresse',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Hook de création ──────────────────────────────────────────────────
    // agency_id assigné ici via forceFill() contrôlé, pas via $fillable

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            /** @var \App\Models\User|null $authUser */
            $authUser = Auth::user();

            // Si agency_id non encore défini, on l'injecte depuis l'utilisateur connecté
            if (empty($user->agency_id) && $authUser && ! $authUser->isSuperAdmin()) {
                $user->agency_id = $authUser->agency_id;
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
        return match ($this->role) {
            'proprietaire' => $this->proprietaire,
            'locataire'    => $this->locataire,
            default        => null,
        };
    }
}