<?php

namespace App\Repositories;

use App\Models\Varietas;
use Illuminate\Database\Eloquent\Collection;

class VarietasRepository extends BaseRepository implements VarietasRepositoryInterface
{
    public function __construct(Varietas $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all varietas with their komoditas relationship.
     */
    public function allWithKomoditas(): Collection
    {
        return $this->model->with('komoditas.kategori')->get();
    }
}
