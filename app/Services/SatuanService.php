<?php

namespace App\Services;

use App\Repositories\SatuanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Satuan;

class SatuanService
{
    protected $satuanRepository;

    public function __construct(SatuanRepositoryInterface $satuanRepository)
    {
        $this->satuanRepository = $satuanRepository;
    }

    public function getAllSatuan(): Collection
    {
        return $this->satuanRepository->all();
    }

    public function getSatuanById(int $id): ?Satuan
    {
        return $this->satuanRepository->find($id);
    }

    public function createSatuan(array $data): Satuan
    {
        return $this->satuanRepository->create($data);
    }

    public function updateSatuan(int $id, array $data): bool
    {
        return $this->satuanRepository->update($id, $data);
    }

    public function deleteSatuan(int $id): bool
    {
        return $this->satuanRepository->delete($id);
    }
}
