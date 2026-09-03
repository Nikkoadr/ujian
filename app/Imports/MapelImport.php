<?php

namespace App\Imports;

use App\Models\Mapel;
use App\Models\Tingkat;
use App\Models\Kompetensi_keahlian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MapelImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {

        // 3. Cari ID Relasi
        $tingkat = Tingkat::where('nama_tingkat', $row['tingkat'])->first();
        $keahlian = Kompetensi_keahlian::where('nama_kompetensi', $row['keahlian'])->first();

        // 4. Simpan ke Database
        return new Mapel([
            'kode_mapel'             => $row['kode_mapel'],
            'nama_mapel'             => $row['mata_pelajaran'],
            'tingkat_id'             => $tingkat ? $tingkat->id : null,
            'kompetensi_keahlian_id' => $keahlian ? $keahlian->id : null,
        ]);
    }
}
