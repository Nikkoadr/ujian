<?php

namespace App\Http\Controllers;

use App\Models\BankPertanyaan;
use App\Models\Jadwal;
use App\Models\Soal;
use App\Models\SoalBankPertanyaan;
use App\Models\UjianPelanggaran;
use App\Models\UjianSiswa;
use App\Models\UjianProgres;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UjianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Render lembar pengerjaan soal ujian
     */
    public function showExam(Jadwal $jadwal)
    {
        // 1. Cek apakah session akses untuk jadwal ini ada dan milik user yang sedang login
        if (session('akses_ujian_' . $jadwal->id) !== Auth::id()) {
            return redirect()->route('home')->with('error', 'Silakan masukkan token ujian terlebih dahulu.');
        }

        // 2. Jika valid, tampilkan view ujian
        return view('ujian.index_test', compact('jadwal'));
    }

    /**
     * Simpan jawaban atau status ragu (AJAX).
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'mapel_id'      => 'required|exists:mapel,id',
            'soal_id'       => 'required|exists:bank_pertanyaan,id',
            'jawaban_id'    => 'nullable|exists:bank_jawaban,id',
            'is_ragu'       => 'required|boolean',
        ]);

        $user = Auth::user();
        $jadwalId = Session::get('ujian_jadwal_id');
        if (!$jadwalId) {
            return response()->json(['success' => false, 'message' => 'Sesi ujian tidak ditemukan.'], 422);
        }

        // Verifikasi bahwa soal ini milik jadwal yang sedang dikerjakan
        $soal = Soal::where('jadwal_id', $jadwalId)->first();
        if (!$soal) {
            return response()->json(['success' => false, 'message' => 'Soal tidak valid.'], 422);
        }

        $isValid = SoalBankPertanyaan::where('soal_id', $soal->id)
            ->where('bank_pertanyaan_id', $request->soal_id)
            ->exists();
        if (!$isValid) {
            return response()->json(['success' => false, 'message' => 'Soal tidak terdaftar dalam ujian ini.'], 422);
        }

        // Cek apakah ujian masih berlangsung
        $jadwal = Jadwal::find($jadwalId);
        $endTime = Carbon::parse($jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->jam_mulai)
            ->addSeconds($this->durationToSeconds($jadwal->durasi));
        if (now()->gt($endTime)) {
            return response()->json(['success' => false, 'message' => 'Waktu ujian telah habis.'], 422);
        }

        // Simpan atau update progres
        $progres = UjianProgres::updateOrCreate(
            [
                'user_id'            => $user->id,
                'mapel_id'           => $request->mapel_id,
                'jadwal_id'          => $jadwalId,
                'bank_pertanyaan_id' => $request->soal_id,
            ],
            [
                'bank_jawaban_id' => $request->jawaban_id,
                'is_ragu'         => $request->is_ragu,
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Catat pelanggaran (keluar halaman) (AJAX).
     */
    public function pelanggaran(Request $request)
    {
        $user = Auth::user();
        $jadwalId = Session::get('ujian_jadwal_id');
        if (!$jadwalId) {
            return response()->json(['success' => false, 'message' => 'Sesi ujian tidak ditemukan.'], 422);
        }

        $pelanggaran = UjianPelanggaran::firstOrCreate(
            ['user_id' => $user->id, 'jadwal_id' => $jadwalId],
            ['jumlah_pelanggaran' => 0]
        );

        $pelanggaran->increment('jumlah_pelanggaran');
        $total = $pelanggaran->refresh()->jumlah_pelanggaran;

        $max = config('exam.max_pelanggaran', 3);
        $blocked = false;
        if ($total >= $max) {
            // Blokir user
            $user->status = 'diblokir';
            $user->save();
            $blocked = true;
        }

        return response()->json([
            'success' => true,
            'total'   => $total,
            'blocked' => $blocked,
        ]);
    }

    /**
     * Blokir user dan logout (AJAX).
     */
    public function blokir(Request $request)
    {
        $user = Auth::user();
        $user->status = 'diblokir';
        $user->save();

        // Hapus sesi ujian
        Session::forget('ujian_jadwal_id');

        return response()->json(['success' => true]);
    }

    /**
     * Selesaikan ujian (POST).
     */
    public function selesai(Jadwal $jadwal)
    {
        $user = Auth::user();

        // Verifikasi sesi
        if (Session::get('ujian_jadwal_id') != $jadwal->id) {
            return redirect()->route('dashboard')->with('error', 'Akses tidak sah.');
        }

        // Update partisipasi
        $partisipasi = UjianSiswa::where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->first();

        if ($partisipasi) {
            $partisipasi->status = 'selesai';
            $partisipasi->selesai_ujian = now();
            $partisipasi->save();
        }

        // Hapus sesi
        Session::forget('ujian_jadwal_id');

        return redirect()->route('dashboard')->with('success', 'Ujian selesai. Terima kasih!');
    }

    // Helper: konversi durasi format HH:MM:SS ke detik
    private function durationToSeconds($durasi)
    {
        $parts = explode(':', $durasi);
        $jam = (int)($parts[0] ?? 0);
        $menit = (int)($parts[1] ?? 0);
        $detik = (int)($parts[2] ?? 0);
        return $jam * 3600 + $menit * 60 + $detik;
    }
}
