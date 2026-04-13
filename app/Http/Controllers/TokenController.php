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

        if ($mapel) {
            $lastUpdate = $mapel->updated_at->timestamp;
            $nextUpdate = $lastUpdate + 300;
            $secondsRemaining = max(0, $nextUpdate - time());

            // Logika Auto-Update jika waktu habis (khusus untuk tampilan)
            if ($secondsRemaining <= 0) {
                $token = strtoupper(\Illuminate\Support\Str::random(6));
                $mapel->update(['token' => $token, 'updated_at' => now()]);
                $secondsRemaining = 300;
            }
        }

        // Pemisahan menggunakan Gate
        if (Gate::allows('admin')) {
            $mapels = Mapel::all();
            return view('token', compact('mapels', 'token', 'secondsRemaining'));
        }

        if (Gate::allows('pengawas')) {
            return view('token_mobile', compact('token', 'secondsRemaining'));
        }

        return abort(403, 'Akses Ditolak');
    }

    public function refreshToken(Request $request)
    {
        if ($request->header('X-Api-Key') !== 'aja_kepo_ya') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $newToken = strtoupper(Str::random(6));

        Mapel::query()->update(['token' => $newToken]);

        return response()->json([
            'status' => 'success',
            'new_token' => $newToken
        ]);
    }
}
