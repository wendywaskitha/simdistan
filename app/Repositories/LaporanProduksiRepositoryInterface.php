<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface LaporanProduksiRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get all reports with relationships.
     */
    public function allWithRelations(): Collection;

    /**
     * Get all reports by specific category.
     */
    public function getByCategory(int $kategoriId): Collection;
}
