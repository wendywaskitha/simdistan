<?php

namespace App\Repositories;

use App\Models\LaporanProduksi;
use Illuminate\Database\Eloquent\Collection;

class LaporanProduksiRepository extends BaseRepository implements LaporanProduksiRepositoryInterface
{
    public function __construct(LaporanProduksi $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all reports with relationships.
     */
    public function allWithRelations(): Collection
    {
        return $this->model->with(['kategori', 'kecamatan', 'komoditas', 'satuan', 'mingguans'])->get();
    }

    /**
     * Get all reports by specific category.
     */
    public function getByCategory(int $kategoriId): Collection
    {
        return $this->model->with(['kategori', 'kecamatan', 'komoditas', 'satuan', 'mingguans'])
            ->where('kategori_komoditas_id', $kategoriId)
            ->get();
    }
}
