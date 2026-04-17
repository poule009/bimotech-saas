<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'statut',
        'date_debut_essai',
        'date_fin_essai',
        'plan',
        'montant_paye',
        'date_debut_abonnement',
        'date_fin_abonnement',
        'reference_paytech',
        'rappel_7j_envoye',
        'rappel_1j_envoye',
        'onboarding_j1_envoye',
        'onboarding_j7_envoye',
        'onboarding_j25_envoye',
    ];

    protected $casts = [
        'date_debut_essai'       => 'datetime',
        'date_fin_essai'         => 'datetime',
        'date_debut_abonnement'  => 'datetime',
        'date_fin_abonnement'    => 'datetime',
        'rappel_7j_envoye'       => 'boolean',
        'rappel_1j_envoye'       => 'boolean',
        'onboarding_j1_envoye'   => 'boolean',
        'onboarding_j7_envoye'   => 'boolean',
        'onboarding_j25_envoye'  => 'boolean',
        'montant_paye'           => 'decimal:2',
    ];

    // ── Tarifs en FCFA ────────────────────────────────────────────────────

    public const TARIFS = [
        'mensuel'      => 25000,
        'trimestriel'  => 67500,
        'semestriel'   => 127500,
        'annuel'       => 240000,
    ];

    public const LABELS = [
        'mensuel'      => 'Mensuel',
        'trimestriel'  => 'Trimestriel',
        'semestriel'   => 'Semestriel',
        'annuel'       => 'Annuel',
    ];

    public const DUREES_MOIS = [
        'mensuel'      => 1,
        'trimestriel'  => 3,
        'semestriel'   => 6,
        'annuel'       => 12,
    ];

    // ── Relations ─────────────────────────────────────────────────────────

    public function agency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    // ── Helpers statut ────────────────────────────────────────────────────

    public function estEnEssai(): bool
    {
        return $this->statut === 'essai'
            && $this->date_fin_essai
            && now()->lt($this->date_fin_essai);
    }

    public function essaiExpire(): bool
    {
        return $this->statut === 'essai'
            && $this->date_fin_essai
            && now()->gt($this->date_fin_essai);
    }

    public function estActif(): bool
    {
        return $this->statut === 'actif'
            && $this->date_fin_abonnement
            && now()->lt($this->date_fin_abonnement);
    }

    public function abonnementExpire(): bool
    {
        return $this->statut === 'actif'
            && $this->date_fin_abonnement
            && now()->gt($this->date_fin_abonnement);
    }

    public function aAcces(): bool
    {
        return $this->estEnEssai() || $this->estActif();
    }

    // ── Jours restants ────────────────────────────────────────────────────

    public function joursRestantsEssai(): int
    {
        if (! $this->date_fin_essai) return 0;
        return max(0, (int) now()->diffInDays($this->date_fin_essai, false));
    }

    public function joursRestantsAbonnement(): int
    {
        if (! $this->date_fin_abonnement) return 0;
        return max(0, (int) now()->diffInDays($this->date_fin_abonnement, false));
    }

    // ── Activer un abonnement + enregistrer le paiement ──────────────────

    public function activer(
        string $plan,
        ?string $referencePaydunya = null,
        string $methode = 'manuel'
    ): void {
        $dureeMois = self::DUREES_MOIS[$plan];
        $montant   = self::TARIFS[$plan];
        $debut     = now();
        $fin       = now()->addMonths($dureeMois);

        $this->update([
            'statut'                => 'actif',
            'plan'                  => $plan,
            'montant_paye'          => $montant,
            'date_debut_abonnement' => $debut,
            'date_fin_abonnement'   => $fin,
            'reference_paytech'     => $referencePaydunya,
            'rappel_7j_envoye'      => false,
            'rappel_1j_envoye'      => false,
        ]);

        // ── Enregistrer le paiement dans l'historique ─────────────────────
        SubscriptionPayment::create([
            'subscription_id' => $this->id,
            'agency_id'       => $this->agency_id,
            'plan'            => $plan,
            'montant'         => $montant,
            'statut'          => 'payé',
            'reference'       => $referencePaydunya,
            'methode'         => $methode,
            'periode_debut'   => $debut,
            'periode_fin'     => $fin,
        ]);
    }

    // ── Marquer comme expiré ──────────────────────────────────────────────

    public function marquerExpire(): void
    {
        $this->update(['statut' => 'expiré']);
    }
}