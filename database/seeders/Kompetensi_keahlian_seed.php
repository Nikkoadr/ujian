<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kompetensi_keahlian;

class Kompetensi_keahlian_seed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kompetensi_keahlian::create([
            'nama_kompetensi' => 'Teknik Komputer dan Jaringan',
            'deskripsi' => 'Program studi yang fokus pada jaringan komputer dan infrastruktur TI.',
        ]);

        Kompetensi_keahlian::create([
            'nama_kompetensi' => 'Teknik Kendaraan Ringan',
            'deskripsi' => 'Program studi yang fokus pada perbaikan dan perawatan kendaraan ringan.',
        ]);

        Kompetensi_keahlian::create([
            'nama_kompetensi' => 'Teknik Elektronika Industri',
            'deskripsi' => 'Program studi yang fokus pada elektronika dan otomatisasi industri.',
        ]);
        Kompetensi_keahlian::create([
            'nama_kompetensi' => 'Teknik Pengelasan',
            'deskripsi' => 'Program studi yang fokus pada pengelasan dan fabrikasi logam.',
        ]);
        Kompetensi_keahlian::create([
            'nama_kompetensi' => 'Teknik Sepeda Motor',
            'deskripsi' => 'Program studi yang fokus pada perbaikan dan perawatan sepeda motor.',
        ]);
        Kompetensi_keahlian::create([
            'nama_kompetensi' => 'Layanan Penunjang Kefarmasian Klinis dan Komunitas (LPK3)',
            'deskripsi' => 'Program studi yang fokus pada layanan kefarmasian di klinik dan komunitas.',
        ]);
    }
}
