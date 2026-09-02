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

}
