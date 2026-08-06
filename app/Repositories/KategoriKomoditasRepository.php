<?php

namespace App\Repositories;

use App\Models\KategoriKomoditas;

class KategoriKomoditasRepository extends BaseRepository implements KategoriKomoditasRepositoryInterface
{
    public function __construct(KategoriKomoditas $model)
    {
        parent::__construct($model);
    }
}
