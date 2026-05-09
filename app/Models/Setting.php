<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'setting';

    protected $fillable = [
        'max_pelanggaran',
        'max_tombol_selesai',
        'anti_nyontek',
    ];
}
