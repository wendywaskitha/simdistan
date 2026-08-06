<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'nama',
    'deskripsi'
])]
class JenisPupuk extends Model
{
    use HasFactory;

    protected $table = 'jenis_pupuks';
}
