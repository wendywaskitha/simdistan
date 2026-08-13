<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'kecamatan_id',
    'komoditas_id',
    'tahun',
    'luas_lahan',
])]
class LuasLahanBaku extends Model
{
    use HasFactory;

    protected $table = 'luas_lahan_bakus';

    /**
     * Get the kecamatan that owns the LuasLahanBaku.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Get the komoditas that owns the LuasLahanBaku.
     */
    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class);
    }
}
