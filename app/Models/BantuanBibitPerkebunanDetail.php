<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['bantuan_bibit_perkebunan_id', 'petani_id', 'jumlah_bantuan'])]
class BantuanBibitPerkebunanDetail extends Model
{
    use HasFactory;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BantuanBibitPerkebunan::class, 'bantuan_bibit_perkebunan_id');
    }

    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class);
    }
}
