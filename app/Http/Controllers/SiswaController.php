<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Kelas;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
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
        $kelas = Kelas::with('tingkat', 'kompetensi')->get();

        $siswas = Siswa::with([
            'user',
            'kelas.tingkat',
            'kelas.kompetensi'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('siswa.index', compact('siswas', 'kelas'));
    }

    // Tambah Siswa (Admin Only)
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'password' => 'required|string|min:6|confirmed',
            'email' => 'required|email|unique:users,email',
            'nisn' => 'required|unique:siswa,nisn|max:15',
            'nis' => 'required|unique:siswa,nis|max:15',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'role_id' => 3, // Role Siswa
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin ?? 'laki-laki',
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa',
                'status' => 'aktif'
            ]);

            $user->siswa()->create([
                'nisn' => $request->nisn,
                'nis' => $request->nis,
                'kelas_id' => $request->kelas_id,
            ]);
        });

        return redirect()->back()->with('success', 'Siswa berhasil didaftarkan!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data siswa berhasil diimport!');
        } catch (\Exception $e) {
            // Gunakan withErrors agar JavaScript tahu ini error milik file_excel
            return redirect()->back()->withErrors(['file_excel' => 'Gagal import: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $siswa = Siswa::with('user', 'kelas')->findOrFail($id);
        $kelas = Kelas::all();
        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $user = $siswa->user;

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nisn' => 'required|unique:siswa,nisn,' . $siswa->id,
            'nis' => 'required|unique:siswa,nis,' . $siswa->id,
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'password' => 'nullable|min:8', // Password opsional saat edit
        ]);

        DB::transaction(function () use ($request, $siswa, $user) {
            // Update data User
            $userData = [
                'nama' => $request->nama,
                'email' => $request->email,
                'jenis_kelamin' => $request->jenis_kelamin,
            ];

            // Jika password diisi, enkripsi dan tambahkan ke array
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Update data Siswa
            $siswa->update([
                'nisn' => $request->nisn,
                'nis' => $request->nis,
                'kelas_id' => $request->kelas_id,
            ]);
        });

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    // Blokir / Buka Blokir (Admin & Pengawas)
    public function toggleStatus($id)
    {
        $siswa = Siswa::findOrFail($id);
        $user = $siswa->user;

        $user->status = ($user->status == 'aktif') ? 'diblokir' : 'aktif';
        $user->save();

        return redirect()->back()->with('success', 'Status akun ' . $user->nama . ' berhasil diubah.');
    }

    // Hapus Siswa (Admin Only)
    public function destroy($id)
    {
        // Cari data siswa
        $siswa = Siswa::findOrFail($id);

        // Ambil user terkait
        $user = $siswa->user;

        // Gunakan Transaction untuk memastikan kedua data terhapus tanpa sisa
        DB::transaction(function () use ($user) {
            if ($user) {
                $user->delete(); // Menghapus User dan otomatis Siswa jika ada Cascade
            }
        });

        return redirect()->back()->with('success', 'Data siswa dan akun pengguna berhasil dihapus permanen.');
    }
}
