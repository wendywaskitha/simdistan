<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface DesaRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get all desas with their kecamatan relationship.
     */
    public function allWithKecamatan(): Collection;
}
