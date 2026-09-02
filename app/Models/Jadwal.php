<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $guarded = [];

    public function periodeUjian()
    {
        return $this->belongsTo(PeriodeUjian::class, 'periode_ujian_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function soal()
    {
        return $this->hasMany(Soal::class);
    }

}
