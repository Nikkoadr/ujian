<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soal';
    protected $guarded = [];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function bankPertanyaan()
    {
        return $this->belongsToMany(BankPertanyaan::class, 'soal_bank_pertanyaan')
            ->withTimestamps();
    }

    public function PeriodeUjian()
    {
        return $this->belongsTo(PeriodeUjian::class, 'periode_ujian_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
}
