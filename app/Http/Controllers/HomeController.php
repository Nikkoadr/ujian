<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use App\Models\Siswa;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;


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
            // Mengambil total utama
            $totalSiswa = Siswa::count();
            $totalGuru = User::where('role_id', 2)->count(); // Asumsi role_id 2 adalah Guru
            $totalPengawas = User::where('role_id', 4)->count(); // Asumsi role_id 4 adalah Pengawas
            $totalUser = User::count();

            // Mengambil status user dalam satu kali query untuk efisiensi (opsional, tapi bagus untuk skala besar)
            $statusCounts = User::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            $statusAktif = $statusCounts['aktif'] ?? 0;
            $statusNonAktif = $statusCounts['nonaktif'] ?? 0;
            $statusBlokir = $statusCounts['diblokir'] ?? 0;

            $persenAktif = $totalUser > 0 ? ($statusAktif / $totalUser) * 100 : 0;
            $persenNonAktif = $totalUser > 0 ? ($statusNonAktif / $totalUser) * 100 : 0;
            $persenBlokir = $totalUser > 0 ? ($statusBlokir / $totalUser) * 100 : 0;

            $data = [
                'total_siswa'    => $totalSiswa,
                'total_guru'     => $totalGuru,
                'total_pengawas' => $totalPengawas,
                'total_mapel'    => Mapel::count(),
                'total_kelas'    => Kelas::count(),
                'p_aktif'        => round($persenAktif),
                'p_non'          => round($persenNonAktif),
                'p_blokir'       => round($persenBlokir),
                'status_blokir'  => $statusBlokir,
            ];

            return view('home', $data);
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
