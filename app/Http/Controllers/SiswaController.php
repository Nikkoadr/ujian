<?php

namespace App\Http\Controllers;

use App\Imports\SiswaImport;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
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
     * @return Renderable
     */
    public function index(Request $request)
    {
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

        // --- 1. LOGIKA KHUSUS PENGAWAS (Mobile - AJAX Mode) ---
        if (Gate::allows('pengawas')) {
            if ($request->ajax()) {
                $query = Siswa::with(['user', 'kelas']);

                if ($request->filterStatus) {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('status', $request->filterStatus);
                    });
                }

                if ($request->filterKelas) {
                    $query->where('kelas_id', $request->filterKelas);
                }

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->filterColumn('user.nama', function ($query, $keyword) {
                        $query->whereHas('user', function ($q) use ($keyword) {
                            $q->where('nama', 'like', "%{$keyword}%");
                        });
                    })
                    ->make(true);
            }

            // View Mobile (Tanpa mengirim $siswas karena pakai AJAX)
            return view('siswa.index_mobile', compact('kelas'));
        }

        // --- 2. LOGIKA KHUSUS ADMIN (Web - Server-Side AJAX Mode) ---
        // 2000+ baris tidak lagi di-render sekaligus; DataTables mengambil
        // 10-100 baris per halaman via JSON (search/filter/sort di database).
        if (Gate::allows('admin')) {
            if ($request->ajax()) {
                $query = Siswa::with(['user', 'kelas']);

                if ($request->filterStatus) {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('status', $request->filterStatus);
                    });
                }

                if ($request->filterKelas) {
                    $query->where('kelas_id', $request->filterKelas);
                }

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->filter(function ($query) use ($request) {
                        if ($keyword = $request->input('search.value')) {
                            $query->where(function ($q) use ($keyword) {
                                $q->where('nis', 'like', "%{$keyword}%")
                                    ->orWhere('nisn', 'like', "%{$keyword}%")
                                    ->orWhereHas('user', function ($u) use ($keyword) {
                                        $u->where('nama', 'like', "%{$keyword}%")
                                            ->orWhere('email', 'like', "%{$keyword}%");
                                    });
                            });
                        }
                    }, true)
                    ->addColumn('nama', fn ($siswa) => $siswa->user->nama ?? '-')
                    ->orderColumn('nama', function ($query, $direction) {
                        $query->orderBy(
                            DB::table('users')->select('nama')->whereColumn('users.id', 'siswa.user_id'),
                            $direction
                        );
                    })
                    ->addColumn('detail', function ($siswa) {
                        $jenisKelamin = ($siswa->user->jenis_kelamin ?? '') === 'laki-laki' ? 'Laki-laki' : 'Perempuan';

                        return '<div class="small">'
                            .'<span class="badge badge-info mb-1">NISN: '.e($siswa->nisn).'</span> '
                            .'<span class="badge badge-secondary mb-1">NIS: '.e($siswa->nis).'</span><br>'
                            .'<span class="text-muted"><i class="fas fa-envelope fa-xs mr-1"></i> '.e($siswa->user->email ?? '-').'</span><br>'
                            .'<span class="text-muted"><i class="fas fa-venus-mars fa-xs mr-1"></i> '.$jenisKelamin.'</span>'
                            .'</div>';
                    })
                    ->addColumn('kelas', function ($siswa) {
                        return '<span class="badge badge-light p-2 border">'
                            .'<i class="fas fa-door-open mr-1 text-primary"></i> '.e($siswa->kelas->nama_kelas ?? '-')
                            .'</span>';
                    })
                    ->addColumn('status', function ($siswa) {
                        return ($siswa->user->status ?? '') === 'aktif'
                            ? '<span class="badge badge-success px-3 py-2" style="border-radius: 8px;">Aktif</span>'
                            : '<span class="badge badge-danger px-3 py-2" style="border-radius: 8px;">Terblokir</span>';
                    })
                    ->addColumn('aksi', function ($siswa) {
                        $aktif = ($siswa->user->status ?? '') === 'aktif';

                        return '<div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #eaecf4;">'
                            .'<a href="'.route('siswa.edit', $siswa->id).'" class="btn btn-sm btn-white text-primary border-right px-3" title="Edit Data"><i class="fas fa-edit"></i></a>'
                            .'<button type="button" class="btn btn-sm btn-white '.($aktif ? 'text-warning' : 'text-success').' border-right px-3 btn-toggle-status" data-id="'.$siswa->id.'" title="'.($aktif ? 'Blokir' : 'Buka Blokir').'"><i class="fas '.($aktif ? 'fa-user-slash' : 'fa-user-check').'"></i></button>'
                            .'<button type="button" class="btn btn-sm btn-white text-danger px-3 btn-delete-siswa" data-id="'.$siswa->id.'" data-nama="'.e($siswa->user->nama ?? '-').'" title="Hapus"><i class="fas fa-trash"></i></button>'
                            .'</div>';
                    })
                    ->rawColumns(['detail', 'kelas', 'status', 'aksi'])
                    ->make(true);
            }

            return view('siswa.index', compact('kelas'));
        }

        return abort(403);
    }

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
                'status' => 'aktif',
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
            'file_excel' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file_excel'));

            return redirect()->back()->with('success', 'Data siswa berhasil diimport!');
        } catch (\Exception $e) {
            // Gunakan withErrors agar JavaScript tahu ini error milik file_excel
            return redirect()->back()->withErrors(['file_excel' => 'Gagal import: '.$e->getMessage()]);
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
            'email' => 'required|email|unique:users,email,'.$user->id,
            'nisn' => 'required|unique:siswa,nisn,'.$siswa->id,
            'nis' => 'required|unique:siswa,nis,'.$siswa->id,
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
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

    public function toggleStatus($id)
    {
        $siswa = Siswa::findOrFail($id);
        $user = $siswa->user;

        $sebelum = $user->status;

        // Logika switch status
        $user->status = ($user->status == 'aktif') ? 'diblokir' : 'aktif';
        $user->save();

        // Reset pelanggaran jika status berubah dari diblokir menjadi aktif
        if ($sebelum == 'diblokir' && $user->status == 'aktif') {
            DB::table('ujian_siswa')
                ->where('user_id', $user->id)
                ->update(['pelanggaran' => 0]);
        }

        // Jika dipanggil via AJAX (DataTable Mobile)
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status '.$user->nama.' sekarang '.$user->status,
            ]);
        }

        // TAMBAHKAN INI: Skenario jika diklik dari halaman biasa (Non-AJAX)
        return redirect()->back()->with('success', 'Status '.$user->nama.' sekarang '.$user->status);
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
