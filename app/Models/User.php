<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, LogsActivity, HasRoles;

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
    public function getActivityLogTitle(): string
    {
        $role = $this->role ? ' (' . $this->role . ')' : '';
        return ($this->name ?? 'Utilisateur #' . $this->id) . $role;
    }

    protected static array $activityFieldLabels = [
        'name'      => 'Nom',
        'email'     => 'Email',
        'telephone' => 'Téléphone',
        'role'      => 'Rôle',
        'agency_id' => 'Agence',
        'actif'     => 'Actif',
    ];

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'password',
        'telephone',
        'adresse',
        'email_verified_at',
        'notification_preferences',
    ];

    // is_owner et role intentionnellement absents de $fillable — assignation directe uniquement

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'is_owner'                 => 'boolean',
        'must_change_password'     => 'boolean',
        'notification_preferences' => 'array',
        'email_verified_at'        => 'datetime',
        'two_factor_confirmed_at'  => 'datetime',
        'password'                 => 'hashed',
        'two_factor_secret'        => 'encrypted',
        'two_factor_recovery_codes'=> 'encrypted',
        // ── Équipe interne (comptes super-admin) ──
        'sa_est_principal'         => 'boolean',
        'sa_taux_commission'       => 'decimal:2',
        'sa_permissions'           => 'array',
        'sa_acces_revoque_at'      => 'datetime',
        // Note : pas de cast Enum ici — $user->role reste une string en Blade.
        // Utiliser UserRole::from($user->role) dans le code PHP si l'enum est nécessaire.
    ];

    // sa_est_principal, sa_taux_commission, sa_permissions et sa_acces_revoque_at
    // sont intentionnellement ABSENTS de $fillable : ce sont des privilèges plateforme,
    // assignés uniquement par l'admin principal via EquipeInterneController (assignation
    // directe), jamais par un formulaire mass-assignable.

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

    // ── Permissions granulaires (collaborateurs) ──────────────────────────
    // Superadmin et directeur (is_owner) passent toujours.
    // Collaborateurs (role=admin, is_owner=false) → vérifié via spatie.
    public function hasAgencyPermission(string $permission): bool
    {
        if ($this->isSuperAdmin() || $this->isOwner()) return true;
        if ($this->role !== UserRole::Admin->value) return false;
        // checkPermissionTo() = alias fail-closed de hasPermissionTo() : renvoie false
        // (au lieu de lever PermissionDoesNotExist → 500) si la permission n'est pas
        // enregistrée. Garantit un 403 propre même si le seeder n'a pas été rejoué.
        return $this->checkPermissionTo($permission);
    }

    // ── Helpers rôles ─────────────────────────────────────────────────────
    // ->value extrait la string de l'enum → compatible avec la colonne DB string.
    // Avantage : l'IDE autocompète UserRole::Admin, impossible de faire une faute.

    public function isSuperAdmin(): bool   { return $this->role === UserRole::SuperAdmin->value; }
    public function isAdmin(): bool        { return $this->role === UserRole::Admin->value; }
    public function isOwner(): bool        { return $this->isAdmin() && (bool) $this->is_owner; }
    public function isProprietaire(): bool { return $this->role === UserRole::Proprietaire->value; }
    public function isLocataire(): bool    { return $this->role === UserRole::Locataire->value; }

    // ── Équipe interne (super-admin principal vs collaborateur restreint) ──
    //
    // Un compte super-admin est soit PRINCIPAL (Malick : accès total implicite,
    // aucun toggle), soit COLLABORATEUR restreint (Israel & futurs : périmètre
    // limité à ses agences apportées, permissions fines, taux de commission).

    /** Les 4 permissions d'un collaborateur restreint, avec leur défaut (brief). */
    public const SA_PERMISSIONS = [
        'voir_agences'    => true,   // voir ses agences attribuées
        'impersonation'   => true,   // impersonation limitée à ses agences
        'facturation'     => false,  // facturation globale plateforme
        'regles_fiscales' => false,  // règles fiscales
    ];

    /** Admin principal de la plateforme (accès total, non filtré). */
    public function estSuperAdminPrincipal(): bool
    {
        return $this->isSuperAdmin() && (bool) $this->sa_est_principal;
    }

    /** Collaborateur super-admin à accès restreint (périmètre = ses agences). */
    public function estCollaborateurSa(): bool
    {
        return $this->isSuperAdmin() && ! $this->sa_est_principal;
    }

    /** Accès super-admin révoqué (connexion coupée, agences conservées). */
    public function saAccesRevoque(): bool
    {
        return $this->sa_acces_revoque_at !== null;
    }

    /** Valeur effective d'un toggle de permission (défaut du brief si non défini). */
    public function saPermission(string $cle): bool
    {
        // Le principal a tout ; pour un collaborateur on lit sa config, à défaut le défaut.
        if ($this->estSuperAdminPrincipal()) {
            return true;
        }
        $perms = $this->sa_permissions ?? [];

        return (bool) ($perms[$cle] ?? (self::SA_PERMISSIONS[$cle] ?? false));
    }

    /** Taux de commission (%) du collaborateur — 0 si non configuré. */
    public function tauxCommission(): float
    {
        return (float) ($this->sa_taux_commission ?? 0);
    }

    /** Agences apportées par ce collaborateur (périmètre de commission). */
    public function agencesApportees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Agency::class, 'amenee_par');
    }

    /** Collaborateurs super-admin (hors principal), pour l'écran Équipe interne. */
    public function scopeCollaborateursSa(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('role', UserRole::SuperAdmin->value)->where('sa_est_principal', false);
    }

    // ── 2FA helpers ───────────────────────────────────────────────────────

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_confirmed_at !== null;
    }

    public function generateRecoveryCodes(): array
    {
        $plain = [];
        for ($i = 0; $i < 8; $i++) {
            $plain[] = strtoupper(substr(bin2hex(random_bytes(5)), 0, 5) . '-' . substr(bin2hex(random_bytes(5)), 0, 5));
        }

        $this->two_factor_recovery_codes = json_encode(array_map('bcrypt', $plain));
        $this->save();

        return $plain;
    }

    public function useRecoveryCode(string $code): bool
    {
        $hashes = json_decode($this->two_factor_recovery_codes ?? '[]', true);

        foreach ($hashes as $index => $hash) {
            if (password_verify($code, $hash)) {
                array_splice($hashes, $index, 1);
                $this->two_factor_recovery_codes = json_encode(array_values($hashes));
                $this->save();
                return true;
            }
        }

        return false;
    }

    // ── Profil selon le rôle ──────────────────────────────────────────────

    public function profil(): Proprietaire|Locataire|null
    {
        return match ($this->role) {
            UserRole::Proprietaire->value => $this->proprietaire,
            UserRole::Locataire->value    => $this->locataire,
            default                       => null,
        };
    }

    // ── Préférences de notification (in-app) ──────────────────────────────

    public const NOTIF_DEFAULTS = [
        'alerte_retard'   => true,   // alerte à chaque nouveau retard de paiement
        'rappel_echeance' => false,  // rappel des contrats bientôt échus
    ];

    /** Valeur d'une préférence de notification (avec défaut si non définie). */
    public function notifPref(string $key): bool
    {
        $prefs = $this->notification_preferences ?? [];
        return (bool) ($prefs[$key] ?? (self::NOTIF_DEFAULTS[$key] ?? false));
    }
}