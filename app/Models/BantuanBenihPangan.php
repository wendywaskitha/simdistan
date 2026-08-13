<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['kelompok_tani_id', 'komoditas_id', 'varietas_id', 'jumlah_bantuan', 'satuan', 'sumber_dana', 'tahun_bantuan', 'keterangan'])]
class BantuanBenihPangan extends Model
{
    use HasFactory;

    public function kelompokTani(): BelongsTo
    {
        return $this->belongsTo(KelompokTani::class);
    }

    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class);
    }

    public function varietas(): BelongsTo
    {
        return $this->belongsTo(Varietas::class);
    }

    public function details()
    {
        return $this->hasMany(BantuanBenihPanganDetail::class, 'bantuan_benih_pangan_id');
    }
}
