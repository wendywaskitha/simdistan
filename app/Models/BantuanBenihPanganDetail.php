<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['bantuan_benih_pangan_id', 'petani_id', 'jumlah_bantuan'])]
class BantuanBenihPanganDetail extends Model
{
    use HasFactory;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BantuanBenihPangan::class, 'bantuan_benih_pangan_id');
    }

    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class);
    }
}
