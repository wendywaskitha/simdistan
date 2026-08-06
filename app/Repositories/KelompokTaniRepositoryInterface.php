<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface KelompokTaniRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get all kelompok tanis with relations.
     */
    public function allWithRelations(): Collection;
}
