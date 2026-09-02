<?php

namespace App\Http\Controllers;

use App\Models\PeriodeUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeriodeUjianController extends Controller
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
    
    public function index()
    {
        $periodeUjian = PeriodeUjian::latest()->get();
        return view('periode_ujian.index', compact('periodeUjian'));
    }

    public function create()
    {
        return view('periode_ujian.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_periode'   => 'required|string|max:255',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi'      => 'nullable|string',
            'is_active'      => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $active = $request->has('is_active') && $request->is_active == true;

        // Jika yang baru akan aktif, matikan semua periode lain yang aktif
        if ($active) {
            PeriodeUjian::where('is_active', true)->update(['is_active' => false]);
        }

        PeriodeUjian::create([
            'nama_periode'   => $request->nama_periode,
            'tanggal_mulai'  => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'deskripsi'      => $request->deskripsi,
            'is_active'      => $active ? 1 : 0,
        ]);

        return redirect()->route('periode_ujian.index')
            ->with('success', 'Periode Ujian berhasil ditambahkan.');
    }

    public function show(PeriodeUjian $periodeUjian)
    {
        return view('periode_ujian.show', compact('periodeUjian'));
    }

    public function edit(PeriodeUjian $periodeUjian)
    {
        return view('periode_ujian.edit', compact('periodeUjian'));
    }

    public function update(Request $request, PeriodeUjian $periodeUjian)
    {
        $validator = Validator::make($request->all(), [
            'nama_periode'   => 'required|string|max:255',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi'      => 'nullable|string',
            'is_active'      => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $active = $request->has('is_active') && $request->is_active == true;

        // Jika akan diaktifkan, matikan semua periode lain yang aktif (kecuali dirinya sendiri)
        if ($active) {
            PeriodeUjian::where('id', '!=', $periodeUjian->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $periodeUjian->update([
            'nama_periode'   => $request->nama_periode,
            'tanggal_mulai'  => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'deskripsi'      => $request->deskripsi,
            'is_active'      => $active ? 1 : 0,
        ]);

        return redirect()->route('periode_ujian.index')
            ->with('success', 'Periode Ujian berhasil diperbarui.');
    }

    public function destroy(PeriodeUjian $periodeUjian)
    {
        $periodeUjian->delete();
        return redirect()->route('periode_ujian.index')
            ->with('success', 'Periode Ujian berhasil dihapus.');
    }
}
