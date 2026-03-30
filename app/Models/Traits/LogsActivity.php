<?php

namespace App\Models\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
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

            $agencyId = $model->agency_id
                ?? $user?->agency_id
                ?? null;

            $description = match ($action) {
                'created' => sprintf('%s #%s créé', class_basename($model), $model->getKey()),
                'updated' => self::buildUpdatedDescription($model),
                'deleted' => sprintf('%s #%s supprimé', class_basename($model), $model->getKey()),
                default => sprintf('%s #%s %s', class_basename($model), $model->getKey(), $action),
            };

            ActivityLog::create([
                'user_id'     => $user?->id,
                'agency_id'   => $agencyId,
                'action'      => $action,
                'description' => $description,
                'model_type'  => get_class($model),
                'model_id'    => (int) $model->getKey(),
                'ip_address'  => request()?->ip(),
            ]);
        } catch (\Throwable $e) {
            // Ne jamais bloquer le flux métier si le log échoue
        }
    }

    protected static function buildUpdatedDescription(Model $model): string
    {
        $changes = array_keys($model->getChanges());
        $changes = array_values(array_diff($changes, ['updated_at']));

        if (empty($changes)) {
            return sprintf('%s #%s modifié', class_basename($model), $model->getKey());
        }

        return sprintf(
            '%s #%s modifié (champs: %s)',
            class_basename($model),
            $model->getKey(),
            implode(', ', $changes)
        );
    }
}
