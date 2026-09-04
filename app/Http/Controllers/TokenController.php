<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TokenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('refreshToken');
    }

    public function index()
    {
        $now = Carbon::now();

        // Cari jadwal ujian yang aktif berdasarkan tanggal dan jam sekarang
        $jadwal = Jadwal::with('mapel')
            ->where('status', 'aktif')
            ->whereDate('tanggal_ujian', $now->toDateString())
            ->whereTime('jam_mulai', '<=', $now->toTimeString())
            ->whereTime('jam_selesai', '>=', $now->toTimeString())
            ->first();

        $token = $jadwal ? $jadwal->token : '------';

        $secondsRemaining = 0;
        $isStale = false;

        if ($jadwal) {
            $lastUpdate = $jadwal->updated_at->timestamp;
            $nextUpdate = $lastUpdate + 300; // Interval 5 menit
            $currentTime = time();

            $secondsRemaining = max(0, $nextUpdate - $currentTime);

            if ($secondsRemaining <= 0) {
                $isStale = true;
            }
        }

        if (Gate::allows('admin')) {
            $jadwals = Jadwal::with('mapel')->get();
            return view('token', compact('jadwals', 'token', 'secondsRemaining', 'isStale', 'jadwal'));
        }

        if (Gate::allows('pengawas')) {
            return view('token_mobile', compact('token', 'secondsRemaining', 'isStale', 'jadwal'));
        }

        return abort(403, 'Akses Ditolak');
    }

    public function refreshToken(Request $request)
    {
        // Keamanan API Key
        if ($request->header('X-Api-Key') !== 'aja_kepo_ya') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $newToken = strtoupper(\Illuminate\Support\Str::random(6));

        // Update semua record token dan paksa update timestamp
        Jadwal::query()->update([
            'token' => $newToken,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'new_token' => $newToken,
            'expiry' => 300 // Detik
        ]);
    }

    public function validasiToken(Request $request)
    {
        $request->validate([
            'ujian_id' => 'required|exists:jadwal,id',
            'token'    => 'required|string|size:6'
        ]);

        $jadwal = Jadwal::find($request->ujian_id);

        if (!$jadwal || $jadwal->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ujian sedang tidak aktif.'
            ], 403);
        }

        $user = Auth::user();
        $sekarang = Carbon::now();

        // Normalisasi format tanggal dan jam
        $tglStr = Carbon::parse($jadwal->tanggal_ujian)->format('Y-m-d');
        $jamMulaiStr = Carbon::parse($jadwal->jam_mulai)->format('H:i:s');
        $jamSelesaiStr = Carbon::parse($jadwal->jam_selesai)->format('H:i:s');

        $mulai   = Carbon::parse($tglStr . ' ' . $jamMulaiStr);
        $selesai = Carbon::parse($tglStr . ' ' . $jamSelesaiStr);

        // Cek apakah siswa sudah pernah mulai ujian
        $partisipasi = DB::table('ujian_siswa')
            ->where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->first();

        // Validasi waktu hanya jika siswa BELUM pernah memulai ujian
        if (!$partisipasi) {
            // Cek tanggal pengerjaan
            if (!$sekarang->isSameDay(Carbon::parse($tglStr))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ujian tidak dijadwalkan untuk hari ini.'
                ], 403);
            }

            // Cek jam belum mulai
            if ($sekarang->lt($mulai)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ujian belum dimulai.'
                ], 403);
            }

            // Cek jam sudah selesai
            if ($sekarang->gte($selesai)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu ujian sudah berakhir.'
                ], 403);
            }
        }

        // Validasi kesesuaian token
        if (strtoupper(trim($jadwal->token)) === strtoupper(trim($request->token))) {
            // Set session akses dan string token aktif
            session([
                'akses_ujian_' . $jadwal->id       => (int) $user->id,
                'akses_ujian_token_' . $jadwal->id => trim($jadwal->token),
            ]);
            session()->save();

            return response()->json([
                'success'  => true,
                'message'  => 'Token Valid!',
                'redirect' => route('ujian.mulai', ['jadwal' => $jadwal->id]) // Pastikan nama route sesuai web.php
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Token salah atau sudah kadaluwarsa.'
        ], 422);
    }
}
