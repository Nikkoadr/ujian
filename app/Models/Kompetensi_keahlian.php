<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kompetensi_keahlian extends Model
{
    protected $table = 'kompetensi_keahlian';

    protected $fillable = [
        'nama_kompetensi',
        'deskripsi',
    ];
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'kompetensi_keahlian_id');
    }
    public function peserta()
    {
        // langsung ambil peserta lewat kelas
        return $this->hasManyThrough(Peserta::class, Kelas::class, 'kompetensi_keahlian_id', 'kelas_id');
    }
}
