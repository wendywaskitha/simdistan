<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nama'])]
class KategoriKomoditas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kategori_komoditas';

    /**
     * Get the komoditas for the category.
     */
    public function komoditas(): HasMany
    {
        return $this->hasMany(Komoditas::class);
    }
}
