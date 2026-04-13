<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Kelas;
use App\Imports\SiswaImport;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

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
    public function index(Request $request)
    {
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

        if ($request->ajax()) {
            $query = Siswa::with(['user', 'kelas']);

            // Filter Status (Gunakan 'diblokir' sesuai logika toggleStatus Anda)
            if ($request->filterStatus) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('status', $request->filterStatus);
                });
            }

            // Filter Kelas
            if ($request->filterKelas) {
                $query->where('kelas_id', $request->filterKelas);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                // Agar kotak pencarian bisa mencari nama di tabel users
                ->filterColumn('user.nama', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('nama', 'like', "%{$keyword}%");
                    });
                })
                ->make(true);
        }

        if (Gate::allows('admin')) {
            return view('siswa.index', compact('kelas'));
        }

        if (Gate::allows('pengawas')) {
            return view('siswa.index_mobile', compact('kelas'));
        }

        return abort(403);
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

        // Logika switch status
        $user->status = ($user->status == 'aktif') ? 'diblokir' : 'aktif';
        $user->save();

        // Jika dipanggil via AJAX (DataTable Mobile)
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status ' . $user->nama . ' sekarang ' . $user->status
            ]);
        }

        // Jika dipanggil via klik tombol biasa (Admin Desktop)
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
