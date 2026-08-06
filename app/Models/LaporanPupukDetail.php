<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'laporan_pupuk_id',
    'kecamatan_id',
    'jenis_pupuk_id',
    'penebusan'
])]
class LaporanPupukDetail extends Model
{
    use HasFactory;

    protected $table = 'laporan_pupuk_details';

    /**
     * Get the parent report.
     */
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(LaporanPupuk::class, 'laporan_pupuk_id');
    }

    /**
     * Get the kecamatan associated with this row.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Get the fertilizer type associated with this row.
     */
    public function jenis(): BelongsTo
    {
        return $this->belongsTo(JenisPupuk::class, 'jenis_pupuk_id');
    }
}
