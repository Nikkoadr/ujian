<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\Mapel;

class LaporanController extends Controller
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
        $kelas = Kelas::all();
        $mapel = Mapel::all();

        // Query Utama
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
                'ujian_partisipasi.status as status_ujian',
                'ujian_partisipasi.user_id',
                'ujian_partisipasi.mapel_id'
            );

        // Filter
        if ($request->kelas_id) {
            $query->where('kelas.id', $request->kelas_id);
        }
        if ($request->mapel_id) {
            $query->where('mapel.id', $request->mapel_id);
        }

        $results = $query->get()->map(function ($item) {
            // Hitung total soal untuk mapel ini
            $totalSoal = DB::table('soal')->where('mapel_id', $item->mapel_id)->count();

            // Hitung jawaban benar dari progres
            $jawabanBenar = DB::table('ujian_progres')
                ->join('jawaban', 'ujian_progres.jawaban_id', '=', 'jawaban.id')
                ->where('ujian_progres.user_id', $item->user_id)
                ->where('ujian_progres.mapel_id', $item->mapel_id)
                ->where('jawaban.jawaban_benar', true)
                ->count();

            $item->benar = $jawabanBenar;
            $item->salah = $totalSoal - $jawabanBenar;
            $item->nilai = $totalSoal > 0 ? round(($jawabanBenar / $totalSoal) * 100, 2) : 0;

            return $item;
        });

        return view('laporan.index', compact('results', 'kelas', 'mapel'));
    }
}
