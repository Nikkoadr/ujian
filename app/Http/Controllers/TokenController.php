<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mapel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class TokenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('refreshToken');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $mapel = Mapel::first();
        $token = $mapel ? $mapel->token : '------';

        $secondsRemaining = 0;
        $isStale = false;

        if ($mapel) {
            $lastUpdate = $mapel->updated_at->timestamp;
            $nextUpdate = $lastUpdate + 300; // Interval 5 menit
            $now = time();

            // Gunakan max(0, ...) agar tidak mengirim angka negatif ke JS
            $secondsRemaining = max(0, $nextUpdate - $now);

            // Jika sisa waktu sudah 0, berarti sistem menunggu Cron Job bekerja
            if ($secondsRemaining <= 0) {
                $isStale = true;
            }
        }

        // Pemisahan View berdasarkan Role
        if (Gate::allows('admin')) {
            $mapels = Mapel::all();
            return view('token', compact('mapels', 'token', 'secondsRemaining', 'isStale'));
        }

        if (Gate::allows('pengawas')) {
            return view('token_mobile', compact('token', 'secondsRemaining', 'isStale'));
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
        Mapel::query()->update([
            'token' => $newToken,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'new_token' => $newToken,
            'expiry' => 300 // Detik
        ]);
    }
}
