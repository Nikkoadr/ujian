<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanUjianExport implements FromCollection, WithHeadings, WithMapping
{
    protected $results;

    public function __construct($results)
    {
        $this->results = $results;
    }

    public function collection()
    {
        return $this->results;
    }

    // Header Kolom di Excel
    public function headings(): array
    {
        return ["NIS", "Nama Siswa", "Kelas", "Mapel", "Nilai", "Status"];
    }

    // Mapping Data agar rapi di Excel
    public function map($res): array
    {
        return [
            $res->nis,
            $res->nama_siswa,
            $res->nama_kelas,
            $res->nama_mapel,
            $res->nilai,
            strtoupper($res->status_label)
        ];
    }
}
