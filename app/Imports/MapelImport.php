<?php

namespace App\Imports;

use App\Models\Mapel;
use App\Models\Tingkat;
use App\Models\Kompetensi_keahlian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MapelImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Helper untuk konversi Jam (Serial Excel atau String)
        $transformTime = function ($value) {
            if (!$value) return null;

            try {
                // Jika berupa angka serial (misal: 0.39583333333333)
                if (is_numeric($value)) {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('H:i:s');
                }
                // Jika berupa string (misal: "01:00")
                return Carbon::parse($value)->format('H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        };

        // 2. Konversi Tanggal Ujian (Dari angka 46181 ke Y-m-d)
        $tanggalUjian = null;
        if (isset($row['tanggal_ujian']) && $row['tanggal_ujian']) {
            try {
                if (is_numeric($row['tanggal_ujian'])) {
                    // Mengubah serial number Excel menjadi objek DateTime PHP
                    $tanggalUjian = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_ujian'])->format('Y-m-d');
                } else {
                    // Jika user mengisi manual sebagai teks DD/MM/YYYY
                    $tanggalUjian = Carbon::createFromFormat('d/m/Y', $row['tanggal_ujian'])->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // Fallback jika format tidak standar
                try {
                    $tanggalUjian = Carbon::parse($row['tanggal_ujian'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalUjian = null;
                }
            }
        }

        // 3. Cari ID Relasi
        $tingkat = Tingkat::where('nama_tingkat', $row['tingkat'])->first();
        $keahlian = Kompetensi_keahlian::where('nama_kompetensi', $row['keahlian'])->first();

        // 4. Simpan ke Database
        return new Mapel([
            'kode_mapel'             => $row['kode_mapel'],
            'nama_mapel'             => $row['mata_pelajaran'],
            'tanggal_ujian'          => $tanggalUjian,
            'jam_mulai'              => $transformTime($row['jam_mulai']),
            'jam_selesai'            => $transformTime($row['jam_selesai']),
            'durasi'                 => $transformTime($row['durasi']),
            'tingkat_id'             => $tingkat ? $tingkat->id : null,
            'kompetensi_keahlian_id' => $keahlian ? $keahlian->id : null,
            'status'                 => 'aktif',
            'token'                  => strtoupper(Str::random(6)),
        ]);
    }
}
