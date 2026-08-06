<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'bulan',
    'tahun',
    'jenis_pupuk_id',
    'kecamatan_asal_id',
    'kecamatan_tujuan_id',
    'jumlah',
    'nama_sk',
    'file_path',
    'keterangan'
])]
class PengalihanPupuk extends Model
{
    use HasFactory;

    protected $table = 'pengalihan_pupuks';

    /**
     * Get the fertilizer type.
     */
    public function jenis(): BelongsTo
    {
        return $this->belongsTo(JenisPupuk::class, 'jenis_pupuk_id');
    }

    /**
     * Get the source kecamatan.
     */
    public function kecamatanAsal(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_asal_id');
    }

    /**
     * Get the target kecamatan.
     */
    public function kecamatanTujuan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_tujuan_id');
    }
}
