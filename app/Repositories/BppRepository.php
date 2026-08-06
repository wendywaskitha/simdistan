<?php

namespace App\Repositories;

use App\Models\Bpp;
use Illuminate\Database\Eloquent\Collection;

class BppRepository extends BaseRepository implements BppRepositoryInterface
{
    public function __construct(Bpp $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all BPPs with their kecamatan relationship.
     */
    public function allWithKecamatan(): Collection
    {
        return $this->model->with('kecamatan')->get();
    }
}
