<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanUjianExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnFormatting,
    ShouldAutoSize,
    WithStyles,
    WithCustomStartCell
{
    protected $results;
    protected $judul;

    public function __construct($results, $judul)
    {
        $this->results = $results;
        $this->judul = $judul;
    }

    public function collection()
    {
        return $this->results;
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function headings(): array
    {
        return [
            'NIS',
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Nilai',
        ];
    }

    public function map($res): array
    {
        return [
            "'" . $res->nis,
            "'" . $res->nisn,
            $res->nama_siswa,
            $res->nama_kelas,
            number_format($res->nilai, 1, ',', '.'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Judul
        $sheet->mergeCells('A1:E1');

        $sheet->setCellValue('A1', $this->judul);

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Header
        $sheet->getStyle('A3:E3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Tengah kolom nilai
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle("E4:E{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
