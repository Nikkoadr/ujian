<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Baris utama dibuat sekali; setting anti-cheat yang sudah diubah
        // admin tidak ditimpa saat seeder dijalankan ulang.
        $setting = Setting::firstOrCreate(
            ['id' => 1],
            [
                'max_pelanggaran' => 10,
                'max_tombol_selesai' => 300,
                'anti_nyontek' => true,
            ]
        );

        $setting->update(
            [
                'nama_sekolah' => 'SMK Muhammadiyah Kandanghaur',
                'alamat_sekolah' => 'Jl. Raya Karanganyar No. 28/A Kec. Kandanghaur Kab. Indramayu 45254',
                'nama_kepala_sekolah' => 'H. Afandi, S.Pd., M.Ed',
                'nama_wakakurikulum' => 'H. Heriyanto, M.Pd',
            ]
        );
    }
}
