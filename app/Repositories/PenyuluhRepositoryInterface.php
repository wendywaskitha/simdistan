<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface PenyuluhRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get all penyuluhs with their BPP relationship.
     */
    public function allWithBpp(): Collection;
}
