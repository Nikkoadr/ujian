<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
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
    public function index()
    {
        $setting = Setting::first();
        return view('setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'max_pelanggaran' => 'required|integer|min:1',
            'max_tombol_selesai' => 'required|integer|min:0',
        ]);

        Setting::updateOrCreate(
            ['id' => 1],
            [
                'max_pelanggaran' => $request->max_pelanggaran,
                'max_tombol_selesai' => $request->max_tombol_selesai,
                'anti_nyontek' => $request->has('anti_nyontek'),
            ]
        );

        return back()->with('success', 'Setting berhasil diperbarui');
    }
}
