<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create(
            [
                'id' => 1,
                'max_pelanggaran' => 10,
                'max_tombol_selesai' => 300,
                'anti_nyontek' => true,
            ]
        );
    }
}
