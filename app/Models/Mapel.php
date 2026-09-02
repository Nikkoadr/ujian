<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $table = 'mapel';
    protected $guarded = [];

    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class, 'tingkat_id');
    }

    public function kompetensiKeahlian()
    {
        return $this->belongsTo(Kompetensi_keahlian::class, 'kompetensi_keahlian_id');
    }

    public function bankPertanyaan()
    {
        return $this->hasMany(BankPertanyaan::class, 'mapel_id');
    }

    public function soal()
    {
        return $this->hasMany(Soal::class);
    }

    public function jadwal()
    {
        return $this->hasOne(Jadwal::class);
    }
}
