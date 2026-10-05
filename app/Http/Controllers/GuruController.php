<?php

namespace App\Http\Controllers;

use App\Imports\GuruImport;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
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
        $data_guru = Guru::with('user')->get();

        return view('guru.index', compact('data_guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|unique:guru,nip',
            'nama' => 'required|string|max:255',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'role_id' => '2',
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'aktif',
            ]);
            Guru::create([
                'user_id' => $user->id,
                'nip' => $request->nip,
                'gelar_depan' => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
            ]);

            DB::commit();

            return redirect()->route('guru.index')->with('success', 'Guru berhasil didaftarkan!');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new GuruImport, $request->file('file_excel'));

            return redirect()->back()->with('success', 'Data guru berhasil diimport!');
        } catch (\Exception $e) {
            // Gunakan withErrors agar JavaScript tahu ini error milik file_excel
            return redirect()->back()->withErrors(['file_excel' => 'Gagal import: '.$e->getMessage()]);
        }
    }

    public function edit($id)
    {
        // Mengambil data guru beserta relasi user-nya
        $guru = Guru::with('user')->findOrFail($id);

        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        $user = $guru->user;

        $request->validate([
            'nip' => 'required|string|unique:guru,nip,'.$guru->id,
            'nama' => 'required|string|max:255',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:8|confirmed', // Nullable: tidak wajib diisi saat edit
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Data User
            $userData = [
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'email' => $request->email,
            ];

            // Hanya update password jika diisi
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // 2. Update Data Guru (NIP + Gelar)
            $guru->update([
                'nip' => $request->nip,
                'gelar_depan' => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
            ]);

            DB::commit();

            return redirect()->route('guru.index')->with('success', 'Data guru '.$user->nama.' berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $guru = Guru::findOrFail($id);
            User::findOrFail($guru->user_id)->delete();

            return redirect()->route('guru.index')->with('success', 'Data Guru berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }
}
