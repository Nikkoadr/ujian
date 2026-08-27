<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujian';

    protected $fillable = [
        'periode_ujian_id',
        'kode_ujian',
        'nama_ujian',
        'tanggal_ujian',
        'jam_mulai',
        'jam_selesai',
        'durasi',
        'tingkat_id',
        'kompetensi_keahlian_id',
        'status',
        'token',
    ];

    protected $casts = [
        'tanggal_ujian' => 'date',
        'jam_mulai' => 'datetime:H:i:s',
        'jam_selesai' => 'datetime:H:i:s',
        'durasi' => 'datetime:H:i:s',
    ];

    // Relasi
    public function periodeUjian()
    {
        return $this->belongsTo(PeriodeUjian::class);
    }

    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class);
    }

    public function kompetensiKeahlian()
    {
        return $this->belongsTo(Kompetensi_keahlian::class);
    }

    // Relasi dengan soal (jika ada)
    public function soals()
    {
        return $this->hasMany(Soal::class, 'ujian_id');
    }
}
