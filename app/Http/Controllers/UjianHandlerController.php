<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Jadwal;
use App\Models\UjianSiswa;

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
                    ->orWhere('ujian_siswa.status', 'like', "%{$search}%")
                    ->orWhere('jadwal.token', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count();

        $columns = [
            1 => 'users.nama',
            2 => 'kelas.nama_kelas',
            3 => 'mapel.nama_mapel',
            4 => 'ujian_siswa.status',
            5 => 'ujian_siswa.pelanggaran',
            6 => 'ujian_siswa.mulai_ujian',
            7 => 'jadwal.token',
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
                    'mulai_ujian' => $p->mulai_ujian ? date('d-m-Y H:i', strtotime($p->mulai_ujian)) : '-',
                    'selesai_ujian' => $p->selesai_ujian ? date('d-m-Y H:i', strtotime($p->selesai_ujian)) : '-',
                    'token_jadwal' => $p->token_jadwal,
                    'tanggal_ujian' => $p->tanggal_ujian ? date('d-m-Y', strtotime($p->tanggal_ujian)) : '-',
                    'jam_ujian' => $p->jam_mulai . ' - ' . $p->jam_selesai,
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
        $query = DB::table('ujian_siswa')
            ->join('users', 'ujian_siswa.user_id', '=', 'users.id')
            ->join('siswa', 'users.id', '=', 'siswa.user_id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->join('jadwal', 'ujian_siswa.jadwal_id', '=', 'jadwal.id')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->select(
                'ujian_siswa.id',
                'users.nama as nama_siswa',
                'siswa.nis',
                'kelas.nama_kelas',
                'mapel.nama_mapel',
                'ujian_siswa.status',
                'ujian_siswa.pelanggaran',
                'ujian_siswa.mulai_ujian',
                'ujian_siswa.selesai_ujian',
                'jadwal.token as token_jadwal',
                'jadwal.tanggal_ujian',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai',
                'jadwal.durasi'
            );

        // Filter berdasarkan kelas
        if ($request->kelas_id) {
            $query->where('kelas.id', $request->kelas_id);
        }

        // Filter berdasarkan mapel (melalui jadwal)
        if ($request->mapel_id) {
            $query->where('mapel.id', $request->mapel_id);
        }

        // Filter berdasarkan status ujian
        if ($request->status) {
            $query->where('ujian_siswa.status', $request->status);
        }

        // Filter berdasarkan jadwal (opsional)
        if ($request->jadwal_id) {
            $query->where('jadwal.id', $request->jadwal_id);
        }

        return $query;
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ujian_siswa,id',
            'status' => 'required|in:sedang mengerjakan,selesai',
        ]);

        $updated = DB::table('ujian_siswa')
            ->whereIn('id', $request->ids)
            ->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

        // Jika status diubah menjadi selesai, update selesai_ujian
        if ($request->status === 'selesai') {
            DB::table('ujian_siswa')
                ->whereIn('id', $request->ids)
                ->whereNull('selesai_ujian')
                ->update([
                    'selesai_ujian' => now(),
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status peserta berhasil diperbarui.',
            'affected' => $updated,
        ]);
    }

    public function updateWaktu(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ujian_siswa,id',
            'mulai_ujian' => 'required|date',
        ]);

        $updated = DB::table('ujian_siswa')
            ->whereIn('id', $request->ids)
            ->update([
                'mulai_ujian' => $request->mulai_ujian,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Waktu mulai ujian berhasil diperbarui.',
            'affected' => $updated,
        ]);
    }

    public function resetUjian(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ujian_siswa,id',
        ]);

        $updated = DB::table('ujian_siswa')
            ->whereIn('id', $request->ids)
            ->update([
                'status' => 'sedang mengerjakan',
                'pelanggaran' => 0,
                'mulai_ujian' => null,
                'selesai_ujian' => null,
                'updated_at' => now(),
            ]);

        // Hapus progres siswa yang terkait
        DB::table('progres_siswa')
            ->whereIn('user_id', function ($query) use ($request) {
                $query->select('user_id')
                    ->from('ujian_siswa')
                    ->whereIn('id', $request->ids);
            })
            ->whereIn('jadwal_id', function ($query) use ($request) {
                $query->select('jadwal_id')
                    ->from('ujian_siswa')
                    ->whereIn('id', $request->ids);
            })
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data ujian berhasil direset.',
            'affected' => $updated,
        ]);
    }
}
