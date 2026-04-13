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
        $transformTime = function ($value) {
            if (!$value) return null;

            try {
                return Carbon::parse($value)->format('H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        };

        try {
            $tanggalUjian = Carbon::createFromFormat('d/m/Y', $row['tanggal_ujian'])->format('Y-m-d');
        } catch (\Exception $e) {
            $tanggalUjian = $row['tanggal_ujian'] ? Carbon::parse($row['tanggal_ujian'])->format('Y-m-d') : null;
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
            'token'                  => strtoupper(Str::random(6)),
        ]);
    }
}
