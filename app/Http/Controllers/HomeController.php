<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class HomeController extends Controller
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
        if (Gate::any(['admin', 'guru', 'pengawas'])) {
            return view('home');
        }

        if (Gate::allows('siswa')) {
            $today = now()->toDateString();

            $user = User::with([
                'siswa.kelas.tingkat',
                'siswa.kelas.kompetensi_keahlian'
            ])->find(Auth::id());

            $siswa = $user->siswa;

            if (!$siswa || !$siswa->kelas) {
                return view('daftar_mapel', [
                    'daftarUjian' => [],
                    'error' => 'Data Kelas belum diatur.'
                ]);
            }

            $kelas = $siswa->kelas;

            $daftarUjian = Mapel::where('status', 'aktif')
                ->whereDate('tanggal_ujian', $today)
                ->where('tingkat_id', $kelas->tingkat_id)
                ->where(function ($query) use ($kelas) {
                    $query->where('kompetensi_keahlian_id', $kelas->kompetensi_keahlian_id)
                        ->orWhereNull('kompetensi_keahlian_id');
                })
                // Menarik data partisipasi untuk user yang sedang login
                ->with(['partisipasi' => function ($query) {
                    $query->where('user_id', Auth::id());
                }])
                ->get();

            return view('daftar_mapel', compact('daftarUjian', 'siswa', 'kelas', 'user'));
        }

        abort(403);
    }
}
