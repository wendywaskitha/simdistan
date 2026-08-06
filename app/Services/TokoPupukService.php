<?php

namespace App\Services;

use App\Repositories\TokoPupukRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\TokoPupuk;

class TokoPupukService
{
    protected $tokoRepository;

    public function __construct(TokoPupukRepositoryInterface $tokoRepository)
    {
        $this->tokoRepository = $tokoRepository;
    }

    public function getAllToko(): Collection
    {
        return $this->tokoRepository->all();
    }

    public function getTokoById(int $id): ?TokoPupuk
    {
        return $this->tokoRepository->find($id);
    }

    public function createToko(array $data): TokoPupuk
    {
        $toko = $this->tokoRepository->create($data);
        if (isset($data['kecamatan_ids'])) {
            $toko->kecamatans()->sync($data['kecamatan_ids']);
        }
        return $toko;
    }

    public function updateToko(int $id, array $data): bool
    {
        $updated = $this->tokoRepository->update($id, $data);
        if ($updated) {
            $toko = $this->getTokoById($id);
            if (isset($data['kecamatan_ids'])) {
                $toko->kecamatans()->sync($data['kecamatan_ids']);
            } else {
                $toko->kecamatans()->detach();
            }
        }
        return $updated;
    }

    public function deleteToko(int $id): bool
    {
        return $this->tokoRepository->delete($id);
    }
}
