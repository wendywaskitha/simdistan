<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface EloquentRepositoryInterface
{
    /**
     * Get all models.
     */
    public function all(): Collection;

    /**
     * Get all models with soft deleted ones.
     */
    public function allWithTrashed(): Collection;

    /**
     * Find model by id.
     */
    public function find(int $id): ?Model;

    /**
     * Find model by id with soft deleted ones.
     */
    public function findWithTrashed(int $id): ?Model;

    /**
     * Create a model.
     */
    public function create(array $attributes): Model;

    /**
     * Update a model.
     */
    public function update(int $id, array $attributes): bool;

    /**
     * Delete a model by id.
     */
    public function delete(int $id): bool;

    /**
     * Restore a soft deleted model by id.
     */
    public function restore(int $id): bool;
}
