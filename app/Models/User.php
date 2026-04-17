<?php

namespace App\Models;

use App\Enums\UserRole;
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
        // Note : pas de cast Enum ici — $user->role reste une string en Blade.
        // Utiliser UserRole::from($user->role) dans le code PHP si l'enum est nécessaire.
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
    // ->value extrait la string de l'enum → compatible avec la colonne DB string.
    // Avantage : l'IDE autocompète UserRole::Admin, impossible de faire une faute.

    public function isSuperAdmin(): bool   { return $this->role === UserRole::SuperAdmin->value; }
    public function isAdmin(): bool        { return $this->role === UserRole::Admin->value; }
    public function isProprietaire(): bool { return $this->role === UserRole::Proprietaire->value; }
    public function isLocataire(): bool    { return $this->role === UserRole::Locataire->value; }

    // ── Profil selon le rôle ──────────────────────────────────────────────

    public function profil(): Proprietaire|Locataire|null
    {
        return match ($this->role) {
            UserRole::Proprietaire->value => $this->proprietaire,
            UserRole::Locataire->value    => $this->locataire,
            default                       => null,
        };
    }
}