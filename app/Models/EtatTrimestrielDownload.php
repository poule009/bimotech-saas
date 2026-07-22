<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class EtatTrimestrielDownload extends Model
{
    use HasAgencyScope;

    /** Auto-injection de l'agence à la création (défense en profondeur). */
    protected static function booted(): void
    {
        static::creating(function (self $download) {
            if (empty($download->agency_id) && Auth::check()) {
                $download->agency_id = Auth::user()->agency_id;
            }
        });
    }

    protected $fillable = [
        'agency_id',
        'trimestre',
        'annee',
        'type',
        'downloaded_at',
        'downloaded_by',
    ];

    protected $casts = [
        'trimestre'     => 'integer',
        'annee'         => 'integer',
        'downloaded_at' => 'datetime',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function downloadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'downloaded_by');
    }
}
