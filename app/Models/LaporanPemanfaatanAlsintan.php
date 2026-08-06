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
    'hour_meter'
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
    ];

    /**
     * Get the Alsintan related to this report.
     */
    public function alsintan(): BelongsTo
    {
        return $this->belongsTo(Alsintan::class);
    }
}
