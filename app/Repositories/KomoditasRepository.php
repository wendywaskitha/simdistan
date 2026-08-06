<?php

namespace App\Repositories;

use App\Models\Komoditas;
use Illuminate\Database\Eloquent\Collection;

class KomoditasRepository extends BaseRepository implements KomoditasRepositoryInterface
{
    public function __construct(Komoditas $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all komoditas with their category relationship.
     */
    public function allWithKategori(): Collection
    {
        return $this->model->with('kategori')->get();
    }
}
