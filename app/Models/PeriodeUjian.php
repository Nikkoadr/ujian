<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeUjian extends Model
{
    use HasFactory;

    protected $table = 'periode_ujian';
    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'is_active',
    ];

    // (Opsional) Casting agar is_active otomatis boolean
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
