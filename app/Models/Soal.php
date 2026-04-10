<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soal';

    protected $fillable = [
        'mapel_id',
        'pertanyaan',
        'gambar_soal',
        'jenis_soal',
        'bobot_nilai',
    ];

    // Relasi ke Mapel
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class, 'soal_id');
    }
}
