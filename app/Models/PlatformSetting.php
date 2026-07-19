<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Réglage plateforme (paire clé/valeur). Ne pas lire/écrire directement :
 * passer par App\Support\PlatformSettings, qui porte les défauts et le cache.
 */
class PlatformSetting extends Model
{
    protected $fillable = ['cle', 'valeur'];

    protected $casts = [
        'valeur' => 'array',
    ];
}
