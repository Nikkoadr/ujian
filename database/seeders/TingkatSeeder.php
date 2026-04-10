<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tingkat;

class TingkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tingkat 10
        Tingkat::create(['nama_tingkat' => '10']);
        // Tingkat 11
        Tingkat::create(['nama_tingkat' => '11']);
        // Tingkat 12
        Tingkat::create(['nama_tingkat' => '12']);
    }
}
