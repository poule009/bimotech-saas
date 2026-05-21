<?php

namespace App\Models;

use App\Enums\BienStatut;
use App\Models\Concerns\HasAgencyScope;
use App\Models\Scopes\AgencyScope;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bien extends Model
{
    use HasFactory, HasAgencyScope, LogsActivity, SoftDeletes;

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
        'immeuble_id',
        'reference',
        'titre',
        'type',
        'adresse',
        'ville',
        'quartier',
        'commune',
        'surface_m2',
        'nombre_pieces',
        'meuble',
        'loyer_mensuel',    // ← prix de référence du bien (snapshot annonce) — NE PAS confondre avec Contrat.loyer_nu
        'taux_commission',
        'statut',
        'description',
        'visible_portail',
    ];

    protected $casts = [
        'loyer_mensuel'   => 'decimal:2',
        'surface_m2'      => 'decimal:2',
        'taux_commission' => 'decimal:2',
        'meuble'          => 'boolean',
        'visible_portail' => 'boolean',
        'deleted_at'      => 'datetime',
        // Note : pas de cast Enum — $bien->statut reste une string en Blade.
        // Utiliser BienStatut::from($bien->statut) dans le code PHP si l'enum est nécessaire.
    ];

    // ── Hooks ─────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::created(function (Bien $bien) {
            $base = implode('-', array_filter([
                $bien->type,
                $bien->quartier,
                $bien->ville,
            ]));
            // slug absent de $fillable → assignation directe.
            // saveQuietly() évite de redéclencher l'event created.
            $bien->slug = Str::slug($base) . '-' . $bien->id;
            $bien->saveQuietly();
        });
    }

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

    public function immeuble(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Immeuble::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeStandalone(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNull('immeuble_id');
    }

    public function scopeUnite(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNotNull('immeuble_id');
    }

    public function scopePortail(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        // Dans un scope, on ne peut pas retourner un nouveau builder.
        // withoutGlobalScope() sur $query est ce que withoutAgencyScope() fait sous le capot.
        return $query
            ->withoutGlobalScope(AgencyScope::class)
            ->where('statut', 'disponible')
            ->where('visible_portail', true)
            ->whereDoesntHave('contratActif')
            ->whereHas('agency', fn($q) => $q->where('actif', true))
            ->whereNotNull('titre')
            ->where('titre', '!=', '')
            ->whereNotNull('quartier')
            ->where('quartier', '!=', '')
            ->whereNotNull('slug')
            ->whereHas('photos')
            ->with([
                'photos'   => fn($q) => $q->where('est_principale', true),
                'agency',
                'immeuble:id,nom',
            ]);
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

    public function getTitreFallbackAttribute(): string
    {
        if (! empty($this->titre)) {
            return $this->titre;
        }

        // relationLoaded() évite un lazy load accidentel hors contexte portail.
        if ($this->immeuble_id && $this->relationLoaded('immeuble') && $this->immeuble) {
            return $this->immeuble->nom . ' — ' . $this->type_label;
        }

        return $this->type_label . (! empty($this->quartier) ? ' — ' . $this->quartier : '');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    public static function generateReference(int $agencyId): string
    {
        $agId    = str_pad($agencyId, 2, '0', STR_PAD_LEFT);
        $prefixe = 'BT-AG' . $agId . '-';

        // Verrou pessimiste pour éviter les doublons en cas de requêtes concurrentes.
        $derniere = static::withoutGlobalScope(\App\Models\Scopes\AgencyScope::class)
            ->where('agency_id', $agencyId)
            ->where('reference', 'like', $prefixe . '%')
            ->withTrashed()
            ->lockForUpdate()
            ->orderByDesc('reference')
            ->value('reference');

        $seq = $derniere
            ? ((int) substr($derniere, strrpos($derniere, '-') + 1)) + 1
            : 1;

        return $prefixe . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}