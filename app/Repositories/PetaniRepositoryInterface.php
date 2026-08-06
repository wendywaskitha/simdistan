<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface PetaniRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get all petanis with relations.
     */
    public function allWithRelations(): Collection;
}
