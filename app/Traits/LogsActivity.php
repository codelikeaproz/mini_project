<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * LogsActivity Trait
 *
 * Provides activity logging functionality for models and services
 */
trait LogsActivity
{
    /**
     * Log an activity
     *
     * @param string $action
     * @param User|null $user
     * @param array $details
     * @return void
     */
    public static function logActivity(string $action, ?User $user = null, array $details = []): void
    {
        try {
            ActivityLog::create([
                'user_id' => $user?->id,
                'action' => $action,
                'model_type' => static::class,
                'model_id' => null,
                'description' => $action . ' - ' . json_encode($details),
                'new_values' => $details,
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to log activity: {$e->getMessage()}", [
                'action' => $action,
                'user_id' => $user?->id,
                'details' => $details
            ]);
        }
    }

    /**
     * Log model activity with model context
     *
     * @param string $action
     * @param mixed $model
     * @param User|null $user
     * @param array $details
     * @return void
     */
    public static function logModelActivity(string $action, $model, ?User $user = null, array $details = []): void
    {
        try {
            ActivityLog::create([
                'user_id' => $user?->id,
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->id ?? null,
                'description' => $action . ' - ' . get_class($model) . ' ID: ' . ($model->id ?? 'new'),
                'new_values' => array_merge([
                    'model_data' => $model->toArray()
                ], $details),
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to log model activity: {$e->getMessage()}", [
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->id ?? null,
                'user_id' => $user?->id,
                'details' => $details
            ]);
        }
    }
}
