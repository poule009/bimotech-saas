<?php

namespace App\Models;

use App\Models\Scopes\AgencyScope;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Bien extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    // ── Types de biens courants au Sénégal ────────────────────────────────
    public const TYPES = [
        'appartement'       => 'Appartement',
        'villa'             => 'Villa',
        'studio'            => 'Studio',
        'chambre'           => 'Chambre',
        'bureau'            => 'Bureau',
        'local_commercial'  => 'Local commercial',
        'entrepot'          => 'Entrepôt',
        'terrain'           => 'Terrain',
        'maison'            => 'Maison',
        'duplex'            => 'Duplex',
    ];

    public const STATUTS = [
        'disponible'  => 'Disponible',
        'loue'        => 'Loué',
        'en_travaux'  => 'En travaux',
    ];

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
        'loyer_mensuel',
        'taux_commission',
        'statut',
        'description',
    ];

    protected $casts = [
        'loyer_mensuel'   => 'decimal:2',
        'taux_commission' => 'decimal:2',
        'surface_m2'      => 'integer',
        'nombre_pieces'   => 'integer',
        'meuble'          => 'boolean',
    ];

    // ── Global Scope ──────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::addGlobalScope(new AgencyScope());

        static::creating(function (Bien $bien) {
            if (empty($bien->agency_id) && Auth::check()) {
                $bien->agency_id = Auth::user()->agency_id;
            }
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

    public function photos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BienPhoto::class)->orderBy('ordre');
    }

    public function photoPrincipale(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BienPhoto::class)->where('est_principale', true);
    }
}