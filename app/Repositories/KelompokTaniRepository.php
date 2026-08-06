<?php

namespace App\Repositories;

use App\Models\KelompokTani;
use Illuminate\Database\Eloquent\Collection;

class KelompokTaniRepository extends BaseRepository implements KelompokTaniRepositoryInterface
{
    public function __construct(KelompokTani $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all kelompok tanis with relations.
     */
    public function allWithRelations(): Collection
    {
        return $this->model->with(['desa.kecamatan', 'gapoktan'])->get();
    }
}
