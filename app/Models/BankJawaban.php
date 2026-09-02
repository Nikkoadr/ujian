<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankJawaban extends Model
{
    protected $table = 'bank_jawaban';
    protected $guarded = [];

    public function pertanyaan()
    {
        return $this->belongsTo(BankPertanyaan::class, 'bank_pertanyaan_id');
    }
}
