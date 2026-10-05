<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat_id',
        'kompetensi_keahlian_id',
        'guru_id',
    ];

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class, 'tingkat_id');
    }

    public function kompetensi_keahlian()
    {
        return $this->belongsTo(Kompetensi_keahlian::class, 'kompetensi_keahlian_id');
    }
}
