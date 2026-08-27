<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank_jawaban extends Model
{
    protected $table = 'bank_jawaban';
    protected $guarded = [];

    public function pertanyaan()
    {
        return $this->belongsTo(Bank_pertanyaan::class, 'bank_pertanyaan_id');
    }
}
