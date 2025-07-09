<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository Interface
 *
 * Follows SOLID principles:
 * - Interface Segregation: Clean, focused interface for data operations
 * - Dependency Inversion: High-level modules depend on abstractions
 * - Liskov Substitution: Any implementation can be substituted
 */
interface RepositoryInterface
{
    /**
     * Find entity by ID
     */
    public function findById(int $id): ?Model;

    /**
     * Find entity by specific criteria
     */
    public function findBy(array $criteria): ?Model;

    /**
     * Get all entities
     */
    public function all(): Collection;

    /**
     * Get entities with pagination
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Create new entity
     */
    public function create(array $data): Model;

    /**
     * Update existing entity
     */
    public function update(int $id, array $data): Model;

    /**
     * Delete entity
     */
    public function delete(int $id): bool;

    /**
     * Get entities by specific criteria
     */
    public function getBy(array $criteria): Collection;

    /**
     * Count entities by criteria
     */
    public function countBy(array $criteria): int;

    /**
     * Check if entity exists
     */
    public function exists(array $criteria): bool;

    /**
     * Get first entity or fail
     */
    public function firstOrFail(array $criteria): Model;

    /**
     * Bulk insert entities
     */
    public function bulkInsert(array $data): bool;

    /**
     * Get entities with specific relationships
     */
    public function with(array $relations): self;

    /**
     * Order entities by specific column
     */
    public function orderBy(string $column, string $direction = 'asc'): self;

    /**
     * Limit the number of entities
     */
    public function limit(int $limit): self;
}
