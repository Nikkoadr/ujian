<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soal';
    protected $guarded = [];

    // Relasi ke periode ujian
    public function periodeUjian()
    {
        return $this->belongsTo(PeriodeUjian::class);
    }

    // Relasi ke jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    // Relasi ke mapel
    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    // Relasi many-to-many dengan bank_pertanyaan
    public function bankPertanyaan()
    {
        return $this->belongsToMany(BankPertanyaan::class, 'soal_bank_pertanyaan');
    }
}
