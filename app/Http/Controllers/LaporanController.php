<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Jadwal;
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
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
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

        $filename = "Hasil_{$namaMapel}_{$namaKelas}_" . date('Y-m-d') . ".xlsx";

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
        return DB::table('ujian_siswa')
            ->join('users', 'ujian_siswa.user_id', '=', 'users.id')
            ->join('siswa', 'users.id', '=', 'siswa.user_id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->join('jadwal', 'ujian_siswa.jadwal_id', '=', 'jadwal.id')
            ->whereDate('ujian_siswa.created_at', $tanggal)
            ->select(
                'users.id as user_id',
                'users.nama as nama_siswa',
                'siswa.nis',
                'kelas.nama_kelas',
                DB::raw('COUNT(DISTINCT jadwal.mapel_id) as jumlah_mapel_db')
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
        // Ambil data ujian siswa berdasarkan jadwal
        $ujianSiswa = DB::table('ujian_siswa')
            ->join('jadwal', 'ujian_siswa.jadwal_id', '=', 'jadwal.id')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->where('ujian_siswa.user_id', $item->user_id)
            ->whereDate('ujian_siswa.created_at', $tanggal)
            ->select(
                'mapel.id as mapel_id',
                'mapel.nama_mapel',
                'ujian_siswa.status as status_db',
                'ujian_siswa.updated_at as aktivitas_terakhir',
                'ujian_siswa.selesai_ujian',
                'ujian_siswa.mulai_ujian',
                'jadwal.tanggal_ujian',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai',
                'jadwal.durasi'
            )
            ->orderBy('mapel.nama_mapel', 'asc')
            ->get();

        $mapelList = [];
        $statusGlobal = 'SELESAI';

        foreach ($ujianSiswa as $p) {
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
        // Ambil total soal dari bank_pertanyaan berdasarkan mapel
        $totalSoal = DB::table('bank_pertanyaan')
            ->where('mapel_id', $p->mapel_id)
            ->count();

        // Ambil jawaban benar dari progres_siswa
        // Gunakan kolom jawaban_benar dari bank_jawaban
        $jawabanBenar = DB::table('progres_siswa')
            ->join('bank_jawaban', 'progres_siswa.bank_jawaban_id', '=', 'bank_jawaban.id')
            ->join('bank_pertanyaan', 'progres_siswa.bank_pertanyaan_id', '=', 'bank_pertanyaan.id')
            ->where('progres_siswa.user_id', $userId)
            ->where('bank_pertanyaan.mapel_id', $p->mapel_id)
            ->where('bank_jawaban.jawaban_benar', true)
            ->count();

        // Total jawaban yang sudah diisi
        $totalDijawab = DB::table('progres_siswa')
            ->join('bank_pertanyaan', 'progres_siswa.bank_pertanyaan_id', '=', 'bank_pertanyaan.id')
            ->where('progres_siswa.user_id', $userId)
            ->where('bank_pertanyaan.mapel_id', $p->mapel_id)
            ->count();

        $nilai = $totalSoal > 0
            ? round(($jawabanBenar / $totalSoal) * 100, 2)
            : 0;

        // Logika status
        if ($p->status_db === 'selesai') {
            $statusLabel = 'SELESAI';
            $statusColor = 'success';
        } else {
            // Cek apakah sudah melewati waktu ujian
            $isTimeout = false;

            // Cek dari jadwal
            if ($p->tanggal_ujian && $p->jam_selesai) {
                $waktuSelesai = strtotime($p->tanggal_ujian . ' ' . $p->jam_selesai);
                $isTimeout = time() > $waktuSelesai;
            }

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
        return DB::table('ujian_siswa')
            ->join('users', 'ujian_siswa.user_id', '=', 'users.id')
            ->join('siswa', 'users.id', '=', 'siswa.user_id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->join('jadwal', 'ujian_siswa.jadwal_id', '=', 'jadwal.id')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->where('jadwal.mapel_id', $request->mapel_id)
            ->where('kelas.id', $request->kelas_id)
            ->whereDate('ujian_siswa.created_at', date('Y-m-d'))
            ->select(
                'users.nama as nama_siswa',
                'siswa.nis',
                'siswa.nisn',
                'kelas.nama_kelas',
                'mapel.id as mapel_id',
                'mapel.nama_mapel',
                'ujian_siswa.user_id',
                'ujian_siswa.status as status_db',
                'ujian_siswa.updated_at as aktivitas_terakhir',
                'ujian_siswa.selesai_ujian',
                'ujian_siswa.mulai_ujian',
                'jadwal.tanggal_ujian',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai',
                'jadwal.durasi'
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
            'tanggal_ujian' => $item->tanggal_ujian ?? null,
            'jam_selesai' => $item->jam_selesai ?? null,
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
