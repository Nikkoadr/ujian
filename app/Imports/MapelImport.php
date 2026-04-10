<?php

namespace App\Imports;

use App\Models\Mapel;
use App\Models\Tingkat;
use App\Models\Kompetensi_keahlian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MapelImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Helper untuk menangani format waktu string (07:30 -> 07:30:00)
        $transformTime = function ($value) {
            if (!$value) return null;

            try {
                // Jika isinya '07:30', ubah jadi format yang diterima database 'H:i:s'
                return \Carbon\Carbon::parse($value)->format('H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        };

        // Konversi Tanggal dari format 20/04/2026 ke 2026-04-20
        try {
            $tanggalUjian = \Carbon\Carbon::createFromFormat('d/m/Y', $row['tanggal'])->format('Y-m-d');
        } catch (\Exception $e) {
            // Jika gagal format d/m/Y, coba parse biasa atau set null
            $tanggalUjian = $row['tanggal'] ? \Carbon\Carbon::parse($row['tanggal'])->format('Y-m-d') : null;
        }

        $tingkat = Tingkat::where('nama_tingkat', $row['tingkat'])->first();
        $keahlian = Kompetensi_keahlian::where('nama_kompetensi', $row['keahlian'])->first();

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
            'token'                  => strtoupper(\Illuminate\Support\Str::random(6)),
        ]);
    }
}
