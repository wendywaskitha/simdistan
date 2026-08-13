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
    'bulan',
    'target',
])]
class TargetTanam extends Model
{
    use HasFactory;

    protected $table = 'target_tanams';

    /**
     * Get the kecamatan that owns the TargetTanam.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Get the komoditas that owns the TargetTanam.
     */
    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class);
    }
}
