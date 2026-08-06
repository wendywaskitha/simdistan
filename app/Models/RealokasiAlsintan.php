<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'alsintan_id',
    'kelompok_tani_asal_id',
    'kelompok_tani_tujuan_id',
    'tanggal_realokasi',
    'keterangan'
])]
class RealokasiAlsintan extends Model
{
    use HasFactory;

    protected $table = 'realokasi_alsintans';

    protected $casts = [
        'tanggal_realokasi' => 'date',
    ];

    /**
     * Get the Alsintan related to this reallocation.
     */
    public function alsintan(): BelongsTo
    {
        return $this->belongsTo(Alsintan::class);
    }

    /**
     * Get the original kelompok tani before reallocation.
     */
    public function kelompokTaniAsal(): BelongsTo
    {
        return $this->belongsTo(KelompokTani::class, 'kelompok_tani_asal_id');
    }

    /**
     * Get the destination kelompok tani after reallocation.
     */
    public function kelompokTaniTujuan(): BelongsTo
    {
        return $this->belongsTo(KelompokTani::class, 'kelompok_tani_tujuan_id');
    }
}
