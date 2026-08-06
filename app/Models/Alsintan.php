<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'kelompok_tani_id',
    'nama_ketua',
    'nama_operator',
    'no_hp_operator',
    'jenis_alat_id',
    'nama_alat',
    'merek',
    'kondisi',
    'nomor_rangka',
    'nomor_mesin',
    'sumber_dana',
    'harga',
    'tahun_bantuan'
])]
class Alsintan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the kelompok tani that owns the Alsintan.
     */
    public function kelompokTani(): BelongsTo
    {
        return $this->belongsTo(KelompokTani::class);
    }

    /**
     * Get the type of machinery (Jenis Alat) for this Alsintan.
     */
    public function jenisAlat(): BelongsTo
    {
        return $this->belongsTo(JenisAlat::class, 'jenis_alat_id');
    }

    /**
     * Get the utilization reports for this Alsintan.
     */
    public function laporanPemanfaatan(): HasMany
    {
        return $this->hasMany(LaporanPemanfaatanAlsintan::class)->orderBy('tanggal', 'desc');
    }

    /**
     * Get the reallocations for this Alsintan.
     */
    public function realokasi(): HasMany
    {
        return $this->hasMany(RealokasiAlsintan::class)->orderBy('tanggal_realokasi', 'desc');
    }
}
