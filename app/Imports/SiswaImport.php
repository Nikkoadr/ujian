<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Gunakan Transaction agar jika satu baris gagal, semua dibatalkan
        return DB::transaction(function () use ($row) {
            // 1. Buat User
            $user = User::create([
                'nama'          => $row['nama'],
                'jenis_kelamin' => strtoupper($row['jenis_kelamin']),
                'email'         => $row['email'],
                'password'      => Hash::make($row['password']),
                'role_id'          => '3',
                'status'        => 'aktif',
            ]);

            $kelas = Kelas::where('nama_kelas', $row['kelas'])->first();
            return new Siswa([
                'user_id'  => $user->id,
                'kelas_id' => $kelas ? $kelas->id : null,
                'nisn'     => $row['nisn'],
                'nis'      => $row['nis'],
            ]);
        });
    }
}
