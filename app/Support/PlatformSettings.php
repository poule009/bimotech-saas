<?php

namespace App\Support;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Cache;

/**
 * Source de vérité applicative des réglages globaux de la plateforme
 * (module Super Admin « Paramètres système »).
 *
 * Porte les défauts, lit/écrit la table platform_settings et met en cache la
 * carte complète — le middleware de maintenance interroge ce service à chaque
 * requête web, la lecture doit donc rester bon marché.
 *
 * Résolu en singleton (voir AppServiceProvider) pour partager le cache mémoire
 * à l'intérieur d'une même requête.
 */
class PlatformSettings
{
    private const CACHE_KEY = 'platform_settings.map';

    /** Défauts — l'absence d'entrée en base retombe ici. */
    public const DEFAULTS = [
        // Général — le nom retombe sur config('app.name') s'il est vide.
        'plateforme_nom'              => null,
        'support_email'               => 'support@bimo-tech.sn',
        'support_telephone'           => '',

        // Mode maintenance (blocage total côté agences ; le Super Admin reste ouvert).
        'maintenance_active'          => false,
        'maintenance_message'         => "La plateforme est momentanément en maintenance. "
            ."Nos équipes interviennent et l'accès sera rétabli dans quelques instants. Merci de votre patience.",

        // Sécurité des comptes admin (toggles ON par défaut, cf. maquette).
        'securite_2fa_obligatoire'    => true,
        'securite_session_expiration' => true,
        'securite_session_minutes'    => 30,
        'securite_mdp_renforce'       => true,
    ];

    /** @var array<string,mixed>|null Cache mémoire intra-requête. */
    private ?array $memo = null;

    /** Carte complète des réglages (base fusionnée sur les défauts). */
    public function all(): array
    {
        if ($this->memo !== null) {
            return $this->memo;
        }

        $stored = Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return PlatformSetting::pluck('valeur', 'cle')->all();
            } catch (\Throwable) {
                // Table absente (avant migration) : on retombe sur les défauts.
                return [];
            }
        });

        return $this->memo = array_merge(self::DEFAULTS, $stored);
    }

    public function get(string $cle, mixed $default = null): mixed
    {
        return $this->all()[$cle] ?? $default;
    }

    public function set(string $cle, mixed $valeur): void
    {
        $this->setMany([$cle => $valeur]);
    }

    /** @param array<string,mixed> $paires */
    public function setMany(array $paires): void
    {
        foreach ($paires as $cle => $valeur) {
            PlatformSetting::updateOrCreate(['cle' => $cle], ['valeur' => $valeur]);
        }

        $this->flush();
    }

    public function flush(): void
    {
        $this->memo = null;
        Cache::forget(self::CACHE_KEY);
    }

    // ── Accès typés (lisibilité des appelants) ──────────────────────────────

    public function plateformeNom(): string
    {
        $nom = $this->get('plateforme_nom');

        return (is_string($nom) && trim($nom) !== '')
            ? $nom
            : (string) config('app.name', 'Bimmo');
    }

    public function supportEmail(): string
    {
        return (string) $this->get('support_email', '');
    }

    public function supportTelephone(): string
    {
        return (string) $this->get('support_telephone', '');
    }

    public function maintenanceActive(): bool
    {
        return (bool) $this->get('maintenance_active', false);
    }

    public function maintenanceMessage(): string
    {
        $msg = trim((string) $this->get('maintenance_message', ''));

        return $msg !== '' ? $msg : self::DEFAULTS['maintenance_message'];
    }

    public function deuxFacteursObligatoire(): bool
    {
        return (bool) $this->get('securite_2fa_obligatoire', true);
    }

    public function sessionExpiration(): bool
    {
        return (bool) $this->get('securite_session_expiration', true);
    }

    public function sessionMinutes(): int
    {
        return max(1, (int) $this->get('securite_session_minutes', 30));
    }

    public function motDePasseRenforce(): bool
    {
        return (bool) $this->get('securite_mdp_renforce', true);
    }
}
