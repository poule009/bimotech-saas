<?php

namespace App\Models;

use App\Models\Scopes\AgencyScope;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Contrat extends Model
{
    use HasFactory, LogsActivity;

    // ── Types de bail ─────────────────────────────────────────────────────
    public const TYPES_BAIL = [
        'habitation'  => "Bail d'habitation",
        'commercial'  => 'Bail commercial',
        'mixte'       => 'Bail mixte',
        'saisonnier'  => 'Bail saisonnier',
    ];

    public const STATUTS = [
        'actif'    => 'Actif',
        'resilié'  => 'Résilié',
        'expiré'   => 'Expiré',
    ];

    protected $fillable = [
        'agency_id',
        'bien_id',
        'locataire_id',
        'date_debut',
        'date_fin',
        'loyer_contractuel',
        'caution',
        'statut',
        'type_bail',
        'frais_agence',
        'charges_mensuelles',
        'indexation_annuelle',
        'nombre_mois_caution',
        'garant_nom',
        'garant_telephone',
        'garant_adresse',
        'observations',
    ];

    protected $casts = [
        'date_debut'          => 'date',
        'date_fin'            => 'date',
        'loyer_contractuel'   => 'decimal:2',
        'caution'             => 'decimal:2',
        'frais_agence'        => 'decimal:2',
        'charges_mensuelles'  => 'decimal:2',
        'indexation_annuelle' => 'decimal:2',
        'nombre_mois_caution' => 'integer',
    ];

    // ── Global Scope ──────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::addGlobalScope(new AgencyScope());

        static::creating(function (Contrat $contrat) {
            if (empty($contrat->agency_id) && Auth::check()) {
                $contrat->agency_id = Auth::user()->agency_id;
            }
        });
    }

    // ── Relations ─────────────────────────────────────────────────────────

    public function agency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function bien(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Bien::class);
    }

    public function locataire(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'locataire_id');
    }

    public function paiements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Paiement::class);
    }
}