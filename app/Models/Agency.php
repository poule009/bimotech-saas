<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agency extends Model
{
    use HasFactory;

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
        'signature_path',
        'modele_contrat',
        'couleur_primaire',
        'adresse',
        'ninea',
        'rccm',
        'onboarding_completed',
        'taux_tva',
    ];

    protected $casts = [
        'actif'                => 'boolean',
        'onboarding_completed' => 'boolean',
        'taux_tva'             => 'decimal:2',
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

    // ── Helpers ───────────────────────────────────────────────────────────

    public function isActif(): bool
    {
        return $this->actif === true;
    }

    public function aAccesAbonnement(): bool
    {
        return $this->subscription && $this->subscription->aAcces();
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