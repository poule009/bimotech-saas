<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReversementProprietaire extends Model
{
    use HasFactory, HasAgencyScope;

    protected $table = 'reversements_proprietaires';

    protected $fillable = [
        'agency_id',
        'proprietaire_id',
        'montant',
        'date_reversement',
        'mode_paiement',
        'reference',
        'periode_debut',
        'periode_fin',
        'notes',
    ];

    protected $casts = [
        'montant'          => 'decimal:2',
        'date_reversement' => 'date',
    ];

    public const MODES_PAIEMENT = [
        'virement'      => 'Virement bancaire',
        'wave'          => 'Wave',
        'orange_money'  => 'Orange Money',
        'especes'       => 'Espèces',
        'cheque'        => 'Chèque',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $reversement) {
            if (empty($reversement->agency_id) && auth()->check()) {
                $reversement->agency_id = auth()->user()->agency_id;
            }
        });
    }

    public function proprietaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }

    public function getModePaiementLibelleAttribute(): string
    {
        return self::MODES_PAIEMENT[$this->mode_paiement] ?? ucfirst($this->mode_paiement);
    }
}
