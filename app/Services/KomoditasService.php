<?php

namespace App\Services;

use App\Repositories\KomoditasRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Komoditas;

class KomoditasService
{
    protected $komoditasRepository;

    public function __construct(KomoditasRepositoryInterface $komoditasRepository)
    {
        $this->komoditasRepository = $komoditasRepository;
    }

    public function getAllKomoditas(): Collection
    {
        return $this->komoditasRepository->allWithKategori();
    }

    public function getKomoditasById(int $id): ?Komoditas
    {
        return $this->komoditasRepository->find($id);
    }

    public function createKomoditas(array $data): Komoditas
    {
        return $this->komoditasRepository->create($data);
    }

    public function updateKomoditas(int $id, array $data): bool
    {
        return $this->komoditasRepository->update($id, $data);
    }

    public function deleteKomoditas(int $id): bool
    {
        return $this->komoditasRepository->delete($id);
    }
}
