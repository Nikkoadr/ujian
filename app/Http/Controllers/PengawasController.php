<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengawas;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PengawasController extends Controller
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
        // Mengambil Pengawas -> Guru -> User secara sekaligus
        $data_pengawas = Pengawas::with('guru.user')->latest()->get();

        // Mengambil daftar Guru yang BELUM menjadi pengawas (opsional, agar tidak duplikat)
        $guru_tersedia = Guru::with('user')
            ->whereDoesntHave('pengawas')
            ->get();

        return view('pengawas.index', compact('data_pengawas', 'guru_tersedia'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id|unique:pengawas,guru_id',
        ], [
            'guru_id.unique' => 'Guru tersebut sudah terdaftar sebagai pengawas.'
        ]);

        try {
            Pengawas::create(['guru_id' => $request->guru_id]);
            return redirect()->route('pengawas.index')->with('success', 'Pengawas berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan pengawas.');
        }
    }

    public function destroy($id)
    {
        try {
            Pengawas::findOrFail($id)->delete();
            return redirect()->route('pengawas.index')->with('success', 'Status pengawas berhasil dicabut.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }
}
