<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScopeThroughUser;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proprietaire extends Model
{
    // Isolation par agence via la relation user (users.agency_id).
    use LogsActivity, SoftDeletes, HasAgencyScopeThroughUser;

    protected $fillable = [
        'user_id', 'cni', 'date_naissance', 'genre', 'nationalite',
        'telephone_secondaire', 'adresse_domicile', 'ville', 'quartier',
        'piece_identite_path',
        'mode_paiement_prefere', 'banque', 'numero_compte',
        'numero_wave', 'numero_om', 'ninea', 'assujetti_tva',
        'est_personne_morale_is',
        'brs_dispense',           // BRS — dispense explicite de retenue (opt-out, niveau bailleur)
        'forme_juridique_bailleur',
        'code_import', 'import_batch_id',
        // CGF — option régime synthétique (Art. 75 CGI SN)
        'cgf_active', 'cgf_annee', 'cgf_revenu_brut_prevu',
        'cgf_montant', 'cgf_mode_paiement', 'cgf_echeances',
    ];

    protected $casts = [
        'date_naissance'          => 'date',
        'assujetti_tva'           => 'boolean',
        'est_personne_morale_is'  => 'boolean',
        'brs_dispense'            => 'boolean',
        'cgf_active'              => 'boolean',
        'cgf_echeances'           => 'array',
        'deleted_at'              => 'datetime',
    ];

    /**
     * La CGF est-elle active et couvre-t-elle l'année donnée ?
     * Sert à l'exclusion mutuelle avec les encarts IRPP-foncier et CFPB (CGF-02) :
     * si vrai, ces calculs parallèles doivent être MASQUÉS pour cette année.
     */
    public function cgfCouvre(int $annee): bool
    {
        return $this->cgf_active && (int) $this->cgf_annee === $annee;
    }

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