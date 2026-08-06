<?php

namespace App\Repositories;

use App\Models\Petani;
use Illuminate\Database\Eloquent\Collection;

class PetaniRepository extends BaseRepository implements PetaniRepositoryInterface
{
    public function __construct(Petani $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all petanis with relations.
     */
    public function allWithRelations(): Collection
    {
        return $this->model->with('kelompokTani.desa.kecamatan')->get();
    }
}
