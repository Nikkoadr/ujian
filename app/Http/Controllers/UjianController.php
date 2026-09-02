<?php

namespace App\Http\Controllers;

use App\Models\BankPertanyaan;
use App\Models\BankJawaban;
use App\Models\Jadwal;
use App\Models\Mapel;
use App\Models\Soal;
use App\Models\SoalBankPertanyaan;
use App\Models\UjianPelanggaran;
use App\Models\UjianPeserta;
use App\Models\UjianProgres;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UjianController extends Controller
{
    /**
     * Dashboard siswa – menampilkan daftar ujian aktif.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $kelas = $user->kelas; // pastikan relasi ada di model User

        // Ambil semua jadwal yang aktif dan sesuai dengan tingkat & kompetensi siswa
        $daftarUjian = Jadwal::with(['mapel', 'partisipasi' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
            ->where('status', 'aktif')
            ->whereHas('mapel', function ($query) use ($kelas) {
                $query->where('tingkat_id', $kelas->tingkat_id)
                    ->where(function ($q) use ($kelas) {
                        $q->whereNull('kompetensi_keahlian_id')
                            ->orWhere('kompetensi_keahlian_id', $kelas->kompetensi_keahlian_id);
                    });
            })
            ->orderBy('tanggal_ujian')
            ->orderBy('jam_mulai')
            ->get();

        return view('dashboard', compact('user', 'kelas', 'daftarUjian'));
    }

    /**
     * Validasi token dan mulai sesi ujian.
     */
    public function validasi(Request $request)
    {
        $request->validate([
            'ujian_id' => 'required|exists:jadwal,id',
            'token'    => 'required|string|size:6',
        ]);

        $jadwal = Jadwal::with('mapel')->find($request->ujian_id);
        $user   = Auth::user();

        // Cek token
        if ($jadwal->token !== $request->token) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid.'], 422);
        }

        // Cek apakah user berhak mengikuti ujian ini
        $kelas = $user->kelas;
        $mapel = $jadwal->mapel;
        if (
            $mapel->tingkat_id != $kelas->tingkat_id ||
            ($mapel->kompetensi_keahlian_id && $mapel->kompetensi_keahlian_id != $kelas->kompetensi_keahlian_id)
        ) {
            return response()->json(['success' => false, 'message' => 'Anda tidak diperbolehkan mengikuti ujian ini.'], 403);
        }

        // Cek apakah ujian sudah lewat waktu
        $endTime = Carbon::parse($jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->jam_mulai)
            ->addSeconds($this->durationToSeconds($jadwal->durasi));
        if (now()->gt($endTime)) {
            return response()->json(['success' => false, 'message' => 'Waktu ujian telah habis.'], 422);
        }

        // Cek status user (tidak diblokir)
        if ($user->status === 'diblokir') {
            return response()->json(['success' => false, 'message' => 'Akun Anda diblokir.'], 403);
        }

        // Buat atau ambil data partisipasi
        $partisipasi = UjianPeserta::firstOrCreate(
            ['user_id' => $user->id, 'jadwal_id' => $jadwal->id],
            ['status' => 'sedang mengerjakan', 'mulai_ujian' => now()]
        );

        // Jika sudah selesai, tidak boleh masuk lagi
        if ($partisipasi->status === 'selesai') {
            return response()->json(['success' => false, 'message' => 'Ujian ini sudah Anda selesaikan.'], 422);
        }

        // Simpan jadwal_id di session untuk keperluan penyimpanan jawaban
        Session::put('ujian_jadwal_id', $jadwal->id);

        return response()->json([
            'success' => true,
            'redirect' => route('ujian.show', $jadwal->id)
        ]);
    }

    /**
     * Tampilkan halaman ujian dengan daftar soal.
     */
    public function showExam(Jadwal $jadwal)
    {
        $user = Auth::user();

        // Pastikan session sesuai (atau user berhak)
        if (Session::get('ujian_jadwal_id') != $jadwal->id) {
            return redirect()->route('dashboard')->with('error', 'Akses tidak sah.');
        }

        // Cek partisipasi
        $partisipasi = UjianPeserta::where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->first();

        if (!$partisipasi || $partisipasi->status === 'selesai') {
            return redirect()->route('dashboard')->with('error', 'Ujian sudah selesai.');
        }

        // Ambil mapel
        $mapel = $jadwal->mapel;

        // Ambil soal (bank_pertanyaan) yang terhubung dengan jadwal ini
        $soal = Soal::where('jadwal_id', $jadwal->id)->first();
        if (!$soal) {
            return redirect()->route('dashboard')->with('error', 'Soal belum tersedia.');
        }

        // Ambil bank_pertanyaan melalui pivot
        $bankPertanyaanIds = SoalBankPertanyaan::where('soal_id', $soal->id)
            ->pluck('bank_pertanyaan_id');
        $bankPertanyaan = BankPertanyaan::with(['jawaban' => function ($q) {
            $q->orderBy('urutan');
        }])->whereIn('id', $bankPertanyaanIds)
            ->orderBy('id') // urutan sesuai id (bisa disesuaikan)
            ->get();

        // Ambil progres user untuk soal-soal ini
        $progres = UjianProgres::where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->whereIn('bank_pertanyaan_id', $bankPertanyaanIds)
            ->get()
            ->keyBy('bank_pertanyaan_id');

        // Bentuk data soal sesuai yang diharapkan view
        $listSoal = [];
        foreach ($bankPertanyaan as $index => $bp) {
            $pilihan = [];
            foreach ($bp->jawaban as $jwb) {
                $pilihan[] = [
                    'db_id' => $jwb->id,
                    'label' => chr(65 + $jwb->urutan), // A, B, C, D, ...
                    'teks'  => $jwb->teks_jawaban ?? '',
                ];
            }

            $jawabanTerpilih = null;
            $isRagu = false;
            if ($progres->has($bp->id)) {
                $p = $progres->get($bp->id);
                $jawabanTerpilih = $p->bank_jawaban_id;
                $isRagu = $p->is_ragu;
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

        // Hitung sisa waktu (dalam detik)
        $endTime = Carbon::parse($jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->jam_mulai)
            ->addSeconds($this->durationToSeconds($jadwal->durasi));
        $timeLeft = max(0, now()->diffInSeconds($endTime, false));

        // Ambil pengaturan (contoh dari config atau database)
        $settingTombolSelesai = config('exam.tombol_selesai', 300); // 5 menit sebelum habis
        $settingAntiNyontek   = config('exam.anti_nyontek', true);
        $settingMaxPelanggaran = config('exam.max_pelanggaran', 3);

        // Ambil jumlah pelanggaran saat ini
        $pelanggaran = UjianPelanggaran::where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->first();
        $currentPelanggaran = $pelanggaran ? $pelanggaran->jumlah_pelanggaran : 0;

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
        $partisipasi = UjianPeserta::where('user_id', $user->id)
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
