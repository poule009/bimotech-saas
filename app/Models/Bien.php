<?php

namespace App\Models;

use App\Enums\BienStatut;
use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bien extends Model
{
    use HasFactory, HasAgencyScope, SoftDeletes;

    public const TYPES = [
        'appartement' => 'Appartement',
        'villa'       => 'Villa',
        'studio'      => 'Studio',
        'bureau'      => 'Bureau',
        'commerce'    => 'Commerce',
        'terrain'     => 'Terrain',
    ];

    public const STATUTS = [
        'disponible' => 'Disponible',
        'loue'       => 'Loué',
        'en_travaux' => 'En travaux',
        'archive'    => 'Archivé',
    ];

    // Colonnes réelles de la table biens
    protected $fillable = [
        'agency_id',
        'proprietaire_id',
        'reference',
        'type',
        'adresse',
        'ville',
        'quartier',
        'commune',
        'surface_m2',
        'nombre_pieces',
        'meuble',
        'loyer_mensuel',    // ← colonne réelle
        'taux_commission',
        'statut',
        'description',
    ];

    protected $casts = [
        'loyer_mensuel'   => 'decimal:2',
        'surface_m2'      => 'decimal:2',
        'taux_commission' => 'decimal:2',
        'meuble'          => 'boolean',
        'deleted_at'      => 'datetime',
        // Note : pas de cast Enum — $bien->statut reste une string en Blade.
        // Utiliser BienStatut::from($bien->statut) dans le code PHP si l'enum est nécessaire.
    ];

    // ── Relations ─────────────────────────────────────────────────────────

    public function agency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function proprietaire(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }

    public function contrats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contrat::class);
    }

    public function contratActif(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Contrat::class)->where('statut', 'actif');
    }

    public function paiements(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Paiement::class, Contrat::class);
    }

    public function photos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BienPhoto::class)->orderBy('ordre');
    }

    // ── Accesseurs ────────────────────────────────────────────────────────

    // Alias loyer_hors_charges → loyer_mensuel pour compatibilité
    public function getLoyerHorsChargesAttribute(): float
    {
        return (float) $this->loyer_mensuel;
    }

    public function getLoyerTotalAttribute(): float
    {
        return (float) $this->loyer_mensuel;
    }

    public function getEstLoueAttribute(): bool
    {
        // $this->statut est une string — BienStatut::Loue->value donne 'loue'.
        return $this->statut === BienStatut::Loue->value;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type ?? '');
    }

    public function getStatutLabelAttribute(): string
    {
        // tryFrom() : si la valeur est reconnue, on utilise le label de l'enum.
        // Sinon fallback sur le tableau STATUTS ou ucfirst.
        $enum = BienStatut::tryFrom($this->statut ?? '');
        return $enum ? $enum->label() : (self::STATUTS[$this->statut] ?? ucfirst($this->statut ?? ''));
    }
}