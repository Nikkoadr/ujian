<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Mapel;
use App\Models\PeriodeUjian;
use App\Models\Tingkat;
use App\Models\Kompetensi_keahlian;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class JadwalUjianController extends Controller
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

    /**
     * Menampilkan daftar jadwal ujian untuk periode aktif
     */
    public function index()
    {
        $periodeAktif = PeriodeUjian::where('is_active', true)->first();

        // Ambil mapel yang memiliki jadwal pada periode aktif
        $mapels = Mapel::with(['jadwal', 'tingkat', 'kompetensiKeahlian'])
            ->join('jadwal', 'mapel.id', '=', 'jadwal.mapel_id')
            ->when($periodeAktif, function ($query) use ($periodeAktif) {
                return $query->where('jadwal.periode_ujian_id', $periodeAktif->id);
            })
            ->orderBy('jadwal.tanggal_ujian', 'asc')
            ->orderBy('jadwal.jam_mulai', 'asc')
            ->select('mapel.*')
            // Tambahkan subquery untuk menghitung jumlah pertanyaan terpilih
            ->addSelect([
                'jumlah_soal_terpilih' => \App\Models\Soal::selectRaw('count(*)')
                    ->join('soal_bank_pertanyaan', 'soal.id', '=', 'soal_bank_pertanyaan.soal_id')
                    ->whereColumn('soal.mapel_id', 'mapel.id')
                    ->whereColumn('soal.jadwal_id', 'jadwal.id')
            ])
            ->get();

        $periodeUjian = PeriodeUjian::all();
        $tingkat = Tingkat::all();
        $kompetensiKeahlian = Kompetensi_keahlian::all();
        $semuaMapel = Mapel::all();

        return view('jadwal_ujian.index', compact(
            'mapels',
            'periodeUjian',
            'tingkat',
            'kompetensiKeahlian',
            'periodeAktif',
            'semuaMapel'
        ));
    }

    /**
     * Menyimpan jadwal ujian baru ke tabel `jadwal`
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'periode_ujian_id' => 'required|exists:periode_ujian,id',
            'mapel_id'         => 'required|exists:mapel,id',
            'tanggal_ujian'    => 'required|date',
            'jam_mulai'        => 'required',
            'jam_selesai'      => 'required|after:jam_mulai',
            'durasi'           => 'required',
            'status'           => 'required|in:aktif,nonaktif',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        // Generate token unik 6 karakter huruf kapital
        do {
            $token = strtoupper(Str::random(6));
        } while (Jadwal::where('token', $token)->exists());
        Jadwal::create([
            'periode_ujian_id'       => $request->periode_ujian_id,
            'mapel_id'               => $request->mapel_id,
            'tanggal_ujian'          => $request->tanggal_ujian,
            'jam_mulai'              => $request->jam_mulai,
            'jam_selesai'            => $request->jam_selesai,
            'durasi'                 => $request->durasi,
            'status'                 => $request->status,
            'token'                  => $token,
        ]);

        return redirect()->route('jadwal-ujian.index')
            ->with('success', 'Jadwal ujian berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit jadwal ujian
     */
    public function edit($id)
    {
        $jadwal = Jadwal::with(['mapel', 'periodeUjian',])
            ->findOrFail($id);

        $periodeUjian = PeriodeUjian::all();
        $semuaMapel = Mapel::all();

        return view('jadwal_ujian.edit', compact(
            'jadwal',
            'periodeUjian',
            'semuaMapel'
        ));
    }

    /**
     * Update jadwal ujian
     */
    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'periode_ujian_id' => 'required|exists:periode_ujian,id',
            'mapel_id'         => 'required|exists:mapel,id',
            'tanggal_ujian'    => 'required|date',
            'jam_mulai'        => 'required',
            'jam_selesai'      => 'required|after:jam_mulai',
            'durasi'           => 'required',
            'status'           => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $jadwal->update([
            'periode_ujian_id'       => $request->periode_ujian_id,
            'mapel_id'               => $request->mapel_id,
            'tanggal_ujian'          => $request->tanggal_ujian,
            'jam_mulai'              => $request->jam_mulai,
            'jam_selesai'            => $request->jam_selesai,
            'durasi'                 => $request->durasi,
            'status'                 => $request->status,
            // token tidak diubah
        ]);

        return redirect()->route('jadwal-ujian.index')
            ->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

    /**
     * Hapus jadwal ujian
     */
    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('jadwal-ujian.index')
            ->with('success', 'Jadwal ujian berhasil dihapus.');
    }

    /**
     * Tampilkan detail jadwal ujian (opsional)
     */
    public function show($id)
    {
        $jadwal = Jadwal::with(['mapel', 'periodeUjian', 'tingkat', 'kompetensiKeahlian'])
            ->findOrFail($id);

        return view('jadwal_ujian.show', compact('jadwal'));
    }
}
