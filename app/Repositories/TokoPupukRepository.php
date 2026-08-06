<?php

namespace App\Repositories;

use App\Models\TokoPupuk;

class TokoPupukRepository extends BaseRepository implements TokoPupukRepositoryInterface
{
    public function __construct(TokoPupuk $model)
    {
        parent::__construct($model);
    }
}
