<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Base Repository Class
 *
 * Implements common repository functionality following SOLID principles:
 * - Single Responsibility: Handles data access operations
 * - Open/Closed: Extensible for specific repository implementations
 * - Liskov Substitution: Can be substituted with any specific repository
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;
    protected Builder $query;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->resetQuery();
    }

    /**
     * Reset query builder to model instance
     */
    protected function resetQuery(): void
    {
        $this->query = $this->model->newQuery();
    }

    /**
     * Find entity by ID
     */
    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find entity by specific criteria
     */
    public function findBy(array $criteria): ?Model
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }

        return $query->first();
    }

    /**
     * Get all entities
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get entities with pagination
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $result = $this->query->paginate($perPage);
        $this->resetQuery();
        return $result;
    }

    /**
     * Create new entity
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update existing entity
     */
    public function update(int $id, array $data): Model
    {
        $entity = $this->findById($id);

        if (!$entity) {
            throw new \ModelNotFoundException("Entity with ID {$id} not found");
        }

        $entity->update($data);
        return $entity->fresh();
    }

    /**
     * Delete entity
     */
    public function delete(int $id): bool
    {
        $entity = $this->findById($id);

        if (!$entity) {
            return false;
        }

        return $entity->delete();
    }

    /**
     * Get entities by specific criteria
     */
    public function getBy(array $criteria): Collection
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->get();
    }

    /**
     * Count entities by criteria
     */
    public function countBy(array $criteria): int
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }

        return $query->count();
    }

    /**
     * Check if entity exists
     */
    public function exists(array $criteria): bool
    {
        return $this->countBy($criteria) > 0;
    }

    /**
     * Get first entity or fail
     */
    public function firstOrFail(array $criteria): Model
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }

        return $query->firstOrFail();
    }

    /**
     * Bulk insert entities
     */
    public function bulkInsert(array $data): bool
    {
        return $this->model->insert($data);
    }

    /**
     * Get entities with specific relationships
     */
    public function with(array $relations): self
    {
        $this->query->with($relations);
        return $this;
    }

    /**
     * Order entities by specific column
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->query->orderBy($column, $direction);
        return $this;
    }

    /**
     * Limit the number of entities
     */
    public function limit(int $limit): self
    {
        $this->query->limit($limit);
        return $this;
    }

    /**
     * Apply where conditions
     */
    public function where(string $column, mixed $operator, mixed $value = null): self
    {
        $this->query->where($column, $operator, $value);
        return $this;
    }

    /**
     * Apply where in conditions
     */
    public function whereIn(string $column, array $values): self
    {
        $this->query->whereIn($column, $values);
        return $this;
    }

    /**
     * Apply date range filter
     */
    public function whereDateBetween(string $column, string $startDate, string $endDate): self
    {
        $this->query->whereBetween($column, [$startDate, $endDate]);
        return $this;
    }

    /**
     * Get the results and reset query
     */
    public function get(): Collection
    {
        $result = $this->query->get();
        $this->resetQuery();
        return $result;
    }

    /**
     * Get first result and reset query
     */
    public function first(): ?Model
    {
        $result = $this->query->first();
        $this->resetQuery();
        return $result;
    }
}
