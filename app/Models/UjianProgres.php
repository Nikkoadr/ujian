<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianProgres extends Model
{
    protected $table = 'ujian_progres';

    protected $fillable = [
        'user_id',
        'mapel_id',
        'status',
        'mulai_ujian',
    ];

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
        return $this->belongsTo(Bank_pertanyaan::class);
    }

    public function bankJawaban()
    {
        return $this->belongsTo(Bank_jawaban::class);
    }
}
