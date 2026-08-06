<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nama'])]
class Kecamatan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the desas for the kecamatan.
     */
    public function desas(): HasMany
    {
        return $this->hasMany(Desa::class);
    }

    /**
     * Get the BPPs for the kecamatan.
     */
    public function bpps(): HasMany
    {
        return $this->hasMany(Bpp::class);
    }
}
