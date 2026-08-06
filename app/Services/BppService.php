<?php

namespace App\Services;

use App\Repositories\BppRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Bpp;

class BppService
{
    protected $bppRepository;

    public function __construct(BppRepositoryInterface $bppRepository)
    {
        $this->bppRepository = $bppRepository;
    }

    public function getAllBpp(): Collection
    {
        return $this->bppRepository->allWithKecamatan();
    }

    public function getBppById(int $id): ?Bpp
    {
        return $this->bppRepository->find($id);
    }

    public function createBpp(array $data): Bpp
    {
        return $this->bppRepository->create($data);
    }

    public function updateBpp(int $id, array $data): bool
    {
        return $this->bppRepository->update($id, $data);
    }

    public function deleteBpp(int $id): bool
    {
        return $this->bppRepository->delete($id);
    }
}
