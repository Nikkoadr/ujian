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

}
