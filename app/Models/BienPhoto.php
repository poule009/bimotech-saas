<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BienPhoto extends Model
{
    protected $fillable = [
        'bien_id', 'chemin', 'nom_original',
        'est_principale', 'ordre',
    ];

    protected $casts = [
        'est_principale' => 'boolean',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    // URL complète de l'image
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->chemin);
    }
}