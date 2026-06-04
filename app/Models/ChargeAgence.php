<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargeAgence extends Model
{
    use HasFactory, HasAgencyScope;

    protected $table = 'charges_agence';

    protected $fillable = [
        'agency_id',
        'libelle',
        'montant',
        'categorie',
        'date_charge',
        'periode',
        'prestataire',
        'justificatif_path',
        'notes',
    ];

    protected $casts = [
        'montant'     => 'decimal:2',
        'date_charge' => 'date',
    ];

    public const CATEGORIES = [
        'salaires'        => 'Salaires',
        'loyer_bureau'    => 'Loyer bureau',
        'telephone'       => 'Téléphone',
        'eau_electricite' => 'Eau & Électricité',
        'carburant'       => 'Carburant',
        'fournitures'     => 'Fournitures',
        'publicite'       => 'Publicité',
        'assurance'       => 'Assurance',
        'autre'           => 'Autre',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $charge) {
            if (empty($charge->agency_id) && auth()->check()) {
                $charge->agency_id = auth()->user()->agency_id;
            }
            if (empty($charge->periode) && $charge->date_charge) {
                $charge->periode = $charge->date_charge->format('Y-m');
            }
        });
    }

    public function getCategorieLibelleAttribute(): string
    {
        return self::CATEGORIES[$this->categorie] ?? ucfirst($this->categorie);
    }
}
