<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtatTrimestrielDownload extends Model
{
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
