<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nama', 'nip', 'telepon', 'bpp_id'])]
class Penyuluh extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the BPP that owns the penyuluh.
     */
    public function bpp(): BelongsTo
    {
        return $this->belongsTo(Bpp::class);
    }
}
