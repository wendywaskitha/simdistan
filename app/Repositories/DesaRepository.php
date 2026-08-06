<?php

namespace App\Repositories;

use App\Models\Desa;
use Illuminate\Database\Eloquent\Collection;

class DesaRepository extends BaseRepository implements DesaRepositoryInterface
{
    public function __construct(Desa $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all desas with their kecamatan relationship.
     */
    public function allWithKecamatan(): Collection
    {
        return $this->model->with('kecamatan')->get();
    }
}
