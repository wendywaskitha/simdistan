<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'toko_pupuk_id',
    'satuan_id',
    'bulan',
    'tahun'
])]
class LaporanPupuk extends Model
{
    use HasFactory;

    protected $table = 'laporan_pupuks';

    /**
     * Get the store that owns the report.
     */
    public function toko(): BelongsTo
    {
        return $this->belongsTo(TokoPupuk::class, 'toko_pupuk_id');
    }

    /**
     * Get the unit that owns the report.
     */
    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    /**
     * Get the details of the report.
     */
    public function details(): HasMany
    {
        return $this->hasMany(LaporanPupukDetail::class, 'laporan_pupuk_id');
    }
}
