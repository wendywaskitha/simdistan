<?php

namespace App\Repositories;

use App\Models\Gapoktan;
use Illuminate\Database\Eloquent\Collection;

class GapoktanRepository extends BaseRepository implements GapoktanRepositoryInterface
{
    public function __construct(Gapoktan $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all gapoktans with their kecamatan relationship.
     */
    public function allWithKecamatan(): Collection
    {
        return $this->model->with('kecamatan')->get();
    }
}
