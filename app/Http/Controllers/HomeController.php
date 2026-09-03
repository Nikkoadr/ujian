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
use Illuminate\Support\Facades\DB;
use App\Models\UjianSiswa;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Gate::allows('admin')) {
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
                    'siswa'       => null,
                    'kelas'       => null,
                    'user'        => $user,
                    'error'       => 'Data Kelas belum diatur.'
                ]);
            }

            $kelas = $siswa->kelas;

            // Ambil jadwal ujian aktif hari ini sesuai kelas siswa
            $jadwals = Jadwal::with('mapel')
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

            // Format data bersih untuk konsumsi frontend (AlpineJS)
            $daftarUjian = $jadwals->map(function ($jadwal) use ($user) {
                $partisipasi = UjianSiswa::where('user_id', $user->id)
                    ->where('jadwal_id', $jadwal->id)
                    ->first();

                // Format jam mulai dan jam selesai (HH:mm)
                $jamMulai = $jadwal->jam_mulai ? Carbon::parse($jadwal->jam_mulai)->format('H:i') : '--:--';
                $jamSelesai = $jadwal->jam_selesai ? Carbon::parse($jadwal->jam_selesai)->format('H:i') : '--:--';

                // Hitung durasi murni dalam menit
                $durasiStr = (string) ($jadwal->durasi instanceof Carbon ? $jadwal->durasi->toTimeString() : $jadwal->durasi);
                $durasiCarbon = Carbon::createFromTimeString($durasiStr);
                $totalMenit = ($durasiCarbon->hour * 60) + $durasiCarbon->minute;

                return [
                    'id'                     => $jadwal->id,
                    'mapel_id'               => $jadwal->mapel_id,
                    'nama_mapel'             => $jadwal->mapel->nama_mapel ?? '-',
                    'kode_mapel'             => $jadwal->mapel->kode_mapel ?? '-',
                    'kompetensi_keahlian_id' => $jadwal->mapel->kompetensi_keahlian_id ?? null,
                    'tanggal_ujian'          => $jadwal->tanggal_ujian,
                    'jam_mulai_format'       => $jamMulai,
                    'jam_selesai_format'     => $jamSelesai,
                    'durasi_menit'           => $totalMenit,
                    'partisipasi'            => $partisipasi ? [
                        'status'      => $partisipasi->status,
                        'mulai_ujian' => $partisipasi->mulai_ujian ? Carbon::parse($partisipasi->mulai_ujian)->toIso8601String() : null,
                    ] : null,
                ];
            });

            return view('daftar_mapel', compact('daftarUjian', 'siswa', 'kelas', 'user'));
        }

        abort(403);
    }
}
