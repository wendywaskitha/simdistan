<?php

namespace App\Repositories;

use App\Models\Satuan;

class SatuanRepository extends BaseRepository implements SatuanRepositoryInterface
{
    public function __construct(Satuan $model)
    {
        parent::__construct($model);
    }
}
