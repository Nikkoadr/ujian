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
        $transformTime = function ($value) {
            if (!$value) return null;

            // Jika Excel mengirimkan angka desimal (format Time di Excel)
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('H:i:s');
            }

            // Jika sudah berupa string (format Text di Excel), pastikan formatnya H:i:s
            return Carbon::parse($value)->format('H:i:s');
        };
        $tingkat = Tingkat::where('nama_tingkat', $row['tingkat'])->first();
        $keahlian = Kompetensi_keahlian::where('nama_kompetensi', $row['keahlian'])->first();

        return new Mapel([
            'kode_mapel'             => $row['kode_mapel'],
            'nama_mapel'             => $row['mata_pelajaran'],
            'tanggal_ujian'          => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal']),
            'tanggal_ujian'          => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal']),
            'jam_mulai'              => $transformTime($row['jam_mulai']),
            'jam_selesai'            => $transformTime($row['jam_selesai']),
            'durasi'                 => $transformTime($row['durasi']),
            'tingkat_id'             => $tingkat ? $tingkat->id : null,
            'kompetensi_keahlian_id' => $keahlian ? $keahlian->id : null,
            'status'                 => 'aktif',
            'token'                  => Str::random(6),
        ]);
    }
}
