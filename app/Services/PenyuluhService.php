<?php

namespace App\Services;

use App\Repositories\PenyuluhRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Penyuluh;

class PenyuluhService
{
    protected $penyuluhRepository;

    public function __construct(PenyuluhRepositoryInterface $penyuluhRepository)
    {
        $this->penyuluhRepository = $penyuluhRepository;
    }

    public function getAllPenyuluh(): Collection
    {
        return $this->penyuluhRepository->allWithBpp();
    }

    public function getPenyuluhById(int $id): ?Penyuluh
    {
        return $this->penyuluhRepository->find($id);
    }

    public function createPenyuluh(array $data): Penyuluh
    {
        return $this->penyuluhRepository->create($data);
    }

    public function updatePenyuluh(int $id, array $data): bool
    {
        return $this->penyuluhRepository->update($id, $data);
    }

    public function deletePenyuluh(int $id): bool
    {
        return $this->penyuluhRepository->delete($id);
    }
}
