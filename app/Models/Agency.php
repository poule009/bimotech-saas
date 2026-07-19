<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agency extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Scope global « périmètre Super Admin ».
     *
     * Un collaborateur super-admin à accès restreint (ou l'admin principal en mode
     * « Voir comme ») ne voit QUE ses agences apportées : le filtre amenee_par est
     * appliqué ici, une fois pour toutes, si bien que dashboard, liste, KPI et MRR
     * (qui itèrent tous sur des collections Agency) sont scopés automatiquement.
     *
     * Périmètre null (admin principal, ou tout rôle non super-admin) → aucun filtre.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('sa_perimetre', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $id = app(\App\Support\SuperAdminContext::class)->perimetreAdminId();
            if ($id !== null) {
                $builder->where($builder->getModel()->getTable() . '.amenee_par', $id);
            }
        });
    }

    /** Échappe le scope de périmètre (opérations plateforme : capture, migrations). */
    public static function sansPerimetre(): \Illuminate\Database\Eloquent\Builder
    {
        return static::withoutGlobalScope('sa_perimetre');
    }

    public function getActivityLogTitle(): string
    {
        return 'Agence ' . ($this->name ?? '#' . $this->id);
    }

    protected static array $activityFieldLabels = [
        'name'                 => 'Nom',
        'email'                => 'Email',
        'telephone'            => 'Téléphone',
        'adresse'              => 'Adresse',
        'couleur_primaire'     => 'Couleur principale',
        'logo_path'            => 'Logo',
        'ninea'                => 'NINEA',
        'actif'                => 'Actif',
        'slogan'               => 'Slogan vitrine',
        'onboarding_completed' => 'Onboarding terminé',
    ];

    /**
     * SÉCURITÉ — Mass Assignment :
     *
     * `actif` et `slug` sont INTENTIONNELLEMENT absents de $fillable.
     *
     * - `actif`  : seul le SuperAdmin peut activer/désactiver une agence.
     *              Si un admin pouvait le modifier via un formulaire, il pourrait
     *              désactiver sa propre agence ou (via une faille) celle d'un concurrent.
     *
     * - `slug`   : identifiant unique d'URL généré à la création. Le modifier
     *              pourrait casser des liens existants ou créer des conflits.
     *
     * Ces deux colonnes sont modifiées uniquement via assignation directe
     * dans SuperAdminController ou dans les migrations de données.
     */
    protected $fillable = [
        'name',
        'email',
        'telephone',
        'logo_path',
        'logo_dark_path',
        'signature_path',
        'cachet_path',
        'modele_contrat',
        'couleur_primaire',
        'adresse',
        'ninea',
        'rccm',
        'onboarding_completed',
        'taux_tva',
        'assujetti_tva',
        'whatsapp',
        'slogan',
    ];

    protected $casts = [
        'actif'                => 'boolean',
        'onboarding_completed' => 'boolean',
        'taux_tva'             => 'decimal:2',
        'assujetti_tva'        => 'boolean',
    ];

    // ── Relations ─────────────────────────────────────────────────────────

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function biens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Bien::class);
    }

    public function contrats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contrat::class);
    }

    public function paiements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function subscription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function featureOverrides(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AgencyFeatureOverride::class);
    }

    /**
     * Collaborateur (compte super-admin) qui a apporté l'agence — « Amenée par ».
     * Nullable = « Non attribué ». Champ interne au back-office, jamais exposé
     * à l'inscription publique ni à l'agence. Modifié uniquement par le principal.
     */
    public function ameneePar(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'amenee_par');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    public function isActif(): bool
    {
        return $this->actif === true;
    }

    public function aAccesAbonnement(): bool
    {
        return $this->subscription && $this->subscription->aAcces();
    }

    public function nbUnitesActives(): int
    {
        return $this->biens()->where('statut', '!=', 'archive')->count();
    }

    public function getNbUnitesActivesAttribute(): int
    {
        return $this->nbUnitesActives();
    }

    // ── Limites du plan (source unique : config/plans.php) ─────────────────
    // Toute vérification de quota DOIT passer par ces helpers — ne jamais
    // recoder les seuils en dur ailleurs (sinon divergence silencieuse).

    /**
     * Niveau de plan EFFECTIF pour cette agence.
     * Résout legacy→pro et traite l'absence d'abonnement comme 'legacy'.
     */
    public function planNiveauEffectif(): string
    {
        return self::niveauEffectif($this->subscription?->plan_niveau);
    }

    /** Niveau effectif pour un plan_niveau donné (null/absent = legacy→pro). */
    public static function niveauEffectif(?string $planNiveau): string
    {
        return config('plans.niveau_effectif.' . ($planNiveau ?: 'legacy'), 'pro');
    }

    /** Limite de biens (unités) du plan courant — null = illimité. */
    public function limiteUnites(): ?int
    {
        return self::limiteUnitesPour($this->subscription?->plan_niveau);
    }

    /**
     * Limite de biens pour un plan_niveau donné — null = illimité.
     *
     * Lue sur la ligne du plan lui-même (table `plans`), sans passer par
     * niveau_effectif : Legacy porte ses propres limites, figées. La résolution
     * legacy→pro ne concerne que l'accès aux fonctionnalités, pas les compteurs.
     */
    public static function limiteUnitesPour(?string $planNiveau): ?int
    {
        return app(\App\Services\PlanService::class)->limiteUnites($planNiveau);
    }

    /** Limite de comptes admins du plan courant — null = illimité. */
    public function limiteAdmins(): ?int
    {
        return app(\App\Services\PlanService::class)->limiteAdmins($this->subscription?->plan_niveau);
    }

    public function couleurEstSombre(): bool
    {
        $hex = ltrim($this->couleur_primaire ?? '#1a3c5e', '#');

        if (strlen($hex) !== 6) {
            return true;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $luminosite = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminosite < 0.5;
    }

    public function classeTexteNav(): string
    {
        return $this->couleurEstSombre() ? 'text-white' : 'text-gray-800';
    }

    public function classeTexteNavHover(): string
    {
        return $this->couleurEstSombre() ? 'hover:text-gray-200' : 'hover:text-gray-600';
    }

    // ── Onboarding ────────────────────────────────────────────────────────

 public function checkOnboarding(): array
{
    $etape1 = ! empty($this->logo_path) && ! empty($this->ninea);
    $etape2 = $this->users()->where('role', 'proprietaire')->exists();
    $etape3 = $this->biens()->exists();
    $etape4 = $this->contrats()->where('statut', 'actif')->exists();

    $nbCompletes = collect([$etape1, $etape2, $etape3, $etape4])->filter()->count();

    if ($nbCompletes === 4 && ! $this->onboarding_completed) {
        $this->update(['onboarding_completed' => true]);
    }

    return [
        'etape1'       => $etape1,
        'etape2'       => $etape2,
        'etape3'       => $etape3,
        'etape4'       => $etape4,
        'nb_completes' => $nbCompletes,
        'total'        => 4,
    ];
}
}