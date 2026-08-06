<?php

namespace App\Repositories;

use App\Models\JenisPupuk;

class JenisPupukRepository extends BaseRepository implements JenisPupukRepositoryInterface
{
    public function __construct(JenisPupuk $model)
    {
        parent::__construct($model);
    }
}
