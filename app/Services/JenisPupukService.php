<?php

namespace App\Services;

use App\Repositories\JenisPupukRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\JenisPupuk;

class JenisPupukService
{
    protected $jenisRepository;

    public function __construct(JenisPupukRepositoryInterface $jenisRepository)
    {
        $this->jenisRepository = $jenisRepository;
    }

    public function getAllJenis(): Collection
    {
        return $this->jenisRepository->all();
    }

    public function getJenisById(int $id): ?JenisPupuk
    {
        return $this->jenisRepository->find($id);
    }

    public function createJenis(array $data): JenisPupuk
    {
        return $this->jenisRepository->create($data);
    }

    public function updateJenis(int $id, array $data): bool
    {
        return $this->jenisRepository->update($id, $data);
    }

    public function deleteJenis(int $id): bool
    {
        return $this->jenisRepository->delete($id);
    }
}
