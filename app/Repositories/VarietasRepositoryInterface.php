<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface VarietasRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get all varietas with their komoditas relationship.
     */
    public function allWithKomoditas(): Collection;
}
