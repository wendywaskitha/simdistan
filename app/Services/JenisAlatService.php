<?php

namespace App\Services;

use App\Repositories\JenisAlatRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\JenisAlat;

class JenisAlatService
{
    protected $jenisAlatRepository;

    public function __construct(JenisAlatRepositoryInterface $jenisAlatRepository)
    {
        $this->jenisAlatRepository = $jenisAlatRepository;
    }

    public function getAllJenisAlat(): Collection
    {
        return $this->jenisAlatRepository->all();
    }

    public function getJenisAlatById(int $id): ?JenisAlat
    {
        return $this->jenisAlatRepository->find($id);
    }

    public function createJenisAlat(array $data): JenisAlat
    {
        return $this->jenisAlatRepository->create($data);
    }

    public function updateJenisAlat(int $id, array $data): bool
    {
        return $this->jenisAlatRepository->update($id, $data);
    }

    public function deleteJenisAlat(int $id): bool
    {
        return $this->jenisAlatRepository->delete($id);
    }
}
