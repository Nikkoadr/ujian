<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Jadwal;
use App\Models\BankPertanyaan;
use Illuminate\Http\Request;

class SoalController extends Controller
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
    
    // Halaman kelola soal (tambah/hapus pertanyaan dari paket soal)
    public function manage($jadwalId, $mapelId)
    {
        // Cari atau buat Soal berdasarkan jadwal_id dan mapel_id
        $soal = Soal::firstOrCreate(
            ['jadwal_id' => $jadwalId, 'mapel_id' => $mapelId],
            ['periode_ujian_id' => Jadwal::find($jadwalId)->periode_ujian_id]
        );

        // Ambil semua pertanyaan dari mapel tersebut
        $bankPertanyaan = BankPertanyaan::where('mapel_id', $mapelId)->get();

        // Ambil id pertanyaan yang sudah terpilih pada Soal ini
        $selectedPertanyaan = $soal->bankPertanyaan->pluck('id')->toArray();

        return view('soal.manage', compact(
            'soal',
            'bankPertanyaan',
            'selectedPertanyaan'
        ));
    }

    // Sinkronisasi pertanyaan yang dipilih (tambah/hapus)
    public function sync(Request $request, Soal $soal)
    {
        $request->validate([
            'pertanyaan_ids' => 'array|exists:bank_pertanyaan,id',
        ]);

        $soal->bankPertanyaan()->sync($request->pertanyaan_ids ?? []);

        return redirect()->route('jadwal-ujian.index')->with('success', 'Paket soal berhasil diperbarui.');
    }
}
