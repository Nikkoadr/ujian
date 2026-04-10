<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(
            [
                'id' => 1,
                'nama_role' => 'Admin',
            ]
        );
        Role::create(
            [
                'id' => 2,
                'nama_role' => 'Guru',
            ]
        );
        Role::create(
            [
                'id' => 3,
                'nama_role' => 'Siswa',
            ]
        );
    }
}
