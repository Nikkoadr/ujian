<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\Mapel;

class UjianHandlerController extends Controller
{
    public function index(Request $request)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mapel = Mapel::orderBy('nama_mapel')->get();

        if ($request->ajax()) {
            return $this->datatable($request);
        }

        return view('ujian_handler.index', compact('kelas', 'mapel'));
    }

    private function datatable(Request $request)
    {
        $query = $this->baseQuery($request);

        $recordsTotal = (clone $query)->count();

        if ($request->filled('search.value')) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('users.nama', 'like', "%{$search}%")
                    ->orWhere('siswa.nis', 'like', "%{$search}%")
                    ->orWhere('kelas.nama_kelas', 'like', "%{$search}%")
                    ->orWhere('mapel.nama_mapel', 'like', "%{$search}%")
                    ->orWhere('ujian_partisipasi.status', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count();

        $columns = [
            1 => 'users.nama',
            2 => 'kelas.nama_kelas',
            3 => 'mapel.nama_mapel',
            4 => 'ujian_partisipasi.status',
            5 => 'ujian_partisipasi.pelanggaran',
            6 => 'ujian_partisipasi.mulai_ujian',
        ];

        $orderColumnIndex = intval($request->input('order.0.column', 1));
        $orderDirection = $request->input('order.0.dir', 'asc');

        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'asc';
        }

        if (isset($columns[$orderColumnIndex])) {
            $query->orderBy($columns[$orderColumnIndex], $orderDirection);
        } else {
            $query->orderBy('users.nama', 'asc');
        }

        $start = intval($request->start ?? 0);
        $length = intval($request->length ?? 50);

        $data = $query
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nama_siswa' => $p->nama_siswa,
                    'nis' => $p->nis,
                    'nama_kelas' => $p->nama_kelas,
                    'nama_mapel' => $p->nama_mapel,
                    'status' => $p->status,
                    'pelanggaran' => $p->pelanggaran,
                    'mulai_ujian' => date('d-m-Y H:i', strtotime($p->mulai_ujian)),
                ];
            });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    private function baseQuery(Request $request)
    {
        $query = DB::table('ujian_partisipasi')
            ->join('users', 'ujian_partisipasi.user_id', '=', 'users.id')
            ->join('siswa', 'users.id', '=', 'siswa.user_id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->join('mapel', 'ujian_partisipasi.mapel_id', '=', 'mapel.id')
            ->select(
                'ujian_partisipasi.id',
                'users.nama as nama_siswa',
                'siswa.nis',
                'kelas.nama_kelas',
                'mapel.nama_mapel',
                'ujian_partisipasi.status',
                'ujian_partisipasi.pelanggaran',
                'ujian_partisipasi.mulai_ujian'
            );

        if ($request->kelas_id) {
            $query->where('kelas.id', $request->kelas_id);
        }

        if ($request->mapel_id) {
            $query->where('mapel.id', $request->mapel_id);
        }

        if ($request->status) {
            $query->where('ujian_partisipasi.status', $request->status);
        }

        return $query;
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'status' => 'required|in:sedang mengerjakan,selesai',
        ]);

        DB::table('ujian_partisipasi')
            ->whereIn('id', $request->ids)
            ->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Status peserta berhasil diperbarui.',
        ]);
    }

    public function updateWaktu(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'mulai_ujian' => 'required|date',
        ]);

        DB::table('ujian_partisipasi')
            ->whereIn('id', $request->ids)
            ->update([
                'mulai_ujian' => $request->mulai_ujian,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Waktu mulai ujian berhasil diperbarui.',
        ]);
    }
}
