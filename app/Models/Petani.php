<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['kelompok_tani_id', 'nama', 'nik', 'telepon', 'alamat', 'ktp', 'luas_lahan'])]
class Petani extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the kelompok tani that owns the petani.
     */
    public function kelompokTani(): BelongsTo
    {
        return $this->belongsTo(KelompokTani::class);
    }
}
