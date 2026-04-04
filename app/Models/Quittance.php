<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quittance extends Model
{
    use HasFactory, HasAgencyScope;

    protected $fillable = [
        'agency_id',
        'paiement_id',
        'contrat_id',
        'numero',
        'date_emission',
        'mois_concerne',
        'generee_par',
    ];

    protected $casts = [
        'date_emission' => 'date',
    ];

    // ─── Relations ──────────────────────────────────────────────────────────

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function generateurPar()
    {
        return $this->belongsTo(User::class, 'generee_par');
    }
}