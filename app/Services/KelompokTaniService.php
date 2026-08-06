<?php

namespace App\Services;

use App\Repositories\KelompokTaniRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\KelompokTani;

class KelompokTaniService
{
    protected $kelompokTaniRepository;

    public function __construct(KelompokTaniRepositoryInterface $kelompokTaniRepository)
    {
        $this->kelompokTaniRepository = $kelompokTaniRepository;
    }

    public function getAllKelompokTani(): Collection
    {
        return $this->kelompokTaniRepository->allWithRelations();
    }

    public function getKelompokTaniById(int $id): ?KelompokTani
    {
        return $this->kelompokTaniRepository->find($id);
    }

    public function createKelompokTani(array $data): KelompokTani
    {
        return $this->kelompokTaniRepository->create($data);
    }

    public function updateKelompokTani(int $id, array $data): bool
    {
        return $this->kelompokTaniRepository->update($id, $data);
    }

    public function deleteKelompokTani(int $id): bool
    {
        return $this->kelompokTaniRepository->delete($id);
    }
}
