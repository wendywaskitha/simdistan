<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['kelompok_tani_id', 'komoditas_id', 'jumlah_bantuan', 'satuan', 'sumber_dana', 'tahun_bantuan', 'keterangan'])]
class BantuanBibitHorti extends Model
{
    use HasFactory;

    protected $table = 'bantuan_bibit_hortis';

    public function kelompokTani(): BelongsTo
    {
        return $this->belongsTo(KelompokTani::class);
    }

    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class);
    }

    public function details()
    {
        return $this->hasMany(BantuanBibitHortiDetail::class, 'bantuan_bibit_horti_id');
    }
}
