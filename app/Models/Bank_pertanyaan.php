<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank_pertanyaan extends Model
{
    protected $table = 'bank_pertanyaan';
    protected $guarded = [];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function jawaban()
    {
        return $this->hasMany(Bank_jawaban::class, 'bank_pertanyaan_id');
    }

    public function kunciJawaban()
    {
        return $this->belongsTo(Bank_jawaban::class, 'kunci_jawaban_id');
    }

    public function soal()
    {
        return $this->belongsToMany(Soal::class, 'soal_bank_pertanyaan');
    }
}
