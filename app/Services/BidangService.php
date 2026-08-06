<?php

namespace App\Services;

use App\Repositories\BidangRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Bidang;

class BidangService
{
    protected $bidangRepository;

    public function __construct(BidangRepositoryInterface $bidangRepository)
    {
        $this->bidangRepository = $bidangRepository;
    }

    public function getAllBidang(): Collection
    {
        return $this->bidangRepository->all();
    }

    public function getBidangById(int $id): ?Bidang
    {
        return $this->bidangRepository->find($id);
    }

    public function createBidang(array $data): Bidang
    {
        return $this->bidangRepository->create($data);
    }

    public function updateBidang(int $id, array $data): bool
    {
        return $this->bidangRepository->update($id, $data);
    }

    public function deleteBidang(int $id): bool
    {
        return $this->bidangRepository->delete($id);
    }
}
