<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use App\Models\Soal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UjianPartisipasi;
use App\Models\Setting;

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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($id)
    {
        $setting = Setting::first();
        $setting_tombol_selesai = $setting->max_tombol_selesai ?? 300;
        $setting_anti_nyontek = $setting->anti_nyontek ?? true;
        $setting_max_pelanggaran = $setting->max_pelanggaran ?? 5;
        $user = Auth::user();

        if ($user->status === 'diblokir') {
            return redirect()->route('home')->with('error', 'Akun Anda ditangguhkan.');
        }
        if (session("akses_ujian_{$id}") !== $user->id) {
            return redirect()->route('home')->with('error', 'Akses ilegal. Silakan masuk melalui dashboard.');
        }

        $mapel = Mapel::findOrFail($id);
        $user = Auth::user();
        $sekarang = Carbon::now();

        $partisipasi = DB::table('ujian_partisipasi')
            ->where('user_id', $user->id)
            ->where('mapel_id', $id)
            ->first();

        if (!$partisipasi) {
            DB::table('ujian_partisipasi')->insert([
                'user_id' => $user->id,
                'mapel_id' => $id,
                'mulai_ujian' => $sekarang,
                'pelanggaran' => 0,
                'created_at' => $sekarang,
                'updated_at' => $sekarang
            ]);
            $waktuMulai = $sekarang;
        } else {
            $waktuMulai = Carbon::parse($partisipasi->mulai_ujian);
        }

        $durasiParts = explode(':', $mapel->durasi);
        $totalDetikUjian = ($durasiParts[0] * 3600) + ($durasiParts[1] * 60) + ($durasiParts[2] ?? 0);
        $waktuSelesai = $waktuMulai->copy()->addSeconds($totalDetikUjian);
        $timeLeft = floor($sekarang->diffInSeconds($waktuSelesai, false));

        if ($timeLeft <= 0) {
            return redirect()->route('home')->with('error', 'Waktu ujian telah habis.');
        }

        $progres = DB::table('ujian_progres')
            ->where('user_id', $user->id)
            ->where('mapel_id', $id)
            ->get()->keyBy('soal_id');

        $soal = Soal::with('jawaban')->where('mapel_id', $id)->get()
            ->map(function ($s, $index) use ($progres) {
                $p = $progres->get($s->id);
                return [
                    'id' => $s->id,
                    'nomor' => $index + 1,
                    'pertanyaan' => $s->pertanyaan,
                    'gambar_soal' => $s->gambar_soal ? asset('storage/soal/' . $s->gambar_soal) : null,
                    'jawaban_terpilih' => $p ? $p->jawaban_id : null,
                    'is_ragu' => $p ? (bool)$p->is_ragu : false,
                    'pilihan' => $s->jawaban->map(fn($j, $i) => [
                        'db_id' => $j->id,
                        'label' => chr(65 + $i),
                        'teks' => $j->teks_jawaban,
                        'gambar' => $j->gambar_jawaban ? asset('storage/jawaban/' . $j->gambar_jawaban) : null
                    ])
                ];
            });
        return view('ujian.index', compact('mapel', 'soal', 'timeLeft', 'setting_tombol_selesai', 'setting_anti_nyontek', 'setting_max_pelanggaran'));
    }

    public function simpan(Request $request)
    {
        try {
            // Data sekarang dikirim langsung sebagai JSON, Laravel otomatis memetakan ke $request
            // Kita gunakan $request->all() atau langsung panggil key-nya.

            // 1. Validasi sederhana
            if (!$request->has('soal_id')) {
                return response()->json(['success' => false, 'message' => 'Data tidak lengkap'], 400);
            }

            // 2. Eksekusi ke database menggunakan data langsung dari $request
            DB::table('ujian_progres')->updateOrInsert(
                [
                    'user_id'  => Auth::id(),
                    'mapel_id' => $request->mapel_id,
                    'soal_id'  => $request->soal_id
                ],
                [
                    'jawaban_id' => $request->jawaban_id,
                    'is_ragu'    => $request->is_ragu ?? false,
                    'updated_at' => now()
                ]
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function blokirSiswa()
    {
        $user = Auth::user();

        DB::table('users')->where('id', $user->id)->update([
            'status' => 'diblokir',
        ]);

        return response()->json(['status' => 'success']);
    }

    public function selesai($id)
    {
        $user = Auth::user();

        DB::table('ujian_partisipasi')
            ->where('user_id', $user->id)
            ->where('mapel_id', $id)
            ->update([
                'status' => 'selesai',
                'updated_at' => now()
            ]);

        return redirect()->route('home')->with('success', 'Ujian berhasil diselesaikan.');
    }

    public function pelanggaran(Request $request)
    {
        $setting = Setting::first();
        $setting_max_pelanggaran = $setting->max_pelanggaran ?? 5;

        $request->validate([
            'mapel_id' => 'required|integer'
        ]);

        $partisipasi = UjianPartisipasi::where('user_id', Auth::id())
            ->where('mapel_id', $request->mapel_id)
            ->first();

        if (!$partisipasi) {
            return response()->json(['message' => 'Sesi ujian tidak ditemukan'], 404);
        }

        $partisipasi->increment('pelanggaran');
        $partisipasi->refresh();

        $max_boleh = $setting_max_pelanggaran ?? 5;

        return response()->json([
            'total' => $partisipasi->pelanggaran,
            'blocked' => $partisipasi->pelanggaran >= $max_boleh
        ]);
    }
}
