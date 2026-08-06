<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'tahun',
    'kecamatan_id',
    'jenis_pupuk_id',
    'jumlah'
])]
class KuotaTahunanPupuk extends Model
{
    use HasFactory;

    protected $table = 'kuota_tahunan_pupuks';

    /**
     * Get the subdistrict.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Get the fertilizer type.
     */
    public function jenis(): BelongsTo
    {
        return $this->belongsTo(JenisPupuk::class, 'jenis_pupuk_id');
    }
}
