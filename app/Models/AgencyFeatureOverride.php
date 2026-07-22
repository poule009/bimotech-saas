<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgencyFeatureOverride extends Model
{
    // Le SuperAdmin (qui gère les overrides de n'importe quelle agence) bypasse
    // ce scope ; un admin ne peut lire/écrire que les overrides de son agence.
    use HasAgencyScope;

    protected $fillable = ['agency_id', 'feature', 'enabled'];

    protected $casts = ['enabled' => 'boolean'];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
