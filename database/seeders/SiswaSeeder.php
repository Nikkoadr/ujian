<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua user yang role_id = 3 (siswa)
        $users = User::where('role_id', 3)->get();

        // Ambil semua kelas dari tabel kelas
        $kelasList = Kelas::all();

        $userIndex = 0;

        foreach ($kelasList as $kelas) {
            // Tentukan jumlah siswa per kelas (sesuaikan dengan kebutuhan)
            $jumlahSiswa = 10; // misal 10 siswa per kelas

            for ($i = 0; $i < $jumlahSiswa; $i++) {
                if (!isset($users[$userIndex])) {
                    break; // berhenti jika user habis
                }

                $user = $users[$userIndex];

                // Generate NISN dan NIS (unik, pakai user id)
                $nisn = 'NISN' . str_pad($user->id, 8, '0', STR_PAD_LEFT);
                $nis = 'NIS' . str_pad($user->id, 6, '0', STR_PAD_LEFT);

                // Buat data siswa
                Siswa::create([
                    'user_id' => $user->id,
                    'kelas_id' => $kelas->id,
                    'nisn' => $nisn,
                    'nis' => $nis,
                ]);

                $userIndex++;
            }
        }
    }
}
