<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory, HasAgencyScope;

    protected $fillable = [
        'agency_id',
        'contrat_id',
        'mois_concerne',
        'date_echeance',
        'loyer_hors_charges',
        'charges',
        'commission_ht',
        'tva_montant',
        'commission_ttc',
        'tom_montant',
        'net_proprietaire',
        'montant_total',
        'montant_recu',
        'date_reglement',
        'mode_paiement',
        'jours_retard',
        'statut',
        'valide_le',
        'valide_par',
    ];

    protected $casts = [
        'date_echeance'      => 'date',
        'date_reglement'     => 'date',
        'valide_le'          => 'datetime',
        'loyer_hors_charges' => 'decimal:0',
        'charges'            => 'decimal:0',
        'commission_ht'      => 'decimal:0',
        'tva_montant'        => 'decimal:0',
        'commission_ttc'     => 'decimal:0',
        'tom_montant'        => 'decimal:0',
        'net_proprietaire'   => 'decimal:0',
        'montant_total'      => 'decimal:0',
        'montant_recu'       => 'decimal:0',
    ];

    // ─── Relations ──────────────────────────────────────────────────────────

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    /**
     * La quittance générée après validation de ce paiement.
     * Relation 1-1 : un paiement validé → une seule quittance.
     */
    public function quittance()
    {
        return $this->hasOne(Quittance::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────

    public function scopeValides($query)
    {
        return $query->where('statut', 'valide');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEnRetard($query)
    {
        return $query->where('statut', 'en_attente')
                     ->where('date_echeance', '<', now()->toDateString());
    }
}