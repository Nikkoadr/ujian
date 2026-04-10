<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mapel;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MapelSeeder extends Seeder
{
    public function run()
    {
        $mapelData = [
            // TKR
            ['kode' => 'TKR01', 'nama' => 'DASAR-DASAR KEJURUAN TKR KELAS 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => 1],
            ['kode' => 'TKR02', 'nama' => 'SISTEM CHASIS KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 1],
            ['kode' => 'TKR03', 'nama' => 'SISTEM CHASIS KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 1],
            ['kode' => 'TKR04', 'nama' => 'SISTEM PEMINDAH TENAGA KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 1],
            ['kode' => 'TKR05', 'nama' => 'SISTEM PEMINDAH TENAGA KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 1],
            ['kode' => 'TKR06', 'nama' => 'SISTEM ENGINE KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 1],
            ['kode' => 'TKR07', 'nama' => 'SISTEM ENGINE KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 1],
            ['kode' => 'TKR08', 'nama' => 'SISTEM KELISTRIKAN OTOMOTIF KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 1],
            ['kode' => 'TKR09', 'nama' => 'SISTEM KELISTRIKAN OTOMOTIF KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 1],

            // TEI
            ['kode' => 'TEI01', 'nama' => 'DASAR-DASAR KEJURUAN TEI KELAS 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => 2],
            ['kode' => 'TEI02', 'nama' => 'PENERAPAN RANGKAIAN ELEKTRONIKA KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 2],
            ['kode' => 'TEI03', 'nama' => 'SISTEM KENDALI ELEKTRONIK KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 2],
            ['kode' => 'TEI04', 'nama' => 'PEMELIHARAAN & PERBAIKAN PERALATAN ELEKTRONIK INDUSTRI (PPPEI) KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 2],
            ['kode' => 'TEI05', 'nama' => 'PEMROGRAMAN SYSTEM EMBEDDED KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 2],
            ['kode' => 'TEI06', 'nama' => 'ANTARMUKA & KOMUNKASI DATA KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 2],
            ['kode' => 'TEI07', 'nama' => 'SISTEM KENDALI INDUSTRI KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 2],
            ['kode' => 'TEI08', 'nama' => 'PEMROGRAMAN SYSTEM EMBEDDED KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 2],
            ['kode' => 'TEI09', 'nama' => 'ANTARMUKA & KOMUNKASI DATA KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 2],

            // TPL
            ['kode' => 'TPL01', 'nama' => 'DASAR-DASAR KEJURUAN (DDK) TPL KELAS 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => 3],
            ['kode' => 'TPL02', 'nama' => 'MUTU PENGELASAN KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 3],
            ['kode' => 'TPL03', 'nama' => 'GAMBAR TEKNIK KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 3],
            ['kode' => 'TPL04', 'nama' => 'LAS FCAW KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 3],
            ['kode' => 'TPL05', 'nama' => 'LAS BUSUR MANUAL KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 3],
            ['kode' => 'TPL06', 'nama' => 'LAS BUSUR MANUAL KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 3],
            ['kode' => 'TPL07', 'nama' => 'LAS TIG KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 3],
            ['kode' => 'TPL08', 'nama' => 'LAS TIG KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 3],
            ['kode' => 'TPL09', 'nama' => 'GAMBAR TEKNIK KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 3],

            // TKJ
            ['kode' => 'TKJ01', 'nama' => 'DASAR-DASAR KEJURUAN TKJ KELAS 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => 4],
            ['kode' => 'TKJ02', 'nama' => 'PERENCANAAN & PENGALAMATAN JARINGAN KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 4],
            ['kode' => 'TKJ03', 'nama' => 'PERENCANAAN & PENGALAMATAN JARINGAN KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 4],
            ['kode' => 'TKJ04', 'nama' => 'TEKNOLOGI JARINGAN KABEL & NIRKABEL KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 4],
            ['kode' => 'TKJ05', 'nama' => 'TEKNOLOGI JARINGAN KABEL & NIRKABEL KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 4],
            ['kode' => 'TKJ06', 'nama' => 'ADMINITRASI SISTEM JARINGAN KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 4],
            ['kode' => 'TKJ07', 'nama' => 'ADMINITRASI SISTEM JARINGAN KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 4],
            ['kode' => 'TKJ08', 'nama' => 'KEAMANAN JARINGAN KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 4],
            ['kode' => 'TKJ09', 'nama' => 'KEAMANAN JARINGAN KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 4],
            ['kode' => 'TKJ10', 'nama' => 'KONFIGURASI PERANGKAT JARINGAN KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 4],
            ['kode' => 'TKJ11', 'nama' => 'KONFIGURASI PERANGKAT JARINGAN KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 4],

            // TSM
            ['kode' => 'TSM01', 'nama' => 'DASAR-DASAR KEJURUAN TSM KELAS 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => 5],
            ['kode' => 'TSM02', 'nama' => 'SISTEM ENGINE KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 5],
            ['kode' => 'TSM03', 'nama' => 'SISTEM ENGINE KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 5],
            ['kode' => 'TSM04', 'nama' => 'SISTEM CHASIS KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 5],
            ['kode' => 'TSM05', 'nama' => 'PENGOLOLAAN BENGKEL KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 5],
            ['kode' => 'TSM06', 'nama' => 'SISTEM PEMINDAH TENAGA KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 5],
            ['kode' => 'TSM07', 'nama' => 'SISTEM KELISTRIKAN OTOMOTIF KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 5],
            ['kode' => 'TSM08', 'nama' => 'SISTEM PEMINDAH TENAGA KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 5],
            ['kode' => 'TSM09', 'nama' => 'SISTEM KELISTRIKAN OTOMOTIF KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 5],

            // FKK
            ['kode' => 'FKK01', 'nama' => 'DASAR-DASAR KEJURUAN FKK KELAS 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => 6],
            ['kode' => 'FKK02', 'nama' => 'TKJ02KEJURUAN FKK KELAS 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => 6],
            ['kode' => 'FKK03', 'nama' => 'KEJURUAN FKK KELAS 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => 6],

            // Adnor
            ['kode' => 'PAI10', 'nama' => 'PAI & Budi Pekerti Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'PAI11', 'nama' => 'PAI & Budi Pekerti Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'PAI12', 'nama' => 'PAI & Budi Pekerti Kelas 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => null],
            ['kode' => 'PKN10', 'nama' => 'Pendidikan Pancasila Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'PKN11', 'nama' => 'Pendidikan Pancasila Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'PKN12', 'nama' => 'Pendidikan Pancasila Kelas 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => null],
            ['kode' => 'MTK10', 'nama' => 'Matematika Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'MTK11', 'nama' => 'Matematika Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'MTK12', 'nama' => 'Matematika Kelas 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => null],
            ['kode' => 'SEJARAH10', 'nama' => 'Sejarah Indonesia Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'SEJARAH11', 'nama' => 'Sejarah Indonesia Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'IPAS10', 'nama' => 'PROJEK ILMU PENGETAHUAN ALAM & SOSIAL Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BING10', 'nama' => 'Bahasa Inggris Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BING11', 'nama' => 'Bahasa Inggris Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BING12', 'nama' => 'Bahasa Inggris Kelas 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => null],
            ['kode' => 'MTKPIL11', 'nama' => 'Matematika Pilihan Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'MTKPIL12', 'nama' => 'Matematika Pilihan Kelas 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BI10', 'nama' => 'Bahasa Indonesia Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BI11', 'nama' => 'Bahasa Indonesia Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BI12', 'nama' => 'Bahasa Indonesia Kelas 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => null],
            ['kode' => 'PKK11', 'nama' => 'PROJEK ILMU & KEWIRAUSAHAAN Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'PKK12', 'nama' => 'PROJEK ILMU & KEWIRAUSAHAAN Kelas 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => null],
            ['kode' => 'KEMUH10', 'nama' => 'Kemuhammadiyahan Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'KEMUH11', 'nama' => 'Kemuhammadiyahan Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'KEMUH12', 'nama' => 'Kemuhammadiyahan Kelas 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => null],
            ['kode' => 'SENI10', 'nama' => 'Seni Budaya Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BTQ10', 'nama' => 'Baca Tulis Quran Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BTQ11', 'nama' => 'Baca Tulis Quran Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BTQ12', 'nama' => 'Baca Tulis Quran Kelas 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => null],
            ['kode' => 'PJOK10', 'nama' => 'Pendidikan Jasmani Olahraga Kesehatan Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'PJOK11', 'nama' => 'Pendidikan Jasmani Olahraga Kesehatan Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BA10', 'nama' => 'Bahasa Arab Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BA11', 'nama' => 'Bahasa Arab Kelas 11', 'tingkat_id' => 2, 'kompetensi_keahlian_id' => null],
            ['kode' => 'BA12', 'nama' => 'Bahasa Arab Kelas 12', 'tingkat_id' => 3, 'kompetensi_keahlian_id' => null],
            ['kode' => 'TI10', 'nama' => 'Informatika Kelas 10', 'tingkat_id' => 1, 'kompetensi_keahlian_id' => null],
        ];

        foreach ($mapelData as $data) {
            Mapel::create([
                'tanggal_ujian' => Carbon::now()->addDays(rand(1, 30)), // random tanggal ujian dalam 30 hari ke depan
                'jam_mulai' => Carbon::createFromTime(rand(7, 10), 0, 0)->format('H:i:s'), // jam mulai random 07-10
                'jam_selesai' => Carbon::createFromTime(rand(11, 15), 0, 0)->format('H:i:s'), // jam selesai random 11-15
                'kode_mapel' => $data['kode'],
                'nama_mapel' => $data['nama'],
                'durasi' => '01:30:00', // default durasi 1 jam 30 menit
                'tingkat_id' => $data['tingkat_id'],
                'kompetensi_keahlian_id' => $data['kompetensi_keahlian_id'],
                'token' => Str::upper(Str::random(6)),
                'status' => 'aktif',
            ]);
        }
    }
}
