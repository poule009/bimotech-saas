<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory, HasAgencyScope;

    protected $fillable = [
        // agency_id en fillable car créé uniquement par code serveur contrôlé
        // (SuperAdmin, IPN PayTech vérifié HMAC, inscription) — jamais par form utilisateur
        'agency_id',
        'statut',
        'plan_niveau',
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

    // ── Niveaux de plan ───────────────────────────────────────────────────

    public const PLAN_STARTER = 'starter';
    public const PLAN_PRO     = 'pro';
    public const PLAN_AGENCE  = 'agence';
    public const PLAN_LEGACY  = 'legacy';

    // ── Tarifs en FCFA — [niveau][cycle] ─────────────────────────────────

    public const TARIFS = [
        'starter' => ['mensuel' => 19900, 'annuel' => 199000],
        'pro'     => ['mensuel' => 39900, 'annuel' => 399000],
        'agence'  => ['mensuel' => 69900, 'annuel' => 699000],
    ];

    public const LABELS = [
        'mensuel' => 'Mensuel',
        'annuel'  => 'Annuel',
    ];

    public const DUREES_MOIS = [
        'mensuel' => 1,
        'annuel'  => 12,
    ];

    // Durée de la période de grâce (lecture seule) après la fin d'essai ou d'échéance.
    public const GRACE_JOURS = 5;

    // ── État effectif du cycle de vie ─────────────────────────────────────
    // essai → (fin essai) → grâce (5j, lecture seule) → suspendu (bloqué)
    // actif → (échéance) → grâce (5j, lecture seule) → suspendu (bloqué)

    public const ETAT_ESSAI    = 'essai';
    public const ETAT_ACTIF    = 'actif';
    public const ETAT_GRACE    = 'grace';
    public const ETAT_SUSPENDU = 'suspendu';

    /**
     * État réel calculé depuis les dates (source de vérité pour l'accès),
     * indépendant de la colonne `statut` qui peut être en retard.
     */
    public function etatEffectif(): string
    {
        $now = now();

        // Abonnement payant en cours ou échu
        if ($this->statut === 'actif' && $this->date_fin_abonnement) {
            if ($now->lt($this->date_fin_abonnement)) {
                return self::ETAT_ACTIF;
            }
            return $now->lt($this->date_fin_abonnement->copy()->addDays(self::GRACE_JOURS))
                ? self::ETAT_GRACE
                : self::ETAT_SUSPENDU;
        }

        // Essai en cours ou échu
        if ($this->date_fin_essai) {
            if ($this->statut === 'essai' && $now->lt($this->date_fin_essai)) {
                return self::ETAT_ESSAI;
            }
            return $now->lt($this->date_fin_essai->copy()->addDays(self::GRACE_JOURS))
                ? self::ETAT_GRACE
                : self::ETAT_SUSPENDU;
        }

        // Aucune date exploitable → suspendu par sécurité
        return self::ETAT_SUSPENDU;
    }

    public function accesComplet(): bool { return in_array($this->etatEffectif(), [self::ETAT_ESSAI, self::ETAT_ACTIF], true); }
    public function enGrace(): bool      { return $this->etatEffectif() === self::ETAT_GRACE; }
    public function estSuspendu(): bool  { return $this->etatEffectif() === self::ETAT_SUSPENDU; }

    /** Jours restants avant suspension (pendant la grâce). */
    public function joursRestantsGrace(): int
    {
        $base = ($this->statut === 'actif' && $this->date_fin_abonnement)
            ? $this->date_fin_abonnement
            : $this->date_fin_essai;

        if (! $base) return 0;
        $fin = $base->copy()->addDays(self::GRACE_JOURS);
        return max(0, (int) now()->diffInDays($fin, false));
    }

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

    /**
     * Active/renouvelle l'abonnement (dates + niveau) SANS créer de ligne de paiement.
     * Utilisé par la confirmation manuelle (le paiement existe déjà) ET par activer().
     *
     * @param  string $cycle       mensuel|annuel
     * @param  string $planNiveau  starter|pro|agence
     * @return array{debut:\Carbon\Carbon,fin:\Carbon\Carbon,montant:float}
     */
    public function activerAbonnement(string $cycle, string $planNiveau): array
    {
        $dureeMois  = self::DUREES_MOIS[$cycle] ?? 1;
        $niveauPrix = array_key_exists($planNiveau, self::TARIFS) ? $planNiveau : 'pro';
        $montant    = self::TARIFS[$niveauPrix][$cycle] ?? self::TARIFS[$niveauPrix]['mensuel'];
        $debut = now();
        $fin   = $debut->copy()->addMonths($dureeMois);

        $this->update([
            'statut'                => 'actif',
            'plan'                  => $cycle,
            'plan_niveau'           => $planNiveau,
            'montant_paye'          => $montant,
            'date_debut_abonnement' => $debut,
            'date_fin_abonnement'   => $fin,
            'rappel_7j_envoye'      => false,
            'rappel_1j_envoye'      => false,
        ]);

        return ['debut' => $debut, 'fin' => $fin, 'montant' => $montant];
    }

    public function activer(
        string $plan,
        ?string $referencePaydunya = null,
        string $methode = 'manuel',
        string $planNiveau = 'pro'
    ): void {
        ['debut' => $debut, 'fin' => $fin, 'montant' => $montant] = $this->activerAbonnement($plan, $planNiveau);
        $this->update(['reference_paytech' => $referencePaydunya]);

        // ── Enregistrer le paiement dans l'historique ─────────────────────
        SubscriptionPayment::create([
            'subscription_id' => $this->id,
            'agency_id'       => $this->agency_id,
            'plan'            => $plan,
            'plan_niveau'     => $planNiveau,
            'montant'         => $montant,
            'statut'          => 'confirme',
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