<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use App\Models\Siswa;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Pengawas;
use App\Models\Jadwal;
use App\Models\ProgresSiswa;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Gate::allows('admin')) {
            // Bagian admin (tidak berubah)
            $totalSiswa = Siswa::count();
            $totalGuru = User::where('role_id', 2)->count();
            $totalPengawas = Pengawas::count();
            $totalUser = User::count();

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

        if (Gate::any(['pengawas', 'guru'])) {
            return redirect()->route('token.index');
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

            // Ambil jadwal ujian yang aktif hari ini dan sesuai dengan kelas siswa
            $daftarUjian = Jadwal::with('mapel') // pastikan relasi mapel() ada di model Jadwal
                ->where('status', 'aktif')
                ->whereDate('tanggal_ujian', $today)
                ->whereHas('mapel', function ($query) use ($kelas) {
                    $query->where('tingkat_id', $kelas->tingkat_id)
                        ->where(function ($q) use ($kelas) {
                            $q->where('kompetensi_keahlian_id', $kelas->kompetensi_keahlian_id)
                                ->orWhereNull('kompetensi_keahlian_id');
                        });
                })
                ->get();

            // Tambahkan status partisipasi siswa untuk setiap jadwal
            foreach ($daftarUjian as $ujian) {
                $ujian->partisipasi = ProgresSiswa::where('user_id', Auth::id())
                    ->where('jadwal_id', $ujian->id)
                    ->first();
            }

            return view('daftar_mapel', compact('daftarUjian', 'siswa', 'kelas', 'user'));
        }

        abort(403);
    }
}
