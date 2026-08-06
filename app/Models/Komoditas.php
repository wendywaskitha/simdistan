<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['kategori_komoditas_id', 'nama', 'jenis_periode', 'form_type', 'durasi_panen_bulan'])]
class Komoditas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'komoditas';

    /**
     * Get the category that owns the komoditas.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriKomoditas::class, 'kategori_komoditas_id');
    }

    /**
     * Get the varietass for the komoditas.
     */
    public function varietas(): HasMany
    {
        return $this->hasMany(Varietas::class);
    }
}
