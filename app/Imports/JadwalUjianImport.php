<?php

namespace App\Imports;

use App\Models\Jadwal;
use App\Models\Mapel;
use App\Models\PeriodeUjian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class JadwalUjianImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cari mapel dari kode
        $mapel = Mapel::where('kode_mapel', $row['kode_mapel'])->first();

        if (!$mapel) {
            throw new \Exception("Mapel tidak ditemukan: {$row['kode_mapel']}");
        }

        // Cari periode dengan pencarian fleksibel
        $periode = $this->findPeriode($row['periode_ujian']);

        if (!$periode) {
            // Tampilkan daftar periode yang tersedia untuk debugging
            $available = PeriodeUjian::where('is_active', true)
                ->pluck('nama_periode')
                ->implode(', ');

            throw new \Exception(
                "Periode ujian aktif tidak ditemukan: '{$row['periode_ujian']}'. " .
                    "Periode tersedia: {$available}"
            );
        }

        // Konversi tanggal
        $tanggal = $this->parseDate($row['tanggal_ujian']);
        $jamMulai = $this->parseTime($row['jam_mulai']);
        $jamSelesai = $this->parseTime($row['jam_selesai']);
        $durasi = $this->parseDuration($row['durasi']);
        $token = $this->generateToken();

        return new Jadwal([
            'mapel_id' => $mapel->id,
            'periode_ujian_id' => $periode->id,
            'tanggal_ujian' => $tanggal,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'durasi' => $durasi,
            'token' => $token,
            'status' => 'aktif',
        ]);
    }

    /**
     * Cari periode dengan berbagai metode
     */
    private function findPeriode($namaPeriode)
    {
        // 1. Pencarian exact match (case sensitive)
        $periode = PeriodeUjian::where('nama_periode', $namaPeriode)
            ->where('is_active', true)
            ->first();

        if ($periode) return $periode;

        // 2. Pencarian case insensitive
        $periode = PeriodeUjian::whereRaw('LOWER(nama_periode) = ?', [strtolower($namaPeriode)])
            ->where('is_active', true)
            ->first();

        if ($periode) return $periode;

        // 3. Pencarian LIKE (mengandung kata kunci)
        $periode = PeriodeUjian::where('nama_periode', 'LIKE', "%{$namaPeriode}%")
            ->where('is_active', true)
            ->first();

        if ($periode) return $periode;

        // 4. Pencarian dengan menghilangkan spasi berlebih
        $cleanName = preg_replace('/\s+/', ' ', trim($namaPeriode));
        $periode = PeriodeUjian::whereRaw('REPLACE(nama_periode, "  ", " ") = ?', [$cleanName])
            ->where('is_active', true)
            ->first();

        if ($periode) return $periode;

        // 5. Pencarian dengan prefix (ambil 3 kata pertama)
        $words = explode(' ', $namaPeriode);
        $prefix = implode(' ', array_slice($words, 0, 3));
        $periode = PeriodeUjian::where('nama_periode', 'LIKE', "{$prefix}%")
            ->where('is_active', true)
            ->first();

        if ($periode) return $periode;

        return null;
    }

    private function parseDate($value)
    {
        if (!$value) return null;

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Format tanggal tidak valid: {$value}");
        }
    }

    private function parseTime($value)
    {
        if (!$value) return null;

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('H:i:s');
            }

            if (preg_match('/^\d{2}:\d{2}$/', $value)) {
                return $value . ':00';
            }

            return Carbon::parse($value)->format('H:i:s');
        } catch (\Exception $e) {
            throw new \Exception("Format waktu tidak valid: {$value}");
        }
    }

    private function parseDuration($value)
    {
        if (!$value) return '00:00:00';

        if (preg_match('/^\d{2}:\d{2}$/', $value)) {
            return $value . ':00';
        }

        if (is_numeric($value)) {
            $hours = floor($value / 60);
            $minutes = $value % 60;
            return sprintf('%02d:%02d:00', $hours, $minutes);
        }

        return $value;
    }

    private function generateToken()
    {
        do {
            $token = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6));
        } while (Jadwal::where('token', $token)->exists());

        return $token;
    }
}
