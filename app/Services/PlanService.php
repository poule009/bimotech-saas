<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\PlanPriceHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Accès aux tarifs et limites de plan — SOURCE UNIQUE.
 *
 * Avant juillet 2026 ces valeurs étaient dupliquées dans Subscription::TARIFS,
 * dans du SQL en dur et dans config/plans.php ; elles divergeaient au premier
 * changement. Tout passe désormais par ici. Ne pas relire la table `plans`
 * directement ailleurs, et ne jamais réintroduire un tarif en dur.
 *
 * ⚠️ RÈGLE MÉTIER — un tarif lu ici est le tarif des NOUVELLES souscriptions et
 * des renouvellements à venir. Pour savoir ce qu'une agence paie sur son cycle
 * en cours, lire le snapshot `subscriptions.montant_paye` (ou
 * `subscription_payments.montant`), jamais ce service : une hausse de tarif ne
 * doit jamais remonter rétroactivement sur un cycle déjà engagé.
 */
class PlanService
{
    private const CACHE_KEY = 'plans.all';
    private const CACHE_TTL = 3600;

    /** Tous les plans, indexés par niveau, triés par ordre d'affichage. */
    public function all(): Collection
    {
        return Cache::remember(
            self::CACHE_KEY,
            self::CACHE_TTL,
            fn () => Plan::orderBy('ordre')->get()->keyBy('niveau')
        );
    }

    public function find(?string $niveau): ?Plan
    {
        return $this->all()->get($niveau ?: 'legacy');
    }

    /** Plans réellement souscriptibles (Legacy exclu — plan figé). */
    public function souscriptibles(): Collection
    {
        return $this->all()->filter(fn (Plan $p) => $p->souscriptible);
    }

    /** Prix d'un plan pour un cycle. 0 si plan inconnu (Legacy = gratuit). */
    public function prix(?string $niveau, string $cycle = 'mensuel'): int
    {
        return $this->find($niveau)?->prix($cycle) ?? 0;
    }

    /** Limite de biens — null = illimité. */
    public function limiteUnites(?string $niveau): ?int
    {
        return $this->find($niveau)?->limite_unites;
    }

    /** Limite de comptes admins — null = illimité. */
    public function limiteAdmins(?string $niveau): ?int
    {
        return $this->find($niveau)?->limite_admins;
    }

    /**
     * Libellé destiné à l'AGENCE (« Pro » pour Legacy — un client Legacy n'a
     * jamais eu à connaître ce plan technique). Pour le back-office, utiliser
     * $plan->libelle, qui nomme Legacy « Legacy ».
     */
    public function label(?string $niveau): string
    {
        return $this->find($niveau)?->libelle_public ?? ucfirst((string) $niveau);
    }

    /**
     * Compare deux plans par leur prix mensuel : >0 si $a est au-dessus de $b.
     * Le classement suit le prix, pas un ordre codé en dur — si le Super Admin
     * change les tarifs, « upgrade » suit automatiquement.
     */
    public function comparer(?string $a, ?string $b): int
    {
        return $this->prix($a) <=> $this->prix($b);
    }

    public function estUpgrade(?string $depuis, ?string $vers): bool
    {
        return $this->comparer($vers, $depuis) > 0;
    }

    /**
     * Applique des modifications à un plan et journalise chaque champ changé.
     *
     * @param  array<string,int|null>  $valeurs  clés limitées à Plan::CHAMPS_EDITABLES
     * @return int  nombre de champs effectivement modifiés
     */
    public function update(Plan $plan, array $valeurs, ?int $userId): int
    {
        // Legacy : plan figé pour les clients beta historiques. La garde est ici
        // (et pas seulement dans le contrôleur) pour couvrir tout appelant futur.
        abort_if($plan->verrouille, 403, 'Le plan '.$plan->libelle.' est verrouillé.');

        $valeurs = array_intersect_key($valeurs, array_flip(Plan::CHAMPS_EDITABLES));
        $changes = 0;

        DB::transaction(function () use ($plan, $valeurs, $userId, &$changes) {
            foreach ($valeurs as $champ => $nouvelle) {
                $ancienne = $plan->{$champ};

                // Comparaison souple : null (illimité) et 0 sont distincts, mais
                // "50" venant du form doit égaler 50 en base.
                if ($ancienne === $nouvelle || (string) $ancienne === (string) $nouvelle) {
                    continue;
                }

                PlanPriceHistory::create([
                    'plan_id'         => $plan->id,
                    'user_id'         => $userId,
                    'champ'           => $champ,
                    'ancienne_valeur' => $ancienne === null ? null : (string) $ancienne,
                    'nouvelle_valeur' => $nouvelle === null ? null : (string) $nouvelle,
                ]);

                $plan->{$champ} = $nouvelle;
                $changes++;
            }

            if ($changes > 0) {
                $plan->save();
            }
        });

        if ($changes > 0) {
            $this->flush();
        }

        return $changes;
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
