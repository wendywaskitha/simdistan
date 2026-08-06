<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'kategori_komoditas_id',
    'kecamatan_id',
    'komoditas_id',
    'satuan_id',
    'bulan',
    'tahun',
    'luas_tanam',
    'luas_panen',
    'luas_rusak',
    'jumlah_tanaman_menghasilkan',
    'jenis_periode',
    'form_type',
    'produktivitas',
    'produksi',
    'luas_lahan',
    // SPH-SBS & SPH-TBF fields
    'luas_tanam_akhir_bulan_lalu',
    'luas_panen_belum_habis',
    'luas_tanam_akhir',
    'produksi_belum_habis',
    'harga_jual',
    // SPH-BST fields (pohon/rumpun)
    'jumlah_tanaman_akhir_triwulan_lalu',
    'tanaman_dibongkar',
    'tanaman_baru',
    'tanaman_tidak_menghasilkan',
    'tanaman_tus_rusak',
    // Perkebunan fields
    'luas_akhir_tahun_lalu',
    'tanam_ulang',
    'tanam_baru',
    'pengurangan',
    'luas_jumlah',
    'tbm',
    'tm',
    'ttm',
    'produksi_akhir_tahun_lalu',
    'wujud_produksi',
    'jumlah_petani_pemilik',
    'jumlah_petani_bmu',
])]
class LaporanProduksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'laporan_produksis';

    /**
     * Get the kategori that owns the report.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriKomoditas::class, 'kategori_komoditas_id');
    }

    /**
     * Get the kecamatan that owns the report.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Get the komoditas that owns the report.
     */
    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class);
    }

    /**
     * Get the satuan that owns the report.
     */
    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    /**
     * Get the weekly details of the report.
     */
    public function mingguans(): HasMany
    {
        return $this->hasMany(LaporanProduksiMingguan::class, 'laporan_produksi_id');
    }
}
