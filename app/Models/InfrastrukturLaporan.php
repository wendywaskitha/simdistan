<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'infrastruktur_id',
    'tanggal_laporan',
    'kondisi',
    'progres_fisik',
    'keterangan'
])]
class InfrastrukturLaporan extends Model
{
    use HasFactory;

    /**
     * Get the infrastruktur that owns the report.
     */
    public function infrastruktur(): BelongsTo
    {
        return $this->belongsTo(Infrastruktur::class);
    }
}
