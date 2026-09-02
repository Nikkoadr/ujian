<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankPertanyaan extends Model
{
    protected $table = 'bank_pertanyaan';
    protected $guarded = [];
    public function bankJawaban()
    {
        return $this->hasMany(BankJawaban::class);
    }

    public function soal()
    {
        return $this->belongsToMany(Soal::class, 'soal_bank_pertanyaan')
            ->withTimestamps();
    }

    public function jawaban()
    {
        return $this->hasMany(BankJawaban::class);
    }
}
