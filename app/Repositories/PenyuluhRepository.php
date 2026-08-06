<?php

namespace App\Repositories;

use App\Models\Penyuluh;
use Illuminate\Database\Eloquent\Collection;

class PenyuluhRepository extends BaseRepository implements PenyuluhRepositoryInterface
{
    public function __construct(Penyuluh $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all penyuluhs with their BPP relationship.
     */
    public function allWithBpp(): Collection
    {
        return $this->model->with('bpp.kecamatan')->get();
    }
}
