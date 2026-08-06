<?php

namespace App\Repositories;

use App\Models\InfrastrukturLaporan;

class InfrastrukturLaporanRepository extends BaseRepository implements InfrastrukturLaporanRepositoryInterface
{
    public function __construct(InfrastrukturLaporan $model)
    {
        parent::__construct($model);
    }
}
