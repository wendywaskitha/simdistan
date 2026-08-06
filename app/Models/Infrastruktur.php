<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'nama_proyek',
    'jenis_infrastruktur',
    'kelompok_tani_id',
    'kecamatan_id',
    'desa_id',
    'volume',
    'satuan',
    'nilai_anggaran',
    'sumber_dana',
    'tahun_anggaran',
    'status_pembangunan',
    'latitude',
    'longitude',
    'kml_file',
    'geojson',
    'keterangan'
])]
class Infrastruktur extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the kelompok tani that owns the Infrastruktur.
     */
    public function kelompokTani(): BelongsTo
    {
        return $this->belongsTo(KelompokTani::class);
    }

    /**
     * Get the kecamatan for this Infrastruktur.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Get the desa for this Infrastruktur.
     */
    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    /**
     * Get the condition reports for this Infrastruktur.
     */
    public function laporans(): HasMany
    {
        return $this->hasMany(InfrastrukturLaporan::class)->orderBy('tanggal_laporan', 'desc');
    }
}
