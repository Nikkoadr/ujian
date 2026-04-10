<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $table = 'mapel';

    protected $fillable = [
        'tanggal_ujian',
        'jam_mulai',
        'jam_selesai',
        'kode_mapel',
        'nama_mapel',
        'durasi',
        'token',
        'status',
        'tingkat_id',
        'kompetensi_keahlian_id',
    ];

    public function partisipasi()
    {
        return $this->hasOne(UjianPartisipasi::class, 'mapel_id', 'id');
    }

    public function soals()
    {
        return $this->hasMany(Soal::class, 'mapel_id');
    }
}
