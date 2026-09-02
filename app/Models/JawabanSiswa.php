<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
    protected $table = 'jawaban_siswa';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function bankPertanyaan()
    {
        return $this->belongsTo(BankPertanyaan::class);
    }

    public function bankJawaban()
    {
        return $this->belongsTo(BankJawaban::class);
    }
}
