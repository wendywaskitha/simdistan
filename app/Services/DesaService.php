<?php

namespace App\Services;

use App\Repositories\DesaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Desa;

class DesaService
{
    protected $desaRepository;

    public function __construct(DesaRepositoryInterface $desaRepository)
    {
        $this->desaRepository = $desaRepository;
    }

    public function getAllDesa(): Collection
    {
        return $this->desaRepository->allWithKecamatan();
    }

    public function getDesaById(int $id): ?Desa
    {
        return $this->desaRepository->find($id);
    }

    public function createDesa(array $data): Desa
    {
        return $this->desaRepository->create($data);
    }

    public function updateDesa(int $id, array $data): bool
    {
        return $this->desaRepository->update($id, $data);
    }

    public function deleteDesa(int $id): bool
    {
        return $this->desaRepository->delete($id);
    }
}
