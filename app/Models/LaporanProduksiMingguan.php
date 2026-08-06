<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'laporan_produksi_id',
    'minggu_ke',
    'luas_tanam',
    'luas_panen',
    'produktivitas',
    'produksi',
    'luas_lahan'
])]
class LaporanProduksiMingguan extends Model
{
    use HasFactory;

    protected $table = 'laporan_produksi_mingguans';

    /**
     * Get the parent report that owns this weekly record.
     */
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(LaporanProduksi::class, 'laporan_produksi_id');
    }
}
