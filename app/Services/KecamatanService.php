<?php

namespace App\Services;

use App\Repositories\KecamatanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Kecamatan;

class KecamatanService
{
    protected $kecamatanRepository;

    public function __construct(KecamatanRepositoryInterface $kecamatanRepository)
    {
        $this->kecamatanRepository = $kecamatanRepository;
    }

    public function getAllKecamatan(): Collection
    {
        return $this->kecamatanRepository->all();
    }

    public function getKecamatanById(int $id): ?Kecamatan
    {
        return $this->kecamatanRepository->find($id);
    }

    public function createKecamatan(array $data): Kecamatan
    {
        return $this->kecamatanRepository->create($data);
    }

    public function updateKecamatan(int $id, array $data): bool
    {
        return $this->kecamatanRepository->update($id, $data);
    }

    public function deleteKecamatan(int $id): bool
    {
        return $this->kecamatanRepository->delete($id);
    }
}
