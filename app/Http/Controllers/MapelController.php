<?php

namespace App\Http\Controllers;

use App\Models\Kompetensi_keahlian;
use App\Models\Mapel;
use App\Models\Tingkat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Imports\MapelImport;
use Maatwebsite\Excel\Facades\Excel;

class MapelController extends Controller
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
        $tingkat = Tingkat::all();
        $kompetensi_keahlian = Kompetensi_keahlian::all();

        $mapels = Mapel::withCount('soals')
            ->orderBy('tanggal_ujian', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return view('mapel.index', compact('mapels', 'tingkat', 'kompetensi_keahlian'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_ujian' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kode_mapel' => 'required|unique:mapel,kode_mapel',
            'nama_mapel' => 'required|string|max:255',
            'durasi' => 'required',
            'tingkat_id' => 'required|exists:tingkat,id',
            'kompetensi_keahlian_id' => 'required|exists:kompetensi_keahlian,id',

        ]);

        Mapel::create([
            'tanggal_ujian' => $request->tanggal_ujian,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'durasi' => $request->durasi,
            'token' => strtoupper(Str::random(6)),
            'status' => 'aktif',
            'tingkat_id' => $request->tingkat_id,
            'kompetensi_keahlian_id' => $request->kompetensi_keahlian_id
        ]);

        return redirect()->back()->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $tingkat = Tingkat::all();
        $kompetensi_keahlian = Kompetensi_keahlian::all();
        $mapel = Mapel::findOrFail($id);
        return view('mapel.edit', compact('mapel', 'tingkat', 'kompetensi_keahlian'));
    }

    public function update(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $request->validate([
            'tanggal_ujian' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kode_mapel' => 'required|unique:mapel,kode_mapel,' . $mapel->id,
            'nama_mapel' => 'required|string|max:255',
            'durasi' => 'required',
            'tingkat_id' => 'required|exists:tingkat,id',
            'kompetensi_keahlian_id' => 'required|exists:kompetensi_keahlian,id',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $mapel->update([
            'tanggal_ujian' => $request->tanggal_ujian,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'durasi' => $request->durasi,
            'tingkat_id' => $request->tingkat_id,
            'kompetensi_keahlian_id' => $request->kompetensi_keahlian_id,
            'status' => $request->status
        ]);

        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $mapel = Mapel::findOrFail($id);
        $mapel->delete();

        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }

    public function validasiToken(Request $request)
    {
        $request->validate([
            'ujian_id' => 'required|exists:mapel,id',
            'token' => 'required'
        ]);

        $ujian = Mapel::find($request->ujian_id);

        if ($ujian->token === strtoupper($request->token)) {
            session(['akses_ujian_' . $ujian->id => Auth::id()]);

            return response()->json([
                'success' => true,
                'message' => 'Token Valid!',
                'redirect' => route('ujian.mulai', $ujian->id)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Token salah atau sudah kadaluwarsa.'
        ], 422);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MapelImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data Mata Pelajaran Berhasil Diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal impor! Pastikan format kolom benar. Error: ' . $e->getMessage());
        }
    }
}
