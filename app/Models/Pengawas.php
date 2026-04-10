<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengawas extends Model
{
    protected $table = 'pengawas';

    protected $fillable = [
        'guru_id',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
