<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['komoditas_id', 'nama'])]
class Varietas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'varietas';

    /**
     * Get the komoditas that owns the varietas.
     */
    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class);
    }
}
