<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\UjianSiswa;
use App\Models\ProgresSiswa;
use App\Models\BankPertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UjianController extends Controller
{
    /**
     * Tampilkan halaman ujian.
     */
    public function showExam(Jadwal $jadwal)
    {
        // Cek akses token
        if (session('akses_ujian_' . $jadwal->id) !== Auth::id()) {
            return redirect()->route('home')->with('error', 'Silakan masukkan token ujian terlebih dahulu.');
        }

        $user = Auth::user();
        $mapel = $jadwal->mapel;

        // Ambil bank_pertanyaan yang terhubung dengan jadwal
        $bankPertanyaanIds = DB::table('soal_bank_pertanyaan')
            ->join('soal', 'soal.id', '=', 'soal_bank_pertanyaan.soal_id')
            ->where('soal.jadwal_id', $jadwal->id)
            ->pluck('soal_bank_pertanyaan.bank_pertanyaan_id');

        if ($bankPertanyaanIds->isEmpty()) {
            return redirect()->route('home')->with('error', 'Belum ada soal untuk ujian ini.');
        }

        $bankPertanyaan = BankPertanyaan::with('jawaban')
            ->whereIn('id', $bankPertanyaanIds)
            ->orderBy('id')
            ->get();

        // Progres siswa
        $progresMap = ProgresSiswa::where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->get()
            ->keyBy('bank_pertanyaan_id');

        // Format soal
        $listSoal = $bankPertanyaan->map(function ($bp, $index) use ($progresMap) {
            $progres = $progresMap->get($bp->id);
            $jawabanTerpilih = $progres ? $progres->bank_jawaban_id : null;
            $isRagu = $progres ? (bool) $progres->is_ragu : false;

            $pilihan = $bp->jawaban->sortBy('urutan')->map(function ($jawaban, $key) {
                return [
                    'db_id' => $jawaban->id,
                    'label' => chr(65 + $key),
                    'teks'  => $jawaban->teks_jawaban,
                ];
            });

            return [
                'id'               => $bp->id,
                'nomor'            => $index + 1,
                'pertanyaan'       => $bp->pertanyaan,
                'gambar_soal'      => $bp->gambar_soal,
                'pilihan'          => $pilihan,
                'jawaban_terpilih' => $jawabanTerpilih,
                'is_ragu'          => $isRagu,
            ];
        });

        // ------------------------------------------------------------------
        // 1. AMBIL / BUAT DATA UJIAN SISWA
        // ------------------------------------------------------------------
        $ujianSiswa = UjianSiswa::firstOrCreate(
            [
                'user_id'   => $user->id,
                'jadwal_id' => $jadwal->id,
            ],
            [
                'status'      => 'sedang mengerjakan',
                'pelanggaran' => 0,
                'mulai_ujian' => now(),
            ]
        );

        $pelanggaran = $ujianSiswa->pelanggaran;

        // ------------------------------------------------------------------
        // 2. PERHITUNGAN SISA WAKTU (mulai_ujian + durasi)
        // ------------------------------------------------------------------
        // Parse waktu mulai ujian siswa
        $waktuMulaiSiswa = \Carbon\Carbon::parse($ujianSiswa->mulai_ujian);

        // Ambil jam, menit, detik dari durasi jadwal (misal '01:30:00')
        $durasiParts = explode(':', $jadwal->durasi);
        $jamDurasi   = (int) ($durasiParts[0] ?? 0);
        $menitDurasi = (int) ($durasiParts[1] ?? 0);
        $detikDurasi = (int) ($durasiParts[2] ?? 0);

        // Waktu selesai siswa
        $waktuSelesai = $waktuMulaiSiswa->copy()
            ->addHours($jamDurasi)
            ->addMinutes($menitDurasi)
            ->addSeconds($detikDurasi);

        $now = \Carbon\Carbon::now();

        // Hitung sisa detik (jika sekarang melewati waktu selesai, set ke 0)
        if ($now->greaterThanOrEqualTo($waktuSelesai)) {
            $timeLeft = 0;
        } else {
            $timeLeft = $now->diffInSeconds($waktuSelesai, false);
        }

        // ------------------------------------------------------------------
        // PENGATURAN
        // ------------------------------------------------------------------
        $settingTombolSelesai = $jadwal->setting_tombol_selesai ?? 0; // dalam menit
        $settingTombolSelesaiDetik = $settingTombolSelesai * 60;

        $settingAntiNyontek = $jadwal->setting_anti_nyontek ?? false;
        $settingMaxPelanggaran = $jadwal->setting_max_pelanggaran ?? 3;

        return view('ujian.index', [
            'jadwal'                => $jadwal,
            'mapel'                 => $mapel,
            'listSoal'              => $listSoal,
            'timeLeft'              => (int) $timeLeft,
            'settingTombolSelesai'  => $settingTombolSelesaiDetik,
            'settingAntiNyontek'    => $settingAntiNyontek,
            'settingMaxPelanggaran' => $settingMaxPelanggaran,
            'pelanggaran'           => $pelanggaran,
        ]);
    }

    public function simpan(Request $request)
    {
        try {
            $request->validate([
                'mapel_id'   => 'required|exists:mapel,id',
                'soal_id'    => 'required|exists:bank_pertanyaan,id',
                'jawaban_id' => 'nullable|exists:bank_jawaban,id',
                'is_ragu'    => 'required|boolean',
            ]);

            $user = Auth::user();

            $jadwal = Jadwal::where('mapel_id', $request->mapel_id)
                ->where('status', 'aktif')
                ->first();

            if (!$jadwal) {
                return response()->json(['error' => 'Jadwal ujian tidak ditemukan'], 404);
            }

            // Validasi waktu (uji apakah ujian masih berlangsung)
            $tanggal = $jadwal->tanggal_ujian;
            $tanggalStr = ($tanggal instanceof Carbon) ? $tanggal->toDateString() : substr($tanggal, 0, 10);
            $jamMulai = $jadwal->jam_mulai;
            $jamMulaiStr = ($jamMulai instanceof Carbon) ? $jamMulai->toTimeString() : substr($jamMulai, 0, 8);
            $waktuMulai = Carbon::createFromFormat('Y-m-d H:i:s', $tanggalStr . ' ' . $jamMulaiStr);

            $durasiParts = explode(':', $jadwal->durasi);
            $waktuSelesai = $waktuMulai->copy()
                ->addHours((int) ($durasiParts[0] ?? 0))
                ->addMinutes((int) ($durasiParts[1] ?? 0))
                ->addSeconds((int) ($durasiParts[2] ?? 0));

            if (Carbon::now()->greaterThan($waktuSelesai)) {
                return response()->json(['error' => 'Waktu ujian telah habis'], 403);
            }

            // ------------------------------------------------------------------
            // SIMPAN PROGRES SECARA MANUAL (hindari error 1364)
            // ------------------------------------------------------------------
            $progres = ProgresSiswa::where('user_id', $user->id)
                ->where('jadwal_id', $jadwal->id)
                ->where('bank_pertanyaan_id', $request->soal_id)
                ->first();

            if ($progres) {
                // Update data yang sudah ada
                $progres->bank_jawaban_id = $request->jawaban_id;
                $progres->is_ragu = $request->is_ragu;
                $progres->save();
            } else {
                // Buat baru dengan semua field
                ProgresSiswa::create([
                    'user_id'            => $user->id,
                    'jadwal_id'          => $jadwal->id,
                    'bank_pertanyaan_id' => $request->soal_id,
                    'bank_jawaban_id'    => $request->jawaban_id,
                    'is_ragu'            => $request->is_ragu,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil disimpan',
            ]);
        } catch (\Exception $e) {
            Log::error('Error simpan jawaban: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Catat pelanggaran.
     */
    public function pelanggaran(Request $request)
    {
        try {
            $request->validate(['mapel_id' => 'required|exists:mapel,id']);

            $user = Auth::user();

            $jadwal = Jadwal::where('mapel_id', $request->mapel_id)
                ->where('status', 'aktif')
                ->first();

            if (!$jadwal) {
                return response()->json(['error' => 'Jadwal ujian tidak ditemukan'], 404);
            }

            $ujianSiswa = UjianSiswa::where('user_id', $user->id)
                ->where('jadwal_id', $jadwal->id)
                ->first();

            if (!$ujianSiswa) {
                return response()->json(['error' => 'Data ujian siswa tidak ditemukan'], 404);
            }

            $ujianSiswa->increment('pelanggaran');
            $total = $ujianSiswa->pelanggaran;

            $maxPelanggaran = $jadwal->setting_max_pelanggaran ?? 3;
            $blocked = $total >= $maxPelanggaran;

            return response()->json([
                'total'   => $total,
                'blocked' => $blocked,
                'max'     => $maxPelanggaran,
            ]);
        } catch (\Exception $e) {
            Log::error('Error pelanggaran: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan server'], 500);
        }
    }

    /**
     * Blokir user.
     */
    public function blokir(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'User diblokir karena melanggar aturan ujian']);
    }

    /**
     * Selesaikan ujian.
     */
    public function selesai(Request $request, $mapelId)
    {
        $user = Auth::user();

        $jadwal = Jadwal::where('mapel_id', $mapelId)
            ->where('status', 'aktif')
            ->first();

        if ($jadwal) {
            UjianSiswa::where('user_id', $user->id)
                ->where('jadwal_id', $jadwal->id)
                ->update([
                    'status'        => 'selesai',
                    'selesai_ujian' => now(),
                ]);

            session()->forget('akses_ujian_' . $jadwal->id);
        }

        return redirect()->route('home')->with('success', 'Ujian selesai, terima kasih.');
    }
}
