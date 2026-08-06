<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface GapoktanRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get all gapoktans with their kecamatan relationship.
     */
    public function allWithKecamatan(): Collection;
}
