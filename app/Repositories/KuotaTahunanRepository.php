<?php

namespace App\Repositories;

use App\Models\KuotaTahunanPupuk;

class KuotaTahunanRepository extends BaseRepository implements KuotaTahunanRepositoryInterface
{
    public function __construct(KuotaTahunanPupuk $model)
    {
        parent::__construct($model);
    }
}
