<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
     * @return Renderable
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
            'nama_sekolah' => 'nullable|string|max:255',
            'alamat_sekolah' => 'nullable|string|max:255',
            'nama_kepala_sekolah' => 'nullable|string|max:255',
            'nama_wakakurikulum' => 'nullable|string|max:255',
            'ttd_kepala_sekolah' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = [
            'max_pelanggaran' => $request->max_pelanggaran,

            // Menit -> Detik
            'max_tombol_selesai' => $request->max_tombol_selesai * 60,

            'anti_nyontek' => $request->has('anti_nyontek'),

            'nama_sekolah' => $request->nama_sekolah,
            'alamat_sekolah' => $request->alamat_sekolah,
            'nama_kepala_sekolah' => $request->nama_kepala_sekolah,
            'nama_wakakurikulum' => $request->nama_wakakurikulum,
        ];

        if ($request->hasFile('ttd_kepala_sekolah')) {
            $lama = Setting::where('id', 1)->value('ttd_kepala_sekolah');

            if ($lama) {
                Storage::disk('r2')->delete($lama);
            }

            $data['ttd_kepala_sekolah'] = $request->file('ttd_kepala_sekolah')->store('ttd', 'r2');
        }

        Setting::updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'Setting berhasil diperbarui');
    }
}
