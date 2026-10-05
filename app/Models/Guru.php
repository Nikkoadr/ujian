<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';

    protected $fillable = [
        'user_id',
        'nip',
        'gelar_depan',
        'gelar_belakang',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Nama lengkap beserta gelar, mis. "Dr. Budi Santoso, M.Pd.".
     */
    public function getNamaLengkapAttribute(): string
    {
        $nama = trim((string) ($this->user->nama ?? ''));
        $depan = trim((string) ($this->gelar_depan ?? ''));
        $belakang = trim((string) ($this->gelar_belakang ?? ''));

        if ($depan !== '' && substr($depan, -1) !== '.') {
            $depan .= '.';
        }

        $hasil = trim($depan.' '.$nama);

        if ($belakang !== '') {
            $hasil .= ', '.ltrim($belakang, ', ');
        }

        return $hasil;
    }

    public function pengawas()
    {
        return $this->hasOne(Pengawas::class, 'guru_id');
    }
}
