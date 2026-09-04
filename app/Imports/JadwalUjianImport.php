<?php

namespace App\Imports;

use App\Models\Jadwal;
use App\Models\Mapel;
use App\Models\PeriodeUjian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class JadwalUjianImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
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

        // Konversi waktu - FIX untuk format Excel desimal
        $jamMulai = $this->parseTimeExcel($row['jam_mulai']);
        $jamSelesai = $this->parseTimeExcel($row['jam_selesai']);

        // Konversi durasi
        $durasi = $this->parseDurationExcel($row['durasi']);

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
     * Validasi rules untuk import
     */
    public function rules(): array
    {
        return [
            'kode_mapel' => 'required|string',
            'periode_ujian' => 'required|string',
            'tanggal_ujian' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'durasi' => 'required',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'kode_mapel.required' => 'Kode mapel wajib diisi',
            'periode_ujian.required' => 'Periode ujian wajib diisi',
            'tanggal_ujian.required' => 'Tanggal ujian wajib diisi',
            'jam_mulai.required' => 'Jam mulai wajib diisi',
            'jam_selesai.required' => 'Jam selesai wajib diisi',
            'durasi.required' => 'Durasi wajib diisi',
        ];
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

    /**
     * Parse date dari berbagai format termasuk Excel serial number
     */
    private function parseDate($value)
    {
        if (!$value) return null;

        try {
            // Cek apakah nilai adalah angka (Excel serial number)
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Format d/m/Y (Indonesia)
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            }

            // Format Y-m-d
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }

            // Format m/d/Y (US)
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
            }

            // Format d-m-Y
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                return Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
            }

            // Fallback: coba parse dengan Carbon
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Format tanggal tidak valid: {$value}");
        }
    }

    /**
     * Parse waktu dari Excel (khusus menangani nilai desimal)
     * Excel menyimpan waktu sebagai desimal: 0.0520833333333333 = 01:15:00
     */
    private function parseTimeExcel($value)
    {
        if (!$value) return null;

        try {
            // ===== FIX UNTUK EXCEL TIME DESIMAL =====
            // Jika nilai adalah numerik (desimal dari Excel)
            if (is_numeric($value)) {
                // Konversi desimal Excel ke jam:menit:detik
                $totalSeconds = $value * 86400; // 1 hari = 86400 detik
                $hours = floor($totalSeconds / 3600);
                $minutes = floor(($totalSeconds % 3600) / 60);
                $seconds = round($totalSeconds % 60);

                // Format HH:MM:SS
                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }

            // Jika nilai sudah berupa string waktu
            $value = trim($value);

            // Format HH:MM:SS
            if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $value)) {
                return $value;
            }

            // Format HH:MM (tanpa detik)
            if (preg_match('/^\d{1,2}:\d{2}$/', $value)) {
                return $value . ':00';
            }

            // Format H:MM (tanpa leading zero)
            if (preg_match('/^\d{1}:\d{2}$/', $value)) {
                return '0' . $value . ':00';
            }

            // Format dengan AM/PM (e.g., 01:15 PM)
            if (preg_match('/^\d{1,2}:\d{2}\s?(AM|PM)$/i', $value)) {
                return Carbon::parse($value)->format('H:i:s');
            }

            // Fallback: coba parse dengan Carbon
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Exception $e) {
            throw new \Exception("Format waktu tidak valid: {$value}");
        }
    }

    /**
     * Parse durasi dari Excel (khusus menangani nilai desimal)
     */
    private function parseDurationExcel($value)
    {
        if (!$value) return '00:00:00';

        try {
            // ===== FIX UNTUK EXCEL TIME DESIMAL =====
            // Jika nilai adalah numerik (desimal dari Excel)
            if (is_numeric($value)) {
                // Konversi desimal Excel ke jam:menit:detik
                $totalSeconds = $value * 86400; // 1 hari = 86400 detik
                $hours = floor($totalSeconds / 3600);
                $minutes = floor(($totalSeconds % 3600) / 60);
                $seconds = round($totalSeconds % 60);

                // Format HH:MM:SS
                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }

            $value = trim($value);

            // Format HH:MM:SS
            if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $value)) {
                return $value;
            }

            // Format HH:MM (tanpa detik)
            if (preg_match('/^\d{1,2}:\d{2}$/', $value)) {
                return $value . ':00';
            }

            // Format menit (misal: 90 = 90 menit)
            if (is_numeric($value)) {
                $hours = floor($value / 60);
                $minutes = $value % 60;
                return sprintf('%02d:%02d:00', $hours, $minutes);
            }

            // Format dengan satuan (misal: 1 jam 30 menit)
            if (preg_match('/(\d+)\s*jam\s*(\d*)\s*menit?/i', $value, $matches)) {
                $hours = (int) $matches[1];
                $minutes = isset($matches[2]) ? (int) $matches[2] : 0;
                return sprintf('%02d:%02d:00', $hours, $minutes);
            }

            // Fallback
            return $value;
        } catch (\Exception $e) {
            throw new \Exception("Format durasi tidak valid: {$value}");
        }
    }

    /**
     * Generate token unik
     */
    private function generateToken()
    {
        do {
            $token = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6));
        } while (Jadwal::where('token', $token)->exists());

        return $token;
    }
}
