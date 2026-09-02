<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use App\Models\Tingkat;
use App\Models\Kompetensi_keahlian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use Carbon\Carbon;

class MapelController extends Controller
{
    public function index()
    {
        $mapels = Mapel::with(['tingkat', 'kompetensiKeahlian'])
            ->withCount('bankPertanyaan')
            ->get();
        $tingkats = Tingkat::all();
        $kompetensis = Kompetensi_keahlian::all();

        return view('mapel.index', compact('mapels', 'tingkats', 'kompetensis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|unique:mapel',
            'nama_mapel' => 'required',
            'tingkat_id' => 'required|exists:tingkat,id',
            'kompetensi_keahlian_id' => 'nullable|exists:kompetensi_keahlian,id',
        ]);

        Mapel::create($request->all());
        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil ditambahkan.');
    }

    public function edit(Mapel $mapel)
    {
        $tingkats = Tingkat::all();
        $kompetensis = Kompetensi_keahlian::all();
        return view('mapel.edit', compact('mapel', 'tingkats', 'kompetensis'));
    }

    public function update(Request $request, Mapel $mapel)
    {
        $request->validate([
            'kode_mapel' => 'required|unique:mapel,kode_mapel,' . $mapel->id,
            'nama_mapel' => 'required',
            'tingkat_id' => 'required|exists:tingkat,id',
            'kompetensi_keahlian_id' => 'nullable|exists:kompetensi_keahlian,id',
        ]);

        $mapel->update($request->all());
        return redirect()->route('mapel.index')->with('success', 'Mapel diperbarui.');
    }

    public function destroy(Mapel $mapel)
    {
        $mapel->delete();
        return redirect()->route('mapel.index')->with('success', 'Mapel dihapus.');
    }

}
