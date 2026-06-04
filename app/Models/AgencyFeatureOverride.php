<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyFeatureOverride extends Model
{
    protected $fillable = ['agency_id', 'feature', 'enabled'];

    protected $casts = ['enabled' => 'boolean'];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
