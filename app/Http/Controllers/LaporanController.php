<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Exports\LaporanUjianExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $kelas = Kelas::all();
        $mapel = Mapel::all();
        $tanggal = $request->tanggal ?: date('Y-m-d');

        // Memanggil fungsi privat agar tidak nulis query dua kali
        $results = $this->getFilteredData($request);

        return view('laporan.index', compact('results', 'kelas', 'mapel', 'tanggal'));
    }

    public function exportExcel(Request $request)
    {
        $results = $this->getFilteredData($request);
        $tanggal = $request->tanggal ?: date('Y-m-d');

        return Excel::download(new LaporanUjianExport($results), "Laporan_CBT_{$tanggal}.xlsx");
    }

    // --- INI FUNGSI YANG TADI KURANG ---
    private function getFilteredData(Request $request)
    {
        $query = DB::table('ujian_partisipasi')
            ->join('users', 'ujian_partisipasi.user_id', '=', 'users.id')
            ->join('siswa', 'users.id', '=', 'siswa.user_id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->join('mapel', 'ujian_partisipasi.mapel_id', '=', 'mapel.id')
            ->select(
                'users.nama as nama_siswa',
                'siswa.nis',
                'kelas.nama_kelas',
                'mapel.nama_mapel',
                'ujian_partisipasi.status as status_db',
                'ujian_partisipasi.user_id',
                'ujian_partisipasi.mapel_id',
                'ujian_partisipasi.created_at as tanggal_ujian',
                'ujian_partisipasi.updated_at as aktivitas_terakhir'
            );

        // Filter Tanggal
        $tanggal = $request->tanggal ?: date('Y-m-d');
        $query->whereDate('ujian_partisipasi.created_at', $tanggal);

        // Filter Kelas & Mapel
        if ($request->kelas_id) $query->where('kelas.id', $request->kelas_id);
        if ($request->mapel_id) $query->where('mapel.id', $request->mapel_id);

        return $query->get()->map(function ($item) {
            // Hitung Total Soal
            $totalSoal = DB::table('soal')->where('mapel_id', $item->mapel_id)->count();

            // Hitung Jawaban Benar
            $jawabanBenar = DB::table('ujian_progres')
                ->join('jawaban', 'ujian_progres.jawaban_id', '=', 'jawaban.id')
                ->where('ujian_progres.user_id', $item->user_id)
                ->where('ujian_progres.mapel_id', $item->mapel_id)
                ->where('jawaban.jawaban_benar', true)
                ->count();

            // Hitung Total yang Dijawab
            $totalDijawab = DB::table('ujian_progres')
                ->where('user_id', $item->user_id)
                ->where('mapel_id', $item->mapel_id)
                ->count();

            // Penentuan Status Label
            if ($item->status_db == 'selesai') {
                $item->status_label = 'SELESAI';
                $item->status_color = 'success';
            } else {
                // Anggap durasi ujian 2 jam (7200 detik)
                $isTimeout = strtotime($item->aktivitas_terakhir) < strtotime('-2 hours');

                if ($isTimeout) {
                    $item->status_label = ($totalDijawab < ($totalSoal * 0.5)) ? 'DITINGGALKAN' : 'WAKTU HABIS';
                    $item->status_color = ($totalDijawab < ($totalSoal * 0.5)) ? 'danger' : 'secondary';
                } else {
                    $item->status_label = 'MENGERJAKAN';
                    $item->status_color = 'primary';
                }
            }

            $item->benar = $jawabanBenar;
            $item->total_soal = $totalSoal;
            $item->dijawab = $totalDijawab;
            $item->nilai = $totalSoal > 0 ? round(($jawabanBenar / $totalSoal) * 100, 2) : 0;

            return $item;
        });
    }
}
