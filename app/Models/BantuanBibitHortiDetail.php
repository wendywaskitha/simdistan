<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['bantuan_bibit_horti_id', 'petani_id', 'jumlah_bantuan'])]
class BantuanBibitHortiDetail extends Model
{
    use HasFactory;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BantuanBibitHorti::class, 'bantuan_bibit_horti_id');
    }

    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class);
    }
}
