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

        // 2. Validasi Session & Token Realtime
        $sessionUser  = (int) session('akses_ujian_' . $jadwal->id);
        $sessionToken = trim((string) session('akses_ujian_token_' . $jadwal->id));
        $dbToken      = trim((string) $jadwal->token);

        if ($sessionUser !== (int) $user->id || strtoupper($sessionToken) !== strtoupper($dbToken)) {
            return redirect()->route('home')->with('error', 'Token ujian telah diperbarui atau sesi habis. Silakan masukkan token terbaru.');
        }

        $mapel = $jadwal->mapel;
        $sekarang = Carbon::now();

        // 3. Logika Partisipasi Siswa (ujian_siswa)
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

        // 4. Hitung Waktu Selesai & Sisa Waktu
        $durasiStr = (string) ($jadwal->durasi instanceof Carbon ? $jadwal->durasi->toTimeString() : $jadwal->durasi);
        $durasiCarbon = Carbon::createFromTimeString($durasiStr);
        $jam   = $durasiCarbon->hour;
        $menit = $durasiCarbon->minute;
        $detik = $durasiCarbon->second;

        $waktuSelesai = $waktuMulai->copy()
            ->addHours($jam)
            ->addMinutes($menit)
            ->addSeconds($detik);

        $timeLeft = (int) floor($sekarang->diffInSeconds($waktuSelesai, false));

        if ($timeLeft <= 0) {
            return redirect()->route('home')->with('error', 'Waktu ujian telah habis.');
        }

        // 5. Ambil Soal dari Pivot
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

        // 8. Ambil Pengaturan dari Tabel Setting
        $setting = DB::table('setting')->first();

        // max_tombol_selesai di database Anda defaultnya 300 (dalam detik)
        $maxTombolSelesaiDetik = (int) ($setting->max_tombol_selesai ?? 300);
        $settingTombolSelesai  = $maxTombolSelesaiDetik > 0 ? $maxTombolSelesaiDetik : false;

        $settingAntiNyontek    = (bool) ($setting->anti_nyontek ?? true);
        $settingMaxPelanggaran = (int) ($setting->max_pelanggaran ?? 5);

        return view('ujian.index', [
            'jadwal'                => $jadwal,
            'mapel'                 => $mapel,
            'listSoal'              => $listSoal,
            'timeLeft'              => (int) $timeLeft,
            'settingTombolSelesai'  => $settingTombolSelesai,
            'settingAntiNyontek'    => $settingAntiNyontek,
            'settingMaxPelanggaran' => $settingMaxPelanggaran,
            'pelanggaran'           => (int) $pelanggaran,
        ]);
    }

    // Endpoint AJAX: POST /ujian/pelanggaran
    public function simpan(Request $request)
    {
        try {
            $request->validate([
                'jadwal_id'  => 'required|exists:jadwal,id',
                'soal_id'    => 'required|exists:bank_pertanyaan,id',
                'jawaban_id' => 'nullable|exists:bank_jawaban,id',
                'is_ragu'    => 'required|boolean',
            ]);

            $user = Auth::user();

            if ($user->status === 'diblokir') {
                return response()->json(['error' => 'Akun Anda ditangguhkan.'], 403);
            }

            $jadwal = Jadwal::findOrFail($request->jadwal_id);

            // Validasi sisa waktu berdasarkan waktu mulai individu siswa di tabel ujian_siswa
            $partisipasi = DB::table('ujian_siswa')
                ->where('user_id', $user->id)
                ->where('jadwal_id', $jadwal->id)
                ->first();

            if (!$partisipasi || $partisipasi->status === 'selesai') {
                return response()->json(['error' => 'Ujian sudah diselesaikan atau tidak valid.'], 403);
            }

            $waktuMulai = Carbon::parse($partisipasi->mulai_ujian);
            $durasiStr = (string) ($jadwal->durasi instanceof Carbon ? $jadwal->durasi->toTimeString() : $jadwal->durasi);
            $durasiCarbon = Carbon::createFromTimeString($durasiStr);

            $waktuSelesai = $waktuMulai->copy()
                ->addHours($durasiCarbon->hour)
                ->addMinutes($durasiCarbon->minute)
                ->addSeconds($durasiCarbon->second);

            if (Carbon::now()->greaterThanOrEqualTo($waktuSelesai)) {
                return response()->json(['error' => 'Waktu ujian telah habis.'], 403);
            }

            // Simpan / update progres jawaban siswa
            ProgresSiswa::updateOrCreate(
                [
                    'user_id'            => $user->id,
                    'jadwal_id'          => $jadwal->id,
                    'bank_pertanyaan_id' => $request->soal_id,
                ],
                [
                    'bank_jawaban_id'    => $request->jawaban_id,
                    'is_ragu'            => (bool) $request->is_ragu,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error simpan jawaban: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan jawaban: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 2. Catat pelanggaran siswa (Sinkron dengan handleViolation di AlpineJS)
     */
    public function pelanggaran(Request $request)
    {
        try {
            $request->validate([
                'jadwal_id' => 'required|exists:jadwal,id',
            ]);

            $user = Auth::user();

            // Ambil batas maksimal pelanggaran dari tabel setting global
            $setting = DB::table('setting')->first();
            $maxPelanggaran = (int) ($setting->max_pelanggaran ?? 5);

            $ujianSiswa = UjianSiswa::where('user_id', $user->id)
                ->where('jadwal_id', $request->jadwal_id)
                ->first();

            if (!$ujianSiswa) {
                return response()->json(['error' => 'Data sesi ujian siswa tidak ditemukan.'], 404);
            }

            $ujianSiswa->increment('pelanggaran');
            $total = (int) $ujianSiswa->pelanggaran;
            $blocked = $total >= $maxPelanggaran;

            if ($blocked) {
                User::where('id', $user->id)->update(['status' => 'diblokir']);
            }

            return response()->json([
                'success' => true,
                'total'   => $total,
                'blocked' => $blocked,
                'max'     => $maxPelanggaran,
            ]);
        } catch (\Exception $e) {
            Log::error('Error catat pelanggaran: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * 3. Blokir user secara permanen (Sinkron dengan blokirUser di AlpineJS)
     */
    public function blokir(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            User::where('id', $user->id)->update(['status' => 'diblokir']);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'success' => true,
            'message' => 'Akun telah diblokir karena melampaui batas pelanggaran ujian.'
        ]);
    }

    /**
     * 4. Selesaikan ujian (Sinkron dengan submitUjian / form-selesai)
     */
    public function selesai(Request $request, Jadwal $jadwal)
    {
        $user = Auth::user();

        if ($jadwal) {
            UjianSiswa::where('user_id', $user->id)
                ->where('jadwal_id', $jadwal->id)
                ->update([
                    'status'        => 'selesai',
                    'selesai_ujian' => Carbon::now(),
                ]);

            // Hapus sesi akses token ujian
            session()->forget('akses_ujian_' . $jadwal->id);
            session()->forget('akses_ujian_token_' . $jadwal->id);
        }

        return redirect()->route('home')->with('success', 'Ujian telah berhasil diselesaikan.');
    }
}
