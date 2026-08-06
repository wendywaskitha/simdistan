<?php

namespace App\Services;

use App\Repositories\KategoriKomoditasRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\KategoriKomoditas;

class KategoriKomoditasService
{
    protected $kategoriRepository;

    public function __construct(KategoriKomoditasRepositoryInterface $kategoriRepository)
    {
        $this->kategoriRepository = $kategoriRepository;
    }

    public function getAllKategori(): Collection
    {
        return $this->kategoriRepository->all();
    }

    public function getKategoriById(int $id): ?KategoriKomoditas
    {
        return $this->kategoriRepository->find($id);
    }

    public function createKategori(array $data): KategoriKomoditas
    {
        return $this->kategoriRepository->create($data);
    }

    public function updateKategori(int $id, array $data): bool
    {
        return $this->kategoriRepository->update($id, $data);
    }

    public function deleteKategori(int $id): bool
    {
        return $this->kategoriRepository->delete($id);
    }
}
