<?php

namespace App\Repositories;

use App\Models\Alsintan;

class AlsintanRepository extends BaseRepository implements AlsintanRepositoryInterface
{
    public function __construct(Alsintan $model)
    {
        parent::__construct($model);
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->with(['kelompokTani.desa', 'jenisAlat'])->get();
    }

    public function find(int $id): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->model->with(['kelompokTani.desa', 'jenisAlat', 'laporanPemanfaatan', 'realokasi.kelompokTaniAsal', 'realokasi.kelompokTaniTujuan'])->find($id);
    }
}
