<?php

namespace App\Services;

use App\Repositories\VarietasRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Varietas;

class VarietasService
{
    protected $varietasRepository;

    public function __construct(VarietasRepositoryInterface $varietasRepository)
    {
        $this->varietasRepository = $varietasRepository;
    }

    public function getAllVarietas(): Collection
    {
        return $this->varietasRepository->allWithKomoditas();
    }

    public function getVarietasById(int $id): ?Varietas
    {
        return $this->varietasRepository->find($id);
    }

    public function createVarietas(array $data): Varietas
    {
        return $this->varietasRepository->create($data);
    }

    public function updateVarietas(int $id, array $data): bool
    {
        return $this->varietasRepository->update($id, $data);
    }

    public function deleteVarietas(int $id): bool
    {
        return $this->varietasRepository->delete($id);
    }
}
