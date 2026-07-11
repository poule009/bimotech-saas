<?php

namespace App\Models\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    // Ces champs n'apparaissent jamais dans les logs (ni nom, ni valeur).
    // Plomberie technique : sans intérêt métier pour le journal du directeur.
    protected static array $hiddenFields = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'must_change_password',
        'email_verified_at',
    ];

    public static function bootLogsActivity(): void
    {
        static::created(fn (Model $model) => self::writeActivityLog($model, 'created'));
        static::updated(fn (Model $model) => self::writeActivityLog($model, 'updated'));
        static::deleted(fn (Model $model) => self::writeActivityLog($model, 'deleted'));
    }

    protected static function writeActivityLog(Model $model, string $action): void
    {
        try {
            $user     = Auth::user();
            $agencyId = $model->agency_id ?? $user?->agency_id ?? null;

            $title = method_exists($model, 'getActivityLogTitle')
                ? $model->getActivityLogTitle()
                : class_basename($model) . ' #' . $model->getKey();

            $changedFields = $action === 'updated' ? self::changedFields($model) : [];

            // Ne pas polluer le journal avec une « modification » qui n'a touché que
            // des champs techniques/masqués (ex. remember_token à la connexion,
            // must_change_password au premier login) : rien de significatif à montrer.
            if ($action === 'updated' && empty($changedFields)) {
                return;
            }

            $properties    = $action === 'updated' ? self::buildProperties($model, $changedFields) : null;

            $description = match ($action) {
                'created' => $title . ' créé',
                'updated' => self::buildUpdatedDescription($title, $changedFields),
                'deleted' => $title . ' supprimé',
                default   => $title . ' ' . $action,
            };

            // Pendant une impersonation, auth()->user() est l'admin impersonné.
            // On note le superadmin réel pour l'audit.
            $superadminId = session('impersonating_id');
            if ($superadminId) {
                $description .= sprintf(' [via impersonation superadmin #%s]', $superadminId);
            }

            ActivityLog::create([
                'user_id'      => $superadminId ?? $user?->id,
                'agency_id'    => $agencyId,
                'action'       => $action,
                'is_sensitive' => self::computeSensitivity($model, $action, $changedFields),
                'description'  => $description,
                'properties'   => $properties,
                'model_type'   => get_class($model),
                'model_id'     => (int) $model->getKey(),
                'ip_address'   => request()?->ip(),
            ]);
        } catch (\Throwable) {
            // Ne jamais bloquer le flux métier si le log échoue.
        }
    }

    /** Champs réellement modifiés (hors updated_at et champs cachés). */
    protected static function changedFields(Model $model): array
    {
        return array_values(array_diff(
            array_keys($model->getChanges()),
            array_merge(['updated_at'], static::$hiddenFields)
        ));
    }

    /** Libellés lisibles des champs, définis via $activityFieldLabels sur le modèle. */
    protected static function fieldLabels(): array
    {
        return property_exists(static::class, 'activityFieldLabels')
            ? static::$activityFieldLabels
            : [];
    }

    /** Capture avant/après { champ: { label, old, new } } pour l'affichage du diff. */
    protected static function buildProperties(Model $model, array $changedFields): ?array
    {
        if (empty($changedFields)) {
            return null;
        }

        $labels = self::fieldLabels();
        $props  = [];

        foreach ($changedFields as $field) {
            $props[$field] = [
                'label' => $labels[$field] ?? $field,
                'old'   => self::scalarize($model->getOriginal($field)),
                'new'   => self::scalarize($model->getAttribute($field)),
            ];
        }

        return $props;
    }

    /** Normalise une valeur pour un stockage JSON lisible (enum/date/bool). */
    protected static function scalarize($value)
    {
        if ($value instanceof \BackedEnum)       return $value->value;
        if ($value instanceof \DateTimeInterface) return $value->format('Y-m-d');
        if (is_bool($value))                      return $value ? 1 : 0;

        return $value;
    }

    protected static function buildUpdatedDescription(string $title, array $changedFields): string
    {
        if (empty($changedFields)) {
            return $title . ' modifié';
        }

        $labels   = self::fieldLabels();
        $readable = array_map(fn ($f) => $labels[$f] ?? $f, $changedFields);

        return $title . ' modifié (' . implode(', ', $readable) . ')';
    }

    /**
     * Caractère sensible figé à l'écriture (valeur de preuve, immuable) :
     *   - toute suppression ;
     *   - règle propre au modèle via isSensitiveActivity() (ex. loyer d'un bail
     *     actif, quittance déjà payée modifiée).
     */
    protected static function computeSensitivity(Model $model, string $action, array $changedFields): bool
    {
        if ($action === 'deleted') {
            return true;
        }

        if (method_exists($model, 'isSensitiveActivity')) {
            return (bool) $model->isSensitiveActivity($action, $changedFields);
        }

        return false;
    }
}
