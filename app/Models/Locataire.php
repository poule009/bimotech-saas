<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locataire extends Model
{
    protected $fillable = [
        'user_id', 'cni', 'date_naissance', 'genre', 'nationalite',
        'profession', 'employeur', 'revenu_mensuel',
        'contact_urgence_nom', 'contact_urgence_tel', 'contact_urgence_lien',
        'adresse_precedente', 'ville', 'quartier',
        'cni_verified', 'justif_revenus_fourni',
    ];

    protected $casts = [
        'date_naissance'         => 'date',
        'cni_verified'           => 'boolean',
        'justif_revenus_fourni'  => 'boolean',
        'revenu_mensuel'         => 'decimal:2',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Ses contrats de location
    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'locataire_id', 'user_id');
    }

    // Contrat actif en cours
    public function contratActif()
    {
        return $this->hasOne(Contrat::class, 'locataire_id', 'user_id')
            ->where('statut', 'actif');
    }

    // Ses paiements via ses contrats
    public function paiements()
    {
        return Paiement::whereIn(
            'contrat_id',
            $this->contrats()->pluck('id')
        )->orderByDesc('periode')->get();
    }

    // ── Accesseurs ───────────────────────────────────────────────────────────

    public function getNomCompletAttribute(): string
    {
        return $this->user->name;
    }

    public function getTauxEffortAttribute(): ?string
    {
        if (! $this->revenu_mensuel || ! $this->contratActif) return null;
        $taux = ($this->contratActif->loyer_contractuel / $this->revenu_mensuel) * 100;
        return round($taux, 1) . '%';
    }
}