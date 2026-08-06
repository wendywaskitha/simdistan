<?php

namespace App\Services;

use App\Repositories\GapoktanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Gapoktan;

class GapoktanService
{
    protected $gapoktanRepository;

    public function __construct(GapoktanRepositoryInterface $gapoktanRepository)
    {
        $this->gapoktanRepository = $gapoktanRepository;
    }

    public function getAllGapoktan(): Collection
    {
        return $this->gapoktanRepository->allWithKecamatan();
    }

    public function getGapoktanById(int $id): ?Gapoktan
    {
        return $this->gapoktanRepository->find($id);
    }

    public function createGapoktan(array $data): Gapoktan
    {
        return $this->gapoktanRepository->create($data);
    }

    public function updateGapoktan(int $id, array $data): bool
    {
        return $this->gapoktanRepository->update($id, $data);
    }

    public function deleteGapoktan(int $id): bool
    {
        return $this->gapoktanRepository->delete($id);
    }
}
