<?php

namespace App\Repositories;

use App\Models\RealokasiAlsintan;

class RealokasiAlsintanRepository extends BaseRepository implements RealokasiAlsintanRepositoryInterface
{
    public function __construct(RealokasiAlsintan $model)
    {
        parent::__construct($model);
    }
}
