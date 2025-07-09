<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Base Service Class
 *
 * Provides common functionality for all service classes following SOLID principles:
 * - Single Responsibility: Handles common service operations
 * - Open/Closed: Extensible for specific service implementations
 * - Dependency Inversion: Abstracts common service dependencies
 */
abstract class BaseService
{
    use LogsActivity;

    protected ?User $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
    }

    /**
     * Execute operation within database transaction
     *
     * @param callable $operation
     * @return mixed
     * @throws \Exception
     */
    protected function executeInTransaction(callable $operation)
    {
        return DB::transaction(function () use ($operation) {
            try {
                $result = $operation();

                // Log successful operation
                $this->logOperation('transaction_success', [
                    'service' => static::class,
                    'user_id' => $this->currentUser?->id
                ]);

                return $result;
            } catch (\Exception $e) {
                // Log failed operation
                $this->logOperation('transaction_failed', [
                    'service' => static::class,
                    'error' => $e->getMessage(),
                    'user_id' => $this->currentUser?->id
                ]);

                throw $e;
            }
        });
    }

    /**
     * Log service operation
     *
     * @param string $action
     * @param array $details
     * @return void
     */
    protected function logOperation(string $action, array $details = []): void
    {
        try {
            static::logActivity($action, $this->currentUser, array_merge([
                'service' => static::class,
                'timestamp' => now()->toISOString()
            ], $details));
        } catch (\Exception $e) {
            Log::error("Failed to log service operation: {$e->getMessage()}");
        }
    }

    /**
     * Validate required parameters
     *
     * @param array $data
     * @param array $required
     * @throws \InvalidArgumentException
     */
    protected function validateRequired(array $data, array $required): void
    {
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Required field '{$field}' is missing or empty");
            }
        }
    }

    /**
     * Get current authenticated user
     *
     * @return User|null
     */
    protected function getCurrentUser(): ?User
    {
        return $this->currentUser;
    }

    /**
     * Check if user has required role
     *
     * @param string $role
     * @return bool
     */
    protected function userHasRole(string $role): bool
    {
        return $this->currentUser && $this->currentUser->role === $role;
    }

    /**
     * Ensure user has admin role
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function ensureAdminRole(): void
    {
        if (!$this->userHasRole('admin')) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Admin role required');
        }
    }

    /**
     * Sanitize input data
     *
     * @param array $data
     * @return array
     */
    protected function sanitizeInput(array $data): array
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return trim(strip_tags($value));
            }
            return $value;
        }, $data);
    }
}
