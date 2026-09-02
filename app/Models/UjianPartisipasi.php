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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
}
