<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Plan d'abonnement — tarifs et limites.
 *
 * Lire les plans via PlanService (cache + garanties), pas directement par ce
 * modèle : c'est le service qui est la porte d'entrée documentée.
 *
 * ⚠️ Le prix d'un plan est le prix des PROCHAINES souscriptions et des
 * renouvellements. Il ne décrit JAMAIS ce qu'une agence paie aujourd'hui —
 * ça, c'est `subscriptions.montant_paye` / `subscription_payments.montant`,
 * figés au moment de l'encaissement.
 */
class Plan extends Model
{
    protected $fillable = [
        'niveau',
        'libelle',
        'libelle_public',
        'prix_mensuel',
        'prix_annuel',
        'limite_unites',
        'limite_admins',
        'verrouille',
        'souscriptible',
        'ordre',
    ];

    protected $casts = [
        'prix_mensuel'  => 'integer',
        'prix_annuel'   => 'integer',
        'limite_unites' => 'integer',
        'limite_admins' => 'integer',
        'verrouille'    => 'boolean',
        'souscriptible' => 'boolean',
    ];

    /** Champs que le Super Admin peut modifier (écran « Configuration des plans »). */
    public const CHAMPS_EDITABLES = ['prix_mensuel', 'prix_annuel', 'limite_unites', 'limite_admins'];

    /**
     * Toute écriture sur un plan invalide le cache de PlanService — y compris
     * hors de son update() (seeder, tinker, migration). Sans ça, un tarif modifié
     * continuerait d'être servi depuis le cache jusqu'à son expiration.
     */
    protected static function booted(): void
    {
        static::saved(fn () => app(\App\Services\PlanService::class)->flush());
        static::deleted(fn () => app(\App\Services\PlanService::class)->flush());
    }

    public function historique(): HasMany
    {
        return $this->hasMany(PlanPriceHistory::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_niveau', 'niveau');
    }

    /** Tarif pour un cycle donné (mensuel|annuel). */
    public function prix(string $cycle): int
    {
        return $cycle === 'annuel' ? $this->prix_annuel : $this->prix_mensuel;
    }

    /** Équivalent mensuel du tarif, pour comparer deux cycles. */
    public function prixMensuelEquivalent(string $cycle): int
    {
        return $cycle === 'annuel' ? intdiv($this->prix_annuel, 12) : $this->prix_mensuel;
    }
}
