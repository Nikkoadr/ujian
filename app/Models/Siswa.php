<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nisn',
        'nis',
        'kelas_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
