<?php

namespace App\Http\Controllers;

use App\Models\BankPertanyaan;
use App\Models\Jadwal;
use App\Models\ProgresSiswa;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UjianController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan daftar soal untuk suatu mapel.
     *
     * @param  int  $mapel_id
     * @return Renderable
     */
    public function showExam(Jadwal $jadwal)
    {
        $user = Auth::user();

        // 1. Validasi Status Blokir Akun: kembalikan ke daftar ujian.
        // Halaman daftar sudah menampilkan banner blokir sendiri,
        // jadi tanpa notifikasi tambahan di sini.
        if ($user->status === 'diblokir') {
            return redirect()->route('home');
        }

        // 2. Validasi Session & Token Realtime
        $sessionUser = (int) session('akses_ujian_'.$jadwal->id);
        $sessionToken = trim((string) session('akses_ujian_token_'.$jadwal->id));
        $dbToken = trim((string) $jadwal->token);

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

        if (! $partisipasi) {
            DB::table('ujian_siswa')->insert([
                'user_id' => $user->id,
                'jadwal_id' => $jadwal->id,
                'status' => 'sedang mengerjakan',
                'mulai_ujian' => $sekarang,
                'pelanggaran' => 0,
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ]);
            $waktuMulai = $sekarang;
            $pelanggaran = 0;
        } elseif (! $partisipasi->mulai_ujian) {
            // Sisa reset lama (mulai_ujian null): anggap mulai baru sekarang
            // agar timer terisi, bukan error/lanjut tanpa waktu.
            DB::table('ujian_siswa')
                ->where('user_id', $user->id)
                ->where('jadwal_id', $jadwal->id)
                ->update([
                    'status' => 'sedang mengerjakan',
                    'mulai_ujian' => $sekarang,
                    'updated_at' => $sekarang,
                ]);
            $waktuMulai = $sekarang;
            $pelanggaran = (int) $partisipasi->pelanggaran;
        } else {
            $waktuMulai = Carbon::parse($partisipasi->mulai_ujian);
            $pelanggaran = $partisipasi->pelanggaran;
        }

        // 4. Hitung Waktu Selesai & Sisa Waktu
        $durasiStr = (string) ($jadwal->durasi instanceof Carbon ? $jadwal->durasi->toTimeString() : $jadwal->durasi);
        $durasiCarbon = Carbon::createFromTimeString($durasiStr);
        $jam = $durasiCarbon->hour;
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
                'id' => $s->id,
                'nomor' => $index + 1,
                'pertanyaan' => $s->pertanyaan,
                'gambar_soal' => $s->gambar_soal ? Storage::disk('r2')->url($s->gambar_soal) : null,
                'jawaban_terpilih' => $p ? $p->bank_jawaban_id : null,
                'is_ragu' => $p ? (bool) $p->is_ragu : false,
                'pilihan' => $s->jawaban->sortBy('urutan')->values()->map(function ($j, $i) {
                    return [
                        'db_id' => $j->id,
                        'label' => chr(65 + $i),
                        'teks' => $j->teks_jawaban,
                        'gambar' => $j->gambar_jawaban ? Storage::disk('r2')->url($j->gambar_jawaban) : null,
                    ];
                }),
            ];
        });

        $setting = DB::table('setting')->first();

        $settingAntiNyontek = (bool) ($setting->anti_nyontek ?? true);

        if ($settingAntiNyontek) {
            // Jika anti-contek AKTIF, terapkan batasan tombol & max pelanggaran
            $maxTombolSelesaiDetik = (int) ($setting->max_tombol_selesai ?? 300);
            $settingTombolSelesai = $maxTombolSelesaiDetik > 0 ? $maxTombolSelesaiDetik : false;
            $settingMaxPelanggaran = (int) ($setting->max_pelanggaran ?? 5);
        } else {
            // Jika anti-contek NONAKTIF, tiadakan semua batasan
            $settingTombolSelesai = false; // Tombol selesai langsung aktif bebas dipencet
            $settingMaxPelanggaran = 0;     // Batasan pelanggaran ditiadakan
        }

        return view('ujian.index', [
            'jadwal' => $jadwal,
            'mapel' => $mapel,
            'listSoal' => $listSoal,
            'timeLeft' => (int) $timeLeft,
            'settingTombolSelesai' => $settingTombolSelesai,
            'settingAntiNyontek' => $settingAntiNyontek,
            'settingMaxPelanggaran' => $settingMaxPelanggaran,
            'pelanggaran' => (int) $pelanggaran,
        ]);
    }

    public function simpan(Request $request)
    {
        try {
            $request->validate([
                'jadwal_id' => 'required|exists:jadwal,id',
                'soal_id' => 'required|exists:bank_pertanyaan,id',
                'jawaban_id' => 'nullable|exists:bank_jawaban,id',
                'is_ragu' => 'required|boolean',
            ]);

            $user = Auth::user();

            if ($user->status === 'diblokir') {
                return response()->json(['error' => 'Akun diblokir. Hubungi pengawas untuk membuka blokir.', 'blocked' => true], 403);
            }

            $jadwal = Jadwal::findOrFail($request->jadwal_id);

            // Validasi sesi siswa
            $partisipasi = DB::table('ujian_siswa')
                ->where('user_id', $user->id)
                ->where('jadwal_id', $jadwal->id)
                ->first();

            if (! $partisipasi || $partisipasi->status === 'selesai') {
                return response()->json(['error' => 'Ujian sudah diselesaikan atau tidak valid.'], 403);
            }

            // Validasi sisa waktu pengerjaan
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

            // Simpan / update progres ke tabel progres_siswa
            ProgresSiswa::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'jadwal_id' => $jadwal->id,
                    'bank_pertanyaan_id' => $request->soal_id,
                ],
                [
                    'bank_jawaban_id' => $request->jawaban_id,
                    'is_ragu' => (bool) $request->is_ragu,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error simpan jawaban: '.$e->getMessage());

            return response()->json(['error' => 'Gagal menyimpan jawaban: '.$e->getMessage()], 500);
        }
    }

    public function pelanggaran(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|integer',
        ]);

        $user = Auth::user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $setting = DB::table('setting')->first();
        $antiNyontek = (bool) ($setting->anti_nyontek ?? true);

        if (! $antiNyontek) {
            return response()->json([
                'total' => 0,
                'max' => 0,
                'blocked' => false,
            ]);
        }

        $maxBoleh = (int) ($setting->max_pelanggaran ?? 5);

        $partisipasi = DB::table('ujian_siswa')
            ->where('user_id', $user->id)
            ->where('jadwal_id', $request->jadwal_id)
            ->first();

        if (! $partisipasi) {
            return response()->json(['message' => 'Sesi ujian tidak ditemukan'], 404);
        }

        // Batas 0 / negatif = tanpa batasan (hindari blokir instan).
        if ($maxBoleh <= 0) {
            return response()->json([
                'total' => (int) $partisipasi->pelanggaran,
                'max' => $maxBoleh,
                'blocked' => false,
            ]);
        }

        // Dedup: abaikan laporan ganda dalam 15 detik (pasangan blur+visibilitychange,
        // retry jaringan, atau kembali-masuk yang cepat). Tanpa ini 1 kejadian jinak
        // seperti layar HP mati sesaat bisa terhitung 2x.
        $lockKey = "pelanggaran:{$user->id}:{$request->jadwal_id}";
        if (Cache::has($lockKey)) {
            return response()->json([
                'total' => (int) $partisipasi->pelanggaran,
                'max' => $maxBoleh,
                'blocked' => false,
            ]);
        }
        Cache::put($lockKey, true, 15);

        $total = (int) $partisipasi->pelanggaran + 1;
        $isBlocked = $total >= $maxBoleh;

        if ($isBlocked) {
            // Gunakan Transaction agar perubahan status user & reset nilai di ujian_siswa pasti terjadi bersamaan
            DB::transaction(function () use ($user, $request) {
                // 1. Ubah status user menjadi diblokir
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'status' => 'diblokir',
                        'updated_at' => now(),
                    ]);

                // 2. Reset counter pelanggaran kembali ke 0
                DB::table('ujian_siswa')
                    ->where('user_id', $user->id)
                    ->where('jadwal_id', $request->jadwal_id)
                    ->update([
                        'pelanggaran' => 0,
                        'updated_at' => now(),
                    ]);
            });

            // 3. Bersihkan sesi ujian
            session()->forget('akses_ujian_'.$request->jadwal_id);
            session()->forget('akses_ujian_token_'.$request->jadwal_id);

            return response()->json([
                'total' => $total,
                'max' => $maxBoleh,
                'blocked' => true,
            ]);
        }

        // Jika belum diblokir, tambahkan nilai pelanggaran
        DB::table('ujian_siswa')
            ->where('user_id', $user->id)
            ->where('jadwal_id', $request->jadwal_id)
            ->increment('pelanggaran', 1, ['updated_at' => now()]);

        return response()->json([
            'total' => $total,
            'max' => $maxBoleh,
            'blocked' => false,
        ]);
    }

    public function blokirSiswa(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            DB::transaction(function () use ($user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'status' => 'diblokir',
                        'updated_at' => now(),
                    ]);

                DB::table('ujian_siswa')
                    ->where('user_id', $user->id)
                    ->update([
                        'pelanggaran' => 0,
                        'updated_at' => now(),
                    ]);
            });

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['status' => 'success']);
    }

    public function selesai($id)
    {
        $user = Auth::user();

        $partisipasi = DB::table('ujian_siswa')
            ->where('user_id', $user->id)
            ->where('jadwal_id', $id)
            ->first();

        if (! $partisipasi) {
            return redirect()->route('home')
                ->with('error', 'Data ujian tidak ditemukan.');
        }

        if ($partisipasi->status !== 'selesai') {
            DB::table('ujian_siswa')
                ->where('user_id', $user->id)
                ->where('jadwal_id', $id)
                ->update([
                    'status' => 'selesai',
                    'selesai_ujian' => now(),
                    'updated_at' => now(),
                ]);
        }

        // Bersihkan sesi ujian
        session()->forget('akses_ujian_'.$id);
        session()->forget('akses_ujian_token_'.$id);

        return redirect()->route('home')
            ->with('success', 'Ujian berhasil diselesaikan.');
    }
}
