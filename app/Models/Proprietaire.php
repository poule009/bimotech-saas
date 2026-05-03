<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;

class Proprietaire extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('agency', function ($builder) {
            if (! Auth::check() || Auth::user()->role === 'superadmin') {
                return;
            }
            if (! Auth::user()->agency_id) {
                $builder->whereRaw('1 = 0');
                return;
            }
            $agencyId = Auth::user()->agency_id;
            $builder->whereHas('user', fn($q) => $q->where('agency_id', $agencyId));
        });
    }

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

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Biens appartenant à ce propriétaire (filtrés par agence via HasAgencyScope). */
    public function biens(): HasMany
    {
        return $this->hasMany(Bien::class, 'proprietaire_id', 'user_id');
    }

    /**
     * Contrats en cours via les biens (HasManyThrough).
     * Chemin : Proprietaire.user_id → Bien.proprietaire_id → Contrat.bien_id
     */
    public function contrats(): HasManyThrough
    {
        return $this->hasManyThrough(
            Contrat::class,    // Modèle final
            Bien::class,       // Modèle intermédiaire
            'proprietaire_id', // FK sur Bien → Proprietaire.user_id
            'bien_id',         // FK sur Contrat → Bien.id
            'user_id',         // Clé locale sur Proprietaire
            'id'               // Clé locale sur Bien
        );
    }

    /**
     * Contrats actifs (résultat en collection, usage hors Eloquent).
     */
    public function contratsActifs()
    {
        return Contrat::whereIn('bien_id', $this->biens()->pluck('id'))
            ->where('statut', 'actif')
            ->get();
    }

    /**
     * Builder de paiements isolé par agence — point d'entrée du BailleurController.
     *
     * Sécurité : double filtre agency_id + proprietaire_id.
     * Les dépenses de gestion sont eager-loadées pour l'accesseur net_final_bailleur.
     *
     * @param  int $agencyId  agency_id de l'utilisateur connecté
     * @return Builder        prêt pour ->get(), ->paginate(), ->sum()…
     */
    public function paiementsQuery(int $agencyId): Builder
    {
        $bienIds    = $this->biens()->pluck('biens.id');
        $contratIds = Contrat::whereIn('bien_id', $bienIds)->pluck('id');

        return Paiement::where('agency_id', $agencyId)
                       ->whereIn('contrat_id', $contratIds);
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