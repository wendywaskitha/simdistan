<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nama', 'deskripsi'])]
class JenisAlat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jenis_alats';

    /**
     * Get the alsintans of this machinery type.
     */
    public function alsintans(): HasMany
    {
        return $this->hasMany(Alsintan::class, 'jenis_alat_id');
    }
}
