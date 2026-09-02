<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresSiswa extends Model
{
    protected $table = 'progres_siswa';

    protected $fillable = [
        'user_id',
        'mapel_id',
        'status',
        'mulai_ujian',
    ];
}
