<?php

namespace App\Services;

use App\Repositories\PetaniRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Petani;

class PetaniService
{
    protected $petaniRepository;

    public function __construct(PetaniRepositoryInterface $petaniRepository)
    {
        $this->petaniRepository = $petaniRepository;
    }

    public function getAllPetani(): Collection
    {
        return $this->petaniRepository->allWithRelations();
    }

    public function getPetaniById(int $id): ?Petani
    {
        return $this->petaniRepository->find($id);
    }

    public function createPetani(array $data): Petani
    {
        return $this->petaniRepository->create($data);
    }

    public function updatePetani(int $id, array $data): bool
    {
        return $this->petaniRepository->update($id, $data);
    }

    public function deletePetani(int $id): bool
    {
        return $this->petaniRepository->delete($id);
    }
}
