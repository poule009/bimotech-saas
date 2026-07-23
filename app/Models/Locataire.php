<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Locataire extends Model
{
    // Isolation par agence : colonne locale agency_id (dénormalisée depuis le user,
    // migration 2026_07_23) — même mécanisme que tous les autres modèles tenant.
    use LogsActivity, SoftDeletes, HasAgencyScope;

    /**
     * agency_id est dérivé du user locataire du profil (un user n'appartient
     * qu'à une seule agence et n'en change jamais). Hors $fillable : posé ici,
     * jamais par un formulaire.
     */
    protected static function booted(): void
    {
        static::creating(function (self $profil) {
            if (empty($profil->agency_id) && $profil->user_id) {
                $profil->agency_id = User::withoutGlobalScopes()
                    ->whereKey($profil->user_id)
                    ->value('agency_id');
            }
        });
    }

    protected $fillable = [
        'user_id',
        'cni', 'date_naissance', 'genre', 'nationalite',
        'profession', 'employeur', 'revenu_mensuel',
        'contact_urgence_nom', 'contact_urgence_tel', 'contact_urgence_lien',
        'adresse_precedente', 'ville', 'quartier',
        'piece_identite_path',
        'cni_verified', 'justif_revenus_fourni',
        // ── Champs fiscaux ────────────────────────────────────────────────
        'est_entreprise',       // BRS applicable si true
        'type_locataire',       // particulier|entreprise|association|ambassade|ong
        'ninea_locataire',      // NINEA entreprise locataire
        'rccm_locataire',       // RCCM si entreprise
        'nom_entreprise',       // Raison sociale si personne morale
        'taux_brs_override',    // Taux BRS personnalisé (null = légal auto)
        'code_import', 'import_batch_id',
    ];

    protected $casts = [
        'date_naissance'         => 'date',
        'cni_verified'           => 'boolean',
        'justif_revenus_fourni'  => 'boolean',
        'revenu_mensuel'         => 'decimal:2',
        // Fiscal
        'est_entreprise'         => 'boolean',
        'taux_brs_override'      => 'decimal:2',
        'deleted_at'             => 'datetime',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contrats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contrat::class, 'locataire_id', 'user_id');
    }

    public function contratActif(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Contrat::class, 'locataire_id', 'user_id')
                    ->where('statut', 'actif');
    }

    // ── Accesseurs ────────────────────────────────────────────────────────────

    public function getNomCompletAttribute(): string
    {
        return $this->user->name;
    }

    /**
     * Taux d'effort locatif (loyer / revenu mensuel).
     * Indicateur de solvabilité pour l'agence.
     */
    public function getTauxEffortAttribute(): ?string
    {
        if (! $this->revenu_mensuel) return null;
        if (! $this->relationLoaded('contratActif') || ! $this->contratActif) return null;
        $taux = ($this->contratActif->loyer_contractuel / $this->revenu_mensuel) * 100;
        return round($taux, 1) . '%';
    }

    /**
     * Libellé du type de locataire pour l'affichage.
     */
    public function getLabelTypeAttribute(): string
    {
        return match($this->type_locataire ?? 'particulier') {
            'particulier'  => 'Particulier',
            'entreprise'   => 'Entreprise',
            'association'  => 'Association',
            'ambassade'    => 'Ambassade / Organisme diplomatique',
            'ong'          => 'ONG / Organisation internationale',
            default        => ucfirst($this->type_locataire ?? 'Particulier'),
        };
    }

    /**
     * Identifiant fiscal affiché : NINEA si entreprise, CNI si particulier.
     */
    public function getIdentifiantFiscalAttribute(): ?string
    {
        if ($this->est_entreprise) {
            return $this->ninea_locataire;
        }
        return $this->cni;
    }

    /**
     * Nom commercial : raison sociale si entreprise, nom complet sinon.
     * Utilisé sur la quittance PDF.
     */
    public function getNomQuittanceAttribute(): string
    {
        if ($this->est_entreprise && $this->nom_entreprise) {
            return $this->nom_entreprise;
        }
        return $this->user->name;
    }
}