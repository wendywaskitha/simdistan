<?php

namespace App\Repositories;

use App\Models\Kecamatan;

class KecamatanRepository extends BaseRepository implements KecamatanRepositoryInterface
{
    public function __construct(Kecamatan $model)
    {
        parent::__construct($model);
    }
}
