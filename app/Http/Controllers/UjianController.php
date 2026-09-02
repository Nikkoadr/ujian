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

    public function showExam(Jadwal $jadwal)
    {
        $user = Auth::user();

        // 1. Validasi session akses dari validasi token
        if (Session::get('akses_ujian_' . $jadwal->id) !== $user->id) {
            return redirect()->route('home')->with('error', 'Akses tidak sah. Silakan masukkan token terlebih dahulu.');
        }

        // 2. Ambil / Buat sesi partisipasi
        $partisipasi = UjianSiswa::firstOrCreate(
            [
                'user_id'   => $user->id,
                'jadwal_id' => $jadwal->id,
            ],
            [
                'status'      => 'sedang mengerjakan',
                'mulai_ujian' => Carbon::now(),
                'pelanggaran' => 0,
            ]
        );

        // 3. Cek jika sudah selesai
        if ($partisipasi->status === 'selesai') {
            return redirect()->route('home')->with('error', 'Ujian sudah selesai dikerjakan.');
        }

        $mapel = $jadwal->mapel;

        // 4. Ambil paket soal
        $soal = Soal::where('jadwal_id', $jadwal->id)->first();
        if (!$soal) {
            return redirect()->route('home')->with('error', 'Soal belum tersedia untuk jadwal ini.');
        }

        // 5. Ambil butir pertanyaan & opsi jawaban
        $bankPertanyaanIds = SoalBankPertanyaan::where('soal_id', $soal->id)
            ->pluck('bank_pertanyaan_id');

        $bankPertanyaan = BankPertanyaan::with(['jawaban' => function ($q) {
            $q->orderBy('urutan');
        }])
            ->whereIn('id', $bankPertanyaanIds)
            ->orderBy('id')
            ->get();

        // 6. Ambil progres pengerjaan
        $progres = UjianProgres::where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->whereIn('bank_pertanyaan_id', $bankPertanyaanIds)
            ->get()
            ->keyBy('bank_pertanyaan_id');

        // 7. Format data untuk view
        $listSoal = [];
        foreach ($bankPertanyaan as $index => $bp) {
            $pilihan = [];
            foreach ($bp->jawaban as $jwb) {
                $pilihan[] = [
                    'db_id' => $jwb->id,
                    'label' => chr(65 + $jwb->urutan),
                    'teks'  => $jwb->teks_jawaban ?? '',
                ];
            }

            $jawabanTerpilih = null;
            $isRagu = false;
            if ($progres->has($bp->id)) {
                $p = $progres->get($bp->id);
                $jawabanTerpilih = $p->bank_jawaban_id;
                $isRagu = (bool) $p->is_ragu;
            }

            $listSoal[] = [
                'id'               => $bp->id,
                'nomor'            => $index + 1,
                'pertanyaan'       => $bp->pertanyaan,
                'gambar_soal'      => $bp->gambar_soal ? asset('storage/' . $bp->gambar_soal) : null,
                'pilihan'          => $pilihan,
                'jawaban_terpilih' => $jawabanTerpilih,
                'is_ragu'          => $isRagu,
            ];
        }

        // 8. Hitung sisa waktu
        $tglStr = Carbon::parse($jadwal->tanggal_ujian)->format('Y-m-d');
        $jamMulaiStr = Carbon::parse($jadwal->jam_mulai)->format('H:i:s');
        $endTime = Carbon::parse($tglStr . ' ' . $jamMulaiStr)->addSeconds($this->durationToSeconds($jadwal->durasi));
        $timeLeft = max(0, (int) now()->diffInSeconds($endTime, false));

        // Pengaturan ujian
        $settingTombolSelesai  = config('exam.tombol_selesai', 300);
        $settingAntiNyontek    = config('exam.anti_nyontek', true);
        $settingMaxPelanggaran = config('exam.max_pelanggaran', 3);
        $currentPelanggaran    = $partisipasi->pelanggaran ?? 0;

        return view('ujian', compact(
            'mapel',
            'timeLeft',
            'settingTombolSelesai',
            'settingAntiNyontek',
            'settingMaxPelanggaran',
            'listSoal',
            'currentPelanggaran',
            'jadwal'
        ));
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
