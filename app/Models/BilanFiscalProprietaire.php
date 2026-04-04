<?php

namespace App\Models;

use App\Models\Scopes\AgencyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BilanFiscalProprietaire extends Model
{
    protected $table = 'bilans_fiscaux_proprietaires';

    protected $fillable = [
        'agency_id', 'proprietaire_id', 'annee',
        'revenus_bruts_loyers', 'revenus_bruts_charges', 'revenus_bruts_total',
        'abattement_forfaitaire_30', 'base_imposable', 'irpp_estime',
        'cfpb_estimee', 'tva_loyer_collectee', 'brs_retenu_total',
        'commissions_agence_ht', 'tva_commissions',
        'net_proprietaire_total', 'nb_paiements', 'nb_biens_geres',
        'calcule_le',
    ];

    protected $casts = [
        'revenus_bruts_loyers'      => 'decimal:2',
        'revenus_bruts_charges'     => 'decimal:2',
        'revenus_bruts_total'       => 'decimal:2',
        'abattement_forfaitaire_30' => 'decimal:2',
        'base_imposable'            => 'decimal:2',
        'irpp_estime'               => 'decimal:2',
        'cfpb_estimee'              => 'decimal:2',
        'tva_loyer_collectee'       => 'decimal:2',
        'brs_retenu_total'          => 'decimal:2',
        'commissions_agence_ht'     => 'decimal:2',
        'tva_commissions'           => 'decimal:2',
        'net_proprietaire_total'    => 'decimal:2',
        'calcule_le'                => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AgencyScope());

        static::creating(function (self $bilan) {
            if (empty($bilan->agency_id) && Auth::check()) {
                $bilan->agency_id = Auth::user()->agency_id;
            }
        });
    }

    public function proprietaire()
    {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}