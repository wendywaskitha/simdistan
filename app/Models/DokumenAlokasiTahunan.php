<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'tahun',
    'file_path'
])]
class DokumenAlokasiTahunan extends Model
{
    use HasFactory;

    protected $table = 'dokumen_alokasi_tahunans';
}
