<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements EloquentRepositoryInterface
{
    /**      
     * @var Model      
     */     
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all models.
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get all models with soft deleted ones.
     */
    public function allWithTrashed(): Collection
    {
        return $this->model->withTrashed()->get();
    }

    /**
     * Find model by id.
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find model by id with soft deleted ones.
     */
    public function findWithTrashed(int $id): ?Model
    {
        return $this->model->withTrashed()->find($id);
    }

    /**
     * Create a model.
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a model.
     */
    public function update(int $id, array $attributes): bool
    {
        $record = $this->find($id);
        if ($record) {
            return $record->update($attributes);
        }
        return false;
    }

    /**
     * Delete a model by id.
     */
    public function delete(int $id): bool
    {
        $record = $this->find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }

    /**
     * Restore a soft deleted model by id.
     */
    public function restore(int $id): bool
    {
        $record = $this->findWithTrashed($id);
        if ($record && $record->trashed()) {
            return $record->restore();
        }
        return false;
    }
}
