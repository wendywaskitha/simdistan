<?php

namespace App\Repositories;

use App\Models\JenisAlat;

class JenisAlatRepository extends BaseRepository implements JenisAlatRepositoryInterface
{
    public function __construct(JenisAlat $model)
    {
        parent::__construct($model);
    }
}
