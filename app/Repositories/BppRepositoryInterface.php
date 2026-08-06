<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface BppRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get all BPPs with their kecamatan relationship.
     */
    public function allWithKecamatan(): Collection;
}
