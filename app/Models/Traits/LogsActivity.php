<?php

namespace App\Models\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    // ✅ CORRECTION B3 : ces noms de champs n'apparaissent plus dans les logs
    protected static array $hiddenFields = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model): void {
            self::writeActivityLog($model, 'created');
        });

        static::updated(function (Model $model): void {
            self::writeActivityLog($model, 'updated');
        });

        static::deleted(function (Model $model): void {
            self::writeActivityLog($model, 'deleted');
        });
    }

    protected static function writeActivityLog(Model $model, string $action): void
    {
        try {
            $user = Auth::user();

            $agencyId = $model->agency_id ?? $user?->agency_id ?? null;

            $title = method_exists($model, 'getActivityLogTitle')
                ? $model->getActivityLogTitle()
                : class_basename($model) . ' #' . $model->getKey();

            $description = match ($action) {
                'created' => $title . ' créé',
                'updated' => self::buildUpdatedDescription($model, $title),
                'deleted' => $title . ' supprimé',
                default   => $title . ' ' . $action,
            };

            // Pendant une impersonation, auth()->user() retourne l'admin impersonné.
            // On note le superadmin réel dans la description pour l'audit.
            $superadminId = session('impersonating_id');
            if ($superadminId) {
                $description .= sprintf(' [via impersonation superadmin #%s]', $superadminId);
            }

            ActivityLog::create([
                'user_id'     => $superadminId ?? $user?->id,
                'agency_id'   => $agencyId,
                'action'      => $action,
                'description' => $description,
                'model_type'  => get_class($model),
                'model_id'    => (int) $model->getKey(),
                'ip_address'  => request()?->ip(),
            ]);
        } catch (\Throwable) {
            // Ne jamais bloquer le flux métier si le log échoue
        }
    }

    protected static function buildUpdatedDescription(Model $model, string $title): string
    {
        $changes = array_keys($model->getChanges());

        $changes = array_values(
            array_diff($changes, array_merge(['updated_at'], static::$hiddenFields))
        );

        if (empty($changes)) {
            return $title . ' modifié';
        }

        $labels = property_exists(static::class, 'activityFieldLabels')
            ? static::$activityFieldLabels
            : [];

        $readable = array_map(fn($f) => $labels[$f] ?? $f, $changes);

        return $title . ' modifié (' . implode(', ', $readable) . ')';
    }
}