<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank_soal extends Model
{
    protected $table = 'bank_soal';
    protected $guarded = [];

    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class);
    }

    public function kompetensiKeahlian()
    {
        return $this->belongsTo(Kompetensi_keahlian::class, 'kompetensi_keahlian_id');
    }

    public function pertanyaans()
    {
        return $this->hasMany(Bank_pertanyaan::class, 'bank_soal_id');
    }
}
