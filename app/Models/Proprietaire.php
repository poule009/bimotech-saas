<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proprietaire extends Model
{
    protected $fillable = [
        'user_id', 'cni', 'date_naissance', 'genre', 'nationalite',
        'telephone_secondaire', 'adresse_domicile', 'ville', 'quartier',
        'mode_paiement_prefere', 'banque', 'numero_compte',
        'numero_wave', 'numero_om', 'ninea', 'assujetti_tva',
    ];

    protected $casts = [
        'date_naissance'  => 'date',
        'assujetti_tva'   => 'boolean',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    // Appartient à un User (auth)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Ses biens immobiliers
    public function biens()
    {
        return $this->hasMany(Bien::class, 'proprietaire_id', 'user_id');
    }

    // Ses contrats actifs (via ses biens)
    public function contratsActifs()
    {
        return Contrat::whereIn('bien_id', $this->biens()->pluck('id'))
            ->where('statut', 'actif')
            ->get();
    }

    // ── Accesseurs ───────────────────────────────────────────────────────────

    public function getNomCompletAttribute(): string
    {
        return $this->user->name;
    }

    public function getEmailAttribute(): string
    {
        return $this->user->email;
    }

    public function getTelephoneAttribute(): string
    {
        return $this->user->telephone ?? '';
    }
}