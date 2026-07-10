<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Lot d'import (voir migration create_import_batches_table).
 *
 * Cycle de vie : preview → committed → annule.
 * Les lignes parsées et leur verdict vivent dans `rows` (JSON) jusqu'au commit.
 */
class ImportBatch extends Model
{
    protected $fillable = [
        'agency_id', 'user_id', 'type', 'statut',
        'original_filename', 'rows', 'codes',
        'nb_total', 'nb_valides', 'nb_erreurs', 'nb_doublons', 'nb_crees',
        'committed_at', 'annule_at',
    ];

    protected $casts = [
        'rows'         => 'array',
        'codes'        => 'array',
        'committed_at' => 'datetime',
        'annule_at'    => 'datetime',
    ];

    /** Isolation par agence (mêmes règles que le reste de l'app). */
    protected static function booted(): void
    {
        static::addGlobalScope('agency', function ($builder) {
            if (! Auth::check() || Auth::user()->role === 'superadmin') {
                return;
            }
            if (! Auth::user()->agency_id) {
                $builder->whereRaw('1 = 0');
                return;
            }
            $builder->where('import_batches.agency_id', Auth::user()->agency_id);
        });
    }

    public const TYPES = [
        'proprietaires' => 'Propriétaires',
        'biens'         => 'Biens',
        'locataires'    => 'Locataires',
        'contrats'      => 'Contrats',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    /** Lignes valides (non doublon, non erreur) — utilisées au commit. */
    public function lignesValides(): array
    {
        return array_values(array_filter(
            $this->rows ?? [],
            fn ($r) => ($r['status'] ?? null) === 'valid'
        ));
    }
}
