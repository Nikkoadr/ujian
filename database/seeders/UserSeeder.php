<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // User manual
        User::create([
            'role_id' => 1,
            'nama' => 'Nikko Adrian',
            'jenis_kelamin' => 'laki-laki',
            'email' => 'nikkoadrian02@gmail.com',
            'password' => bcrypt('Secret1234'),
        ]);

        User::create([
            'role_id' => 2,
            'nama' => 'Pengawas',
            'jenis_kelamin' => 'laki-laki',
            'email' => 'pengawas@gmail.com',
            'password' => bcrypt('Secret1234'),
        ]);

        // User dummy siswa otomatis
        $totalDummySiswa = 100; // jumlah siswa tambahan
        for ($i = 1; $i <= $totalDummySiswa; $i++) {
            User::create([
                'role_id' => 3,
                'nama' => 'Siswa ' . $i,
                'jenis_kelamin' => ($i % 2 == 0) ? 'laki-laki' : 'perempuan',
                'email' => 'siswa' . $i . '@gmail.com',
                'password' => bcrypt('Secret1234'),
            ]);
        }
    }
}
