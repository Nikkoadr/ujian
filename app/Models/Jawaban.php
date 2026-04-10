<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'jawaban';

    protected $fillable = [
        'soal_id',
        'teks_jawaban',
        'gambar_jawaban',
        'jawaban_benar',
    ];

    protected $casts = [
        'jawaban_benar' => 'boolean',
    ];

    // Relasi balik ke Soal
    public function soal()
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }
}
