<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Exports\LaporanUjianExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;

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
        $tanggal = date('Y-m-d');

        if ($request->ajax()) {
            return $this->datatable($request);
        }

        if (Gate::allows('admin')) {
            return view('laporan.index', compact('kelas', 'mapel', 'tanggal'));
        }

        if (Gate::allows('pengawas')) {
            return view('laporan.index_mobile', compact('kelas', 'mapel', 'tanggal'));
        }

        abort(403);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'mapel_id' => 'required',
            'kelas_id' => 'required',
        ]);

        $mapel = Mapel::findOrFail($request->mapel_id);
        $kelas = Kelas::findOrFail($request->kelas_id);

        $results = $this->getExportQuery($request)
            ->get()
            ->map(function ($item) {
                return $this->formatSingleMapelResult($item);
            });

        $namaMapel = str_replace([' ', '/', '\\'], '_', $mapel->nama_mapel);
        $namaKelas = str_replace([' ', '/', '\\'], '_', $kelas->nama_kelas);

        $tanggalMapel = date('Y-m-d', strtotime($mapel->tanggal ?? $mapel->created_at));

        $filename = "Hasil_{$namaMapel}_{$namaKelas}_{$tanggalMapel}.xlsx";

        $judul = "LAPORAN HASIL UJIAN {$mapel->nama_mapel} {$kelas->nama_kelas}";

        return Excel::download(
            new LaporanUjianExport($results, $judul),
            $filename
        );
    }

    private function datatable(Request $request)
    {
        $tanggal = date('Y-m-d');

        $query = $this->getStudentQuery($tanggal);

        $recordsTotal = DB::query()
            ->fromSub(clone $query, 'data_siswa')
            ->count();

        if ($request->filled('search.value')) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('users.nama', 'like', "%{$search}%")
                    ->orWhere('siswa.nis', 'like', "%{$search}%")
                    ->orWhere('kelas.nama_kelas', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = DB::query()
            ->fromSub(clone $query, 'data_siswa_filtered')
            ->count();

        $start = intval($request->start ?? 0);
        $length = intval($request->length ?? 50);

        $orderColumnIndex = intval($request->input('order.0.column', 0));
        $orderDirection = $request->input('order.0.dir', 'asc');

        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'asc';
        }

        if ($orderColumnIndex === 0) {
            $query->orderBy('users.nama', $orderDirection);
        } elseif ($orderColumnIndex === 2) {
            $query->orderBy('jumlah_mapel_db', $orderDirection);
        } else {
            $query->orderBy('kelas.nama_kelas', 'asc')
                ->orderBy('users.nama', 'asc');
        }

        $data = $query
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function ($item) use ($tanggal) {
                return $this->formatStudentResult($item, $tanggal);
            });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    private function getStudentQuery($tanggal)
    {
        return DB::table('ujian_partisipasi')
            ->join('users', 'ujian_partisipasi.user_id', '=', 'users.id')
            ->join('siswa', 'users.id', '=', 'siswa.user_id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->whereDate('ujian_partisipasi.created_at', $tanggal)
            ->select(
                'users.id as user_id',
                'users.nama as nama_siswa',
                'siswa.nis',
                'kelas.nama_kelas',
                DB::raw('COUNT(DISTINCT ujian_partisipasi.mapel_id) as jumlah_mapel_db')
            )
            ->groupBy(
                'users.id',
                'users.nama',
                'siswa.nis',
                'kelas.nama_kelas'
            );
    }

    private function formatStudentResult($item, $tanggal)
    {
        $partisipasi = DB::table('ujian_partisipasi')
            ->join('mapel', 'ujian_partisipasi.mapel_id', '=', 'mapel.id')
            ->where('ujian_partisipasi.user_id', $item->user_id)
            ->whereDate('ujian_partisipasi.created_at', $tanggal)
            ->select(
                'mapel.id as mapel_id',
                'mapel.nama_mapel',
                'ujian_partisipasi.status as status_db',
                'ujian_partisipasi.updated_at as aktivitas_terakhir'
            )
            ->orderBy('mapel.nama_mapel', 'asc')
            ->get();

        $mapelList = [];
        $statusGlobal = 'SELESAI';

        foreach ($partisipasi as $p) {
            $detail = $this->formatMapelDetail($item->user_id, $p);

            if ($detail['status_label'] !== 'SELESAI') {
                $statusGlobal = 'BELUM SELESAI';
            }

            $mapelList[] = $detail;
        }

        $item->jumlah_mapel = count($mapelList);
        $item->mapel_list = $mapelList;
        $item->status_global = $statusGlobal;
        $item->status_color = $statusGlobal === 'SELESAI' ? 'success' : 'warning';

        return $item;
    }

    private function formatMapelDetail($userId, $p)
    {
        $totalSoal = DB::table('soal')
            ->where('mapel_id', $p->mapel_id)
            ->count();

        $jawabanBenar = DB::table('ujian_progres')
            ->join('jawaban', 'ujian_progres.jawaban_id', '=', 'jawaban.id')
            ->where('ujian_progres.user_id', $userId)
            ->where('ujian_progres.mapel_id', $p->mapel_id)
            ->where('jawaban.jawaban_benar', true)
            ->count();

        $totalDijawab = DB::table('ujian_progres')
            ->where('user_id', $userId)
            ->where('mapel_id', $p->mapel_id)
            ->count();

        $nilai = $totalSoal > 0
            ? round(($jawabanBenar / $totalSoal) * 100, 2)
            : 0;

        if ($p->status_db === 'selesai') {
            $statusLabel = 'SELESAI';
            $statusColor = 'success';
        } else {
            $isTimeout = strtotime($p->aktivitas_terakhir) < strtotime('-2 hours');

            if ($isTimeout) {
                $statusLabel = ($totalDijawab < ($totalSoal * 0.5))
                    ? 'DITINGGALKAN'
                    : 'WAKTU HABIS';

                $statusColor = ($totalDijawab < ($totalSoal * 0.5))
                    ? 'danger'
                    : 'secondary';
            } else {
                $statusLabel = 'MENGERJAKAN';
                $statusColor = 'primary';
            }
        }

        return [
            'nama_mapel' => $p->nama_mapel,
            'nilai' => $nilai,
            'benar' => $jawabanBenar,
            'dijawab' => $totalDijawab,
            'total_soal' => $totalSoal,
            'status_label' => $statusLabel,
            'status_color' => $statusColor,
        ];
    }

    private function getExportQuery(Request $request)
    {
        return DB::table('ujian_partisipasi')
            ->join('users', 'ujian_partisipasi.user_id', '=', 'users.id')
            ->join('siswa', 'users.id', '=', 'siswa.user_id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->join('mapel', 'ujian_partisipasi.mapel_id', '=', 'mapel.id')
            ->where('ujian_partisipasi.mapel_id', $request->mapel_id)
            ->where('kelas.id', $request->kelas_id)
            ->select(
                'users.nama as nama_siswa',
                'siswa.nis',
                'siswa.nisn',
                'kelas.nama_kelas',
                'mapel.id as mapel_id',
                'mapel.nama_mapel',
                'ujian_partisipasi.user_id',
                'ujian_partisipasi.status as status_db',
                'ujian_partisipasi.updated_at as aktivitas_terakhir'
            )
            ->orderBy('users.nama', 'asc');
    }

    private function formatSingleMapelResult($item)
    {
        $detail = $this->formatMapelDetail($item->user_id, (object) [
            'mapel_id' => $item->mapel_id,
            'nama_mapel' => $item->nama_mapel,
            'status_db' => $item->status_db,
            'aktivitas_terakhir' => $item->aktivitas_terakhir,
        ]);

        $item->benar = $detail['benar'];
        $item->dijawab = $detail['dijawab'];
        $item->total_soal = $detail['total_soal'];
        $item->nilai = $detail['nilai'];
        $item->status_label = $detail['status_label'];
        $item->status_color = $detail['status_color'];

        return $item;
    }
}
