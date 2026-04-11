<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mapel;
use Illuminate\Support\Str;

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
        $mapel = Mapel::select('token', 'updated_at')->first();

        $token = $mapel ? $mapel->token : '------';
        if ($mapel) {
            $lastUpdate = $mapel->updated_at->timestamp;
            $nextUpdate = $lastUpdate + 300;
            $now = time();

            $secondsRemaining = $nextUpdate - $now;

            if ($secondsRemaining < 0) $secondsRemaining = 0;
        } else {
            $secondsRemaining = 300;
        }

        return view('token', compact('token', 'secondsRemaining'));
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
