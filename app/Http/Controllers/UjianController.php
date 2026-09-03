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
use Illuminate\Support\Facades\Storage;

class UjianController extends Controller
{
    /**
     * Tampilkan halaman ujian.
     */
    public function showExam(Jadwal $jadwal)
    {
        $user = Auth::user();

        // 1. Validasi Status Blokir Akun
        if ($user->status === 'diblokir') {
            return redirect()->route('home')->with('error', 'Akun Anda ditangguhkan.');
        }

        // 2. Cek Akses Token Ujian
        if (session('akses_ujian_' . $jadwal->id) !== $user->id) {
            return redirect()->route('home')->with('error', 'Akses ilegal. Silakan masuk melalui dashboard.');
        }

        $mapel = $jadwal->mapel;
        $sekarang = Carbon::now();

        // 3. Logika Partisipasi Siswa (Sama persis dengan cara lama, disesuaikan ke tabel ujian_siswa)
        $partisipasi = DB::table('ujian_siswa')
            ->where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->first();

        if (!$partisipasi) {
            DB::table('ujian_siswa')->insert([
                'user_id'     => $user->id,
                'jadwal_id'   => $jadwal->id,
                'status'      => 'sedang mengerjakan',
                'mulai_ujian' => $sekarang,
                'pelanggaran' => 0,
                'created_at'  => $sekarang,
                'updated_at'  => $sekarang,
            ]);
            $waktuMulai = $sekarang;
            $pelanggaran = 0;
        } else {
            $waktuMulai = Carbon::parse($partisipasi->mulai_ujian);
            $pelanggaran = $partisipasi->pelanggaran;
        }

        $durasiStr = (string) ($jadwal->durasi instanceof \Carbon\Carbon ? $jadwal->durasi->toTimeString() : $jadwal->durasi);

        // Parse jam, menit, detik secara aman
        $durasiCarbon = \Carbon\Carbon::createFromTimeString($durasiStr);
        $jam   = $durasiCarbon->hour;
        $menit = $durasiCarbon->minute;
        $detik = $durasiCarbon->second;

        // Tambahkan waktu ke waktu mulai
        $waktuSelesai = $waktuMulai->copy()
            ->addHours($jam)
            ->addMinutes($menit)
            ->addSeconds($detik);

        $timeLeft = (int) floor($sekarang->diffInSeconds($waktuSelesai, false));

        // Pengaman: Jika waktu habis langsung redirect ke home
        if ($timeLeft <= 0) {
            return redirect()->route('home')->with('error', 'Waktu ujian telah habis.');
        }

        // 5. Ambil Bank Soal dari Tabel Pivot (soal_bank_pertanyaan)
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

        // 6. Progres Jawaban Siswa
        $progres = DB::table('progres_siswa')
            ->where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->get()
            ->keyBy('bank_pertanyaan_id');

        // 7. Format Soal & Asset Storage R2
        $listSoal = $bankPertanyaan->map(function ($s, $index) use ($progres) {
            $p = $progres->get($s->id);
            return [
                'id'               => $s->id,
                'nomor'            => $index + 1,
                'pertanyaan'       => $s->pertanyaan,
                'gambar_soal'      => $s->gambar_soal ? Storage::disk('r2')->url($s->gambar_soal) : null,
                'jawaban_terpilih' => $p ? $p->bank_jawaban_id : null,
                'is_ragu'          => $p ? (bool) $p->is_ragu : false,
                'pilihan'          => $s->jawaban->sortBy('urutan')->values()->map(function ($j, $i) {
                    return [
                        'db_id'  => $j->id,
                        'label'  => chr(65 + $i),
                        'teks'   => $j->teks_jawaban,
                        'gambar' => $j->gambar_jawaban ? Storage::disk('r2')->url($j->gambar_jawaban) : null,
                    ];
                }),
            ];
        });

        // 8. Pengaturan Ujian
        $settingTombolSelesai = $jadwal->setting_tombol_selesai ?? 0;
        $settingTombolSelesaiDetik = $settingTombolSelesai * 60;

        return view('ujian.index', [
            'jadwal'                => $jadwal,
            'mapel'                 => $mapel,
            'listSoal'              => $listSoal,
            'timeLeft'              => (int) $timeLeft,
            'settingTombolSelesai'  => $settingTombolSelesaiDetik,
            'settingAntiNyontek'    => $jadwal->setting_anti_nyontek ?? false,
            'settingMaxPelanggaran' => $jadwal->setting_max_pelanggaran ?? 3,
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
