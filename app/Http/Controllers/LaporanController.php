<?php

namespace App\Http\Controllers;

use App\Exports\LaporanUjianExport;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\PeriodeUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
        $periodes = PeriodeUjian::orderByDesc('is_active')->orderByDesc('tanggal_mulai')->get();
        $tanggal = date('Y-m-d');

        if ($request->ajax()) {
            return $this->datatable($request);
        }

        if (Gate::allows('admin')) {
            return view('laporan.index', compact('kelas', 'mapel', 'periodes', 'tanggal'));
        }

        if (Gate::allows('pengawas')) {
            return view('laporan.index_mobile', compact('kelas', 'mapel', 'periodes', 'tanggal'));
        }

        abort(403);
    }

    /**
     * Mapel yang memiliki jadwal pada periode tertentu (untuk dropdown berantai).
     */
    public function mapelByPeriode(Request $request)
    {
        $request->validate([
            'periode_ujian_id' => 'required|exists:periode_ujian,id',
        ]);

        $mapel = DB::table('mapel')
            ->join('jadwal', 'jadwal.mapel_id', '=', 'mapel.id')
            ->where('jadwal.periode_ujian_id', $request->periode_ujian_id)
            ->select('mapel.id', 'mapel.nama_mapel')
            ->distinct()
            ->orderBy('mapel.nama_mapel')
            ->get();

        return response()->json($mapel);
    }

    /**
     * Kelas yang relevan untuk suatu mapel: tingkat sama, dan jika mapel
     * terikat kompetensi tertentu maka kelasnya juga harus kompetensi itu.
     * (mis. MTK kelas 10 -> hanya kelas 10 yang muncul).
     */
    public function kelasByMapel(Request $request)
    {
        $request->validate([
            'mapel_id' => 'required|exists:mapel,id',
        ]);

        $mapel = Mapel::findOrFail($request->mapel_id);

        $kelas = Kelas::where('tingkat_id', $mapel->tingkat_id)
            ->when(! is_null($mapel->kompetensi_keahlian_id), function ($query) use ($mapel) {
                $query->where('kompetensi_keahlian_id', $mapel->kompetensi_keahlian_id);
            })
            ->orderBy('nama_kelas')
            ->get(['id', 'nama_kelas']);

        return response()->json($kelas);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'periode_ujian_id' => 'required|exists:periode_ujian,id',
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $periode = PeriodeUjian::findOrFail($request->periode_ujian_id);
        $mapel = Mapel::findOrFail($request->mapel_id);
        $kelas = Kelas::findOrFail($request->kelas_id);

        $rows = $this->getExportQuery($request)->get();
        $results = $this->enrichExportRows($rows, $request);

        if ($results->isEmpty()) {
            return redirect()->route('laporan.index')->with(
                'error',
                "Tidak ada data siswa kelas {$kelas->nama_kelas} untuk {$mapel->nama_mapel} pada periode {$periode->nama_periode}."
            );
        }

        $namaMapel = str_replace([' ', '/', '\\'], '_', $mapel->nama_mapel);
        $namaKelas = str_replace([' ', '/', '\\'], '_', $kelas->nama_kelas);
        $namaPeriode = str_replace([' ', '/', '\\'], '_', $periode->nama_periode);

        $filename = "Hasil_{$namaMapel}_{$namaKelas}_{$namaPeriode}_".date('Y-m-d').'.xlsx';

        $judul = "LAPORAN HASIL UJIAN {$mapel->nama_mapel} {$kelas->nama_kelas} - {$periode->nama_periode}";

        return Excel::download(
            new LaporanUjianExport($results, $judul),
            $filename
        );
    }

    private function datatable(Request $request)
    {
        $tanggal = date('Y-m-d');

        // Filter periode (riwayat). Kosong = perilaku lama: khusus hari ini.
        $periodeId = $request->input('periode_ujian_id') ?: null;

        $query = $this->getStudentQuery($tanggal, $periodeId);

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

        if (! in_array($orderDirection, ['asc', 'desc'])) {
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

        $students = $query
            ->offset($start)
            ->limit($length)
            ->get();

        $data = $this->enrichStudents($students, $tanggal, $periodeId);

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Saring berdasarkan periode bila diminta, selain itu khusus tanggal (hari ini).
     */
    private function scopeTanggalAtauPeriode($query, $tanggal, $periodeId)
    {
        if ($periodeId) {
            $query->where('jadwal.periode_ujian_id', $periodeId);
        } else {
            $query->whereDate('ujian_siswa.created_at', $tanggal);
        }

        return $query;
    }

    private function getStudentQuery($tanggal, $periodeId = null)
    {
        $query = DB::table('ujian_siswa')
            ->join('users', 'ujian_siswa.user_id', '=', 'users.id')
            ->join('siswa', 'users.id', '=', 'siswa.user_id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->join('jadwal', 'ujian_siswa.jadwal_id', '=', 'jadwal.id');

        $this->scopeTanggalAtauPeriode($query, $tanggal, $periodeId);

        return $query->select(
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

    /**
     * Lengkapi baris siswa dengan daftar mapel + nilai.
     *
     * Seluruh data diambil BULK (3 query untuk 1 halaman, bukan N+1):
     * partisipasi, total soal per jadwal, dan agregat progres.
     */
    private function enrichStudents($students, $tanggal, $periodeId = null)
    {
        $userIds = $students->pluck('user_id')->unique()->values()->all();

        if (empty($userIds)) {
            return collect();
        }

        // Semua partisipasi (hari ini atau 1 periode) untuk siswa di halaman ini (1 query)
        $partisipasiQuery = DB::table('ujian_siswa')
            ->join('jadwal', 'ujian_siswa.jadwal_id', '=', 'jadwal.id')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->whereIn('ujian_siswa.user_id', $userIds);

        $this->scopeTanggalAtauPeriode($partisipasiQuery, $tanggal, $periodeId);

        $partisipasi = $partisipasiQuery->select(
            'ujian_siswa.user_id',
            'jadwal.id as jadwal_id',
            'mapel.nama_mapel',
            'ujian_siswa.status as status_db',
            'jadwal.tanggal_ujian',
            'jadwal.jam_selesai'
        )
            ->orderBy('mapel.nama_mapel', 'asc')
            ->get()
            ->groupBy('user_id');

        $jadwalIds = $partisipasi->collapse()->pluck('jadwal_id')->unique()->values()->all();

        $totalSoalPerJadwal = $this->getTotalSoalPerJadwal($jadwalIds);
        $agregatMap = $this->getProgresAgregat($userIds, $jadwalIds);

        return $students->map(function ($item) use ($partisipasi, $totalSoalPerJadwal, $agregatMap) {
            $mapelList = [];
            $statusGlobal = 'SELESAI';

            foreach ($partisipasi->get($item->user_id, []) as $p) {
                $stat = $agregatMap[$item->user_id.'_'.$p->jadwal_id] ?? null;

                $detail = $this->buildMapelDetail(
                    $p->nama_mapel,
                    $p->status_db,
                    (int) ($totalSoalPerJadwal[$p->jadwal_id] ?? 0),
                    (int) ($stat->dijawab ?? 0),
                    (int) ($stat->benar ?? 0),
                    $p->tanggal_ujian,
                    $p->jam_selesai
                );

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
        });
    }

    /**
     * Total soal per jadwal SESUAI paket soal ujian (pivot soal_bank_pertanyaan),
     * bukan seluruh bank mapel. Sebelumnya pembaginya seluruh bank sehingga
     * nilai kekecilan kalau ujian hanya memakai sebagian soal.
     */
    private function getTotalSoalPerJadwal($jadwalIds)
    {
        if (empty($jadwalIds)) {
            return collect();
        }

        return DB::table('soal_bank_pertanyaan')
            ->join('soal', 'soal.id', '=', 'soal_bank_pertanyaan.soal_id')
            ->whereIn('soal.jadwal_id', $jadwalIds)
            ->select('soal.jadwal_id', DB::raw('COUNT(DISTINCT soal_bank_pertanyaan.bank_pertanyaan_id) as total'))
            ->groupBy('soal.jadwal_id')
            ->pluck('total', 'jadwal_id');
    }

    /**
     * Agregat progres per (siswa, jadwal): yang terjawab (jawaban tidak null)
     * dan yang benar. Dibatasi per jadwal agar progres ujian lain yang mapelnya
     * sama tidak bocor masuk ke nilai ini.
     */
    private function getProgresAgregat($userIds, $jadwalIds)
    {
        if (empty($userIds) || empty($jadwalIds)) {
            return [];
        }

        $rows = DB::table('progres_siswa')
            ->leftJoin('bank_jawaban', 'progres_siswa.bank_jawaban_id', '=', 'bank_jawaban.id')
            ->whereIn('progres_siswa.user_id', $userIds)
            ->whereIn('progres_siswa.jadwal_id', $jadwalIds)
            ->select(
                'progres_siswa.user_id',
                'progres_siswa.jadwal_id',
                DB::raw('COUNT(CASE WHEN progres_siswa.bank_jawaban_id IS NOT NULL THEN 1 END) as dijawab'),
                DB::raw('SUM(CASE WHEN bank_jawaban.jawaban_benar = 1 THEN 1 ELSE 0 END) as benar')
            )
            ->groupBy('progres_siswa.user_id', 'progres_siswa.jadwal_id')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $map[$row->user_id.'_'.$row->jadwal_id] = $row;
        }

        return $map;
    }

    private function buildMapelDetail($namaMapel, $statusDb, $totalSoal, $totalDijawab, $jawabanBenar, $tanggalUjian, $jamSelesai)
    {
        $nilai = $totalSoal > 0
            ? round(($jawabanBenar / $totalSoal) * 100, 2)
            : 0;

        // Logika status
        if ($statusDb === 'selesai') {
            $statusLabel = 'SELESAI';
            $statusColor = 'success';
        } else {
            // Cek apakah sudah melewati waktu ujian
            $isTimeout = false;

            // Cek dari jadwal
            if ($tanggalUjian && $jamSelesai) {
                $waktuSelesai = strtotime($tanggalUjian.' '.$jamSelesai);
                $isTimeout = time() > $waktuSelesai;
            }

            if ($isTimeout) {
                $ditinggalkan = $totalDijawab < ($totalSoal * 0.5);
                $statusLabel = $ditinggalkan ? 'DITINGGALKAN' : 'WAKTU HABIS';
                $statusColor = $ditinggalkan ? 'danger' : 'secondary';
            } else {
                $statusLabel = 'MENGERJAKAN';
                $statusColor = 'primary';
            }
        }

        return [
            'nama_mapel' => $namaMapel,
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
            ->where('jadwal.periode_ujian_id', $request->periode_ujian_id)
            ->select(
                'users.nama as nama_siswa',
                'siswa.nis',
                'siswa.nisn',
                'kelas.nama_kelas',
                'jadwal.id as jadwal_id',
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

    private function enrichExportRows($rows, $request)
    {
        $userIds = $rows->pluck('user_id')->unique()->values()->all();
        $jadwalIds = $rows->pluck('jadwal_id')->unique()->values()->all();

        $totalSoalPerJadwal = $this->getTotalSoalPerJadwal($jadwalIds);
        $agregatMap = $this->getProgresAgregat($userIds, $jadwalIds);

        $results = $rows->map(function ($item) use ($totalSoalPerJadwal, $agregatMap) {
            $stat = $agregatMap[$item->user_id.'_'.$item->jadwal_id] ?? null;

            $detail = $this->buildMapelDetail(
                $item->nama_mapel,
                $item->status_db,
                (int) ($totalSoalPerJadwal[$item->jadwal_id] ?? 0),
                (int) ($stat->dijawab ?? 0),
                (int) ($stat->benar ?? 0),
                $item->tanggal_ujian ?? null,
                $item->jam_selesai ?? null
            );

            $item->benar = $detail['benar'];
            $item->dijawab = $detail['dijawab'];
            $item->total_soal = $detail['total_soal'];
            $item->nilai = $detail['nilai'];
            $item->status_label = $detail['status_label'];
            $item->status_color = $detail['status_color'];

            return $item;
        });

        // Siswa sekelas yang sama sekali tidak mengerjakan: tetap masuk daftar
        // dengan nilai 0 (sebelumnya hilang dari download).
        $belumQuery = DB::table('siswa')
            ->join('users', 'users.id', '=', 'siswa.user_id')
            ->join('kelas', 'kelas.id', '=', 'siswa.kelas_id')
            ->where('siswa.kelas_id', $request->kelas_id)
            ->whereNotExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('ujian_siswa')
                    ->join('jadwal as j', 'j.id', '=', 'ujian_siswa.jadwal_id')
                    ->whereColumn('ujian_siswa.user_id', 'users.id')
                    ->where('j.mapel_id', $request->mapel_id)
                    ->where('j.periode_ujian_id', $request->periode_ujian_id);
            })
            ->select(
                'users.id as user_id',
                'users.nama as nama_siswa',
                'siswa.nis',
                'siswa.nisn',
                'kelas.nama_kelas'
            )
            ->orderBy('users.nama', 'asc');

        if (! empty($userIds)) {
            $belumQuery->whereNotIn('users.id', $userIds);
        }

        $referensiTotalSoal = $totalSoalPerJadwal->max() ?? 0;

        foreach ($belumQuery->get() as $siswa) {
            $siswa->jadwal_id = null;
            $siswa->mapel_id = $request->mapel_id;
            $siswa->benar = 0;
            $siswa->dijawab = 0;
            $siswa->total_soal = (int) $referensiTotalSoal;
            $siswa->nilai = 0;
            $siswa->status_label = 'BELUM MENGERJAKAN';
            $siswa->status_color = 'secondary';
            $results->push($siswa);
        }

        return $results->sortBy('nama_siswa')->values();
    }
}
