<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nama', 'kecamatan_id', 'ketua'])]
class Gapoktan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the kecamatan that owns the gapoktan.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Get the kelompok tanis for the gapoktan.
     */
    public function kelompokTanis(): HasMany
    {
        return $this->hasMany(KelompokTani::class);
    }
}
