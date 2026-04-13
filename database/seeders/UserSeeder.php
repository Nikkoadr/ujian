<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'role_id' => 1,
            'nama' => 'Administrator',
            'jenis_kelamin' => 'laki-laki',
            'email' => 'admin@smkmuhkandanghaur.sch.id',
            'password' => Hash::make('Secret1234'),
        ]);
        // User::create([
        //     'role_id' => 2,
        //     'nama' => 'Pengawas',
        //     'jenis_kelamin' => 'laki-laki',
        //     'email' => 'pengawas@smkmuhkandanghaur.sch.id',
        //     'password' => Hash::make('Ps12345*'),
        // ]);

        // User::create([
        //     'role_id' => 2,
        //     'nama' => 'Pengawas',
        //     'jenis_kelamin' => 'laki-laki',
        //     'email' => 'pengawas@gmail.com',
        //     'password' => Hash::make('Secret1234'),
        // ]);

        // // User dummy siswa otomatis
        // $totalDummySiswa = 100; // jumlah siswa tambahan
        // for ($i = 1; $i <= $totalDummySiswa; $i++) {
        //     User::create([
        //         'role_id' => 3,
        //         'nama' => 'Siswa ' . $i,
        //         'jenis_kelamin' => ($i % 2 == 0) ? 'laki-laki' : 'perempuan',
        //         'email' => 'siswa' . $i . '@gmail.com',
        //         'password' => Hash::make('Secret1234'),
        //     ]);
        // }
    }
}
