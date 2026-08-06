<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface KomoditasRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get all komoditas with their category relationship.
     */
    public function allWithKategori(): Collection;
}
