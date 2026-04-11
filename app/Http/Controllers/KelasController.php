<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Tingkat;
use App\Models\Kompetensi_keahlian;

class KelasController extends Controller
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
        // Mengambil data kelas beserta relasinya, diurutkan berdasarkan tingkat
        $data_kelas = Kelas::with(['tingkat', 'kompetensi_keahlian'])
            ->orderBy('tingkat_id', 'asc')
            ->get();

        // Mengambil master data untuk dropdown di modal tambah
        $data_tingkat = Tingkat::all();
        $data_keahlian = Kompetensi_keahlian::all();

        return view('kelas.index', compact('data_kelas', 'data_tingkat', 'data_keahlian'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tingkat_id'             => 'required|exists:tingkat,id',
            'kompetensi_keahlian_id' => 'required|exists:kompetensi_keahlian,id',
            'nama_kelas'             => 'required|string|max:10|unique:kelas,nama_kelas',
        ], [
            // Pesan error kustom (opsional)
            'nama_kelas.unique' => 'Nama kelas ini sudah terdaftar!',
            'nama_kelas.max'    => 'Nama kelas maksimal 10 karakter.',
        ]);

        try {
            // 2. Simpan ke Database
            Kelas::create([
                'tingkat_id'             => $request->tingkat_id,
                'kompetensi_keahlian_id' => $request->kompetensi_keahlian_id,
                'nama_kelas'             => strtoupper($request->nama_kelas), // Kita paksa huruf besar agar rapi
            ]);

            // 3. Redirect dengan Feedback Sukses
            return redirect()->route('kelas.index')->with('success', 'Kelas baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Jika ada error database yang tidak terduga
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        $data_tingkat = Tingkat::all();
        $data_keahlian = Kompetensi_keahlian::all();

        return view('kelas.edit', compact('kelas', 'data_tingkat', 'data_keahlian'));
    }

    /**
     * Memproses pembaruan data kelas
     */
    public function update(Request $request, $id)
    {
        // Validasi input, abaikan pengecekan unique untuk ID yang sedang diedit
        $request->validate([
            'tingkat_id'             => 'required|exists:tingkat,id',
            'kompetensi_keahlian_id' => 'required|exists:kompetensi_keahlian,id',
            'nama_kelas'             => 'required|string|max:10|unique:kelas,nama_kelas,' . $id,
        ]);

        try {
            $kelas = Kelas::findOrFail($id);
            $kelas->update([
                'tingkat_id'             => $request->tingkat_id,
                'kompetensi_keahlian_id' => $request->kompetensi_keahlian_id,
                'nama_kelas'             => strtoupper($request->nama_kelas),
            ]);

            return redirect()->route('kelas.index')->with('success', 'Perubahan data kelas berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data kelas.');
        }
    }

    public function destroy($id)
    {
        try {
            // 1. Cari data kelas
            $kelas = Kelas::findOrFail($id);

            // 2. Eksekusi Hapus
            $kelas->delete();

            // 3. Redirect dengan pesan sukses (akan ditangkap Toast SweetAlert2)
            return redirect()->route('kelas.index')->with('success', 'Data kelas ' . $kelas->nama_kelas . ' berhasil dihapus permanen.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Cek jika error disebabkan karena data sedang digunakan di tabel lain (Foreign Key Constraint)
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'Gagal menghapus! Data kelas ini masih digunakan oleh data Siswa atau Jadwal.');
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menghapus data.');
        }
    }
}
