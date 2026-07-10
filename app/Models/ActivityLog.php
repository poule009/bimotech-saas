<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory, HasAgencyScope;

    protected $fillable = [
        'user_id',
        'agency_id',
        'action',
        'is_sensitive',
        'description',
        'properties',
        'model_type',
        'model_id',
        'ip_address',
    ];

    protected $casts = [
        'properties'   => 'array',
        'is_sensitive' => 'boolean',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Enregistre une CONSULTATION de fiche (action 'viewed').
     * Préparé pour le futur module « Mon équipe » — non branché pour l'instant
     * (aucune fiche ne l'appelle encore ; le journal l'exclut de l'affichage).
     */
    public static function logView(Model $model): void
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return;
            }

            $title = method_exists($model, 'getActivityLogTitle')
                ? $model->getActivityLogTitle()
                : class_basename($model) . ' #' . $model->getKey();

            self::create([
                'user_id'      => $user->id,
                'agency_id'    => $model->agency_id ?? $user->agency_id,
                'action'       => 'viewed',
                'is_sensitive' => false,
                'description'  => $title . ' consulté',
                'model_type'   => get_class($model),
                'model_id'     => (int) $model->getKey(),
                'ip_address'   => request()?->ip(),
            ]);
        } catch (\Throwable) {
            // silencieux
        }
    }
}
