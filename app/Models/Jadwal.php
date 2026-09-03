<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $guarded = [];

    protected $casts = [
        'tanggal_ujian' => 'date',
        'jam_mulai' => 'datetime:H:i:s',
        'jam_selesai' => 'datetime:H:i:s',
        'durasi' => 'datetime:H:i:s',
    ];

    public function periodeUjian()
    {
        return $this->belongsTo(PeriodeUjian::class);
    }
    
    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function soal()
    {
        return $this->hasMany(Soal::class);
    }

    public function sesiUjian()
    {
        return $this->hasMany(SesiUjian::class);
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(JawabanSiswa::class);
    }

    public function ujianSiswa()
    {
        return $this->hasMany(UjianSiswa::class);
    }

}
