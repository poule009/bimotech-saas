<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Immeuble extends Model
{
    use HasFactory, HasAgencyScope, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'proprietaire_id',
        'nom',
        'adresse',
        'ville',
        'nombre_niveaux',
        'description',
    ];

    protected $casts = [
        'nombre_niveaux' => 'integer',
        'deleted_at'     => 'datetime',
    ];

    // agency() est fourni par HasAgencyScope

    public function proprietaire(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }

    public function biens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Bien::class);
    }
}
