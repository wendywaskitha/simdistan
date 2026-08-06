<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nama', 'desa_id', 'gapoktan_id', 'ketua', 'sk_pembentukan', 'berita_acara', 'ketua_petani_id'])]
class KelompokTani extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the petani who is the leader of the kelompok tani.
     */
    public function ketuaPetani(): BelongsTo
    {
        return $this->belongsTo(Petani::class, 'ketua_petani_id');
    }

    /**
     * Get the desa that owns the kelompok tani.
     */
    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    /**
     * Get the gapoktan that owns the kelompok tani.
     */
    public function gapoktan(): BelongsTo
    {
        return $this->belongsTo(Gapoktan::class);
    }

    /**
     * Get the petanis for the kelompok tani.
     */
    public function petanis(): HasMany
    {
        return $this->hasMany(Petani::class);
    }
}
