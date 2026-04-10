<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianPartisipasi extends Model
{
    protected $table = 'ujian_partisipasi';

    protected $fillable = [
        'user_id',
        'mapel_id',
        'status',
        'mulai_ujian',
    ];
}
