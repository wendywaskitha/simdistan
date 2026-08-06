<?php

namespace App\Repositories;

use App\Models\LaporanPemanfaatanAlsintan;

class LaporanPemanfaatanAlsintanRepository extends BaseRepository implements LaporanPemanfaatanAlsintanRepositoryInterface
{
    public function __construct(LaporanPemanfaatanAlsintan $model)
    {
        parent::__construct($model);
    }
}
