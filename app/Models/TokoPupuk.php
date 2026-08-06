<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'nama',
    'pemilik',
    'alamat',
    'telepon'
])]
class TokoPupuk extends Model
{
    use HasFactory;

    protected $table = 'toko_pupuks';

    /**
     * The kecamatan associated with the store.
     */
    public function kecamatans(): BelongsToMany
    {
        return $this->belongsToMany(Kecamatan::class, 'toko_kecamatan', 'toko_pupuk_id', 'kecamatan_id')
                    ->withTimestamps();
    }

    /**
     * Laporans associated with the store.
     */
    public function laporans(): HasMany
    {
        return $this->hasMany(LaporanPupuk::class, 'toko_pupuk_id');
    }
}
