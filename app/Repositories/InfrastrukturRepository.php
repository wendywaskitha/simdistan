<?php

namespace App\Repositories;

use App\Models\Infrastruktur;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class InfrastrukturRepository extends BaseRepository implements InfrastrukturRepositoryInterface
{
    public function __construct(Infrastruktur $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return $this->model->with(['kecamatan', 'desa', 'kelompokTani'])->get();
    }

    public function find(int $id): ?Model
    {
        return $this->model->with(['kecamatan', 'desa', 'kelompokTani', 'laporans'])->find($id);
    }
}
