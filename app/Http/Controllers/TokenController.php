<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        if ($request->header('X-Api-Key') !== 'aja_kepo_ya') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $newToken = strtoupper(Str::random(6));
        $now = Carbon::now();

        Jadwal::where('status', 'aktif')
            ->whereDate('tanggal_ujian', $now->toDateString())
            ->whereTime('jam_mulai', '<=', $now->toTimeString())
            ->whereTime('jam_selesai', '>=', $now->toTimeString())
            ->update([
                'token' => $newToken,
                'updated_at' => now()
            ]);

        return response()->json([
            'status' => 'success',
            'new_token' => $newToken,
            'expiry' => 300
        ]);
    }

    public function validasiToken(Request $request)
    {
        $request->validate([
            'ujian_id' => 'required|exists:jadwal,id',
            'token'    => 'required|string|size:6'
        ]);

        $jadwal = Jadwal::with('mapel')->find($request->ujian_id);

        if (!$jadwal || $jadwal->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ujian tidak aktif.'
            ], 403);
        }

        $sekarang = Carbon::now();

        // Normalisasi format tanggal & jam agar tidak terjadi double date
        $tglStr = Carbon::parse($jadwal->tanggal_ujian)->format('Y-m-d');
        $jamMulaiStr = Carbon::parse($jadwal->jam_mulai)->format('H:i:s');
        $jamSelesaiStr = Carbon::parse($jadwal->jam_selesai)->format('H:i:s');

        $mulai   = Carbon::parse($tglStr . ' ' . $jamMulaiStr);
        $selesai = Carbon::parse($tglStr . ' ' . $jamSelesaiStr);

        // Cek apakah hari ini sesuai dengan tanggal ujian
        if (!$sekarang->isSameDay(Carbon::parse($tglStr))) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian tidak tersedia hari ini.'
            ], 403);
        }

        // Cek belum mulai
        if ($sekarang->lt($mulai)) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian belum dimulai.'
            ], 403);
        }

        // Cek sudah selesai
        if ($sekarang->gte($selesai)) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu ujian sudah berakhir.'
            ], 403);
        }

        // Validasi token
        if (strcasecmp($jadwal->token, $request->token) === 0) {
            session(['akses_ujian_' . $jadwal->id => Auth::id()]);

            // WAJIB: Paksa simpan sesi agar terbawa saat AJAX redirect
            session()->save();

            // Log untuk debugging
            Log::info('Token valid, redirect ke ujian', [
                'jadwal_id' => $jadwal->id,
                'user_id' => Auth::id(),
                'redirect_url' => route('ujian.mulai', ['jadwal_id' => $jadwal->id])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token Valid!',
                'redirect' => route('ujian.mulai', ['jadwal_id' => $jadwal->id])
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Token salah atau sudah kadaluwarsa.'
        ], 422);
    }
}
