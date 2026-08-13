<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'alsintan_id',
    'tanggal',
    'luas_lahan',
    'waktu_pengerjaan',
    'biaya_pengolahan',
    'hour_meter',
    'hour_meter_awal',
    'hour_meter_akhir',
    'foto_hm_awal',
    'foto_hm_akhir',
    'foto_dokumentasi',
    'tanggal_mulai',
    'tanggal_selesai'
])]
class LaporanPemanfaatanAlsintan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laporan_pemanfaatan_alsintans';

    protected $casts = [
        'tanggal' => 'date',
        'luas_lahan' => 'decimal:2',
        'biaya_pengolahan' => 'decimal:2',
        'hour_meter' => 'decimal:2',
        'hour_meter_awal' => 'decimal:2',
        'hour_meter_akhir' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Get the Alsintan related to this report.
     */
    public function alsintan(): BelongsTo
    {
        return $this->belongsTo(Alsintan::class);
    }
}
