<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\PeriodeUjian;
use App\Models\Tingkat;
use App\Models\Kompetensi_keahlian;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class JadwalUjianController extends Controller
{
    public function index()
    {
        // Ambil periode aktif
        $periodeAktif = PeriodeUjian::where('is_active', true)->first();

        // Jika tidak ada periode aktif, ambil semua (atau kosong)
        $ujians = Ujian::with(['periodeUjian', 'tingkat', 'kompetensiKeahlian'])
            ->when($periodeAktif, function ($query) use ($periodeAktif) {
                return $query->where('periode_ujian_id', $periodeAktif->id);
            })
            ->orderBy('tanggal_ujian', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $periodeUjian = PeriodeUjian::all(); // untuk dropdown di modal
        $tingkat = Tingkat::all();
        $kompetensiKeahlian = Kompetensi_keahlian::all();

        return view('jadwal_ujian.index', compact('ujians', 'periodeUjian', 'tingkat', 'kompetensiKeahlian', 'periodeAktif'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'periode_ujian_id' => 'required|exists:periode_ujian,id',
            'kode_ujian' => 'required|unique:ujian,kode_ujian',
            'nama_ujian' => 'required|string|max:255',
            'tanggal_ujian' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'durasi' => 'required',
            'tingkat_id' => 'required|exists:tingkat,id',
            'kompetensi_keahlian_id' => 'nullable|exists:kompetensi_keahlian,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Ujian::create([
            'periode_ujian_id' => $request->periode_ujian_id,
            'kode_ujian' => $request->kode_ujian,
            'nama_ujian' => $request->nama_ujian,
            'tanggal_ujian' => $request->tanggal_ujian,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'durasi' => $request->durasi,
            'tingkat_id' => $request->tingkat_id,
            'kompetensi_keahlian_id' => $request->kompetensi_keahlian_id,
            'status' => $request->status,
            'token' => strtoupper(Str::random(6)),
        ]);

        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $ujian = Ujian::findOrFail($id);
        $periodeUjian = PeriodeUjian::all();
        $tingkat = Tingkat::all();
        $kompetensiKeahlian = Kompetensi_keahlian::all();
        return view('jadwal_ujian.edit', compact('ujian', 'periodeUjian', 'tingkat', 'kompetensiKeahlian'));
    }

    public function update(Request $request, $id)
    {
        $ujian = Ujian::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'periode_ujian_id' => 'required|exists:periode_ujian,id',
            'kode_ujian' => 'required|unique:ujian,kode_ujian,' . $ujian->id,
            'nama_ujian' => 'required|string|max:255',
            'tanggal_ujian' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'durasi' => 'required',
            'tingkat_id' => 'required|exists:tingkat,id',
            'kompetensi_keahlian_id' => 'nullable|exists:kompetensi_keahlian,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $ujian->update([
            'periode_ujian_id' => $request->periode_ujian_id,
            'kode_ujian' => $request->kode_ujian,
            'nama_ujian' => $request->nama_ujian,
            'tanggal_ujian' => $request->tanggal_ujian,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'durasi' => $request->durasi,
            'tingkat_id' => $request->tingkat_id,
            'kompetensi_keahlian_id' => $request->kompetensi_keahlian_id,
            'status' => $request->status,
        ]);

        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $ujian = Ujian::findOrFail($id);
        $ujian->delete();
        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil dihapus.');
    }

    public function show($id)
    {
        $ujian = Ujian::with(['periodeUjian', 'tingkat', 'kompetensiKeahlian'])->findOrFail($id);
        return view('jadwal_ujian.show', compact('ujian'));
    }
}
