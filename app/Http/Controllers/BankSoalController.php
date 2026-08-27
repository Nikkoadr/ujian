<?php

namespace App\Http\Controllers;

use App\Models\Bank_soal;
use App\Models\Tingkat;
use App\Models\Kompetensi_keahlian;
use Illuminate\Http\Request;

class BankSoalController extends Controller
{
    public function index()
    {
        $bankSoals = Bank_soal::with(['tingkat', 'kompetensiKeahlian'])->get();
        $tingkats = Tingkat::all();
        $kompetensis = Kompetensi_keahlian::all();
        return view('bank_soal.index', compact('bankSoals', 'tingkats', 'kompetensis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|unique:bank_soal',
            'nama_mapel' => 'required',
            'tingkat_id' => 'required|exists:tingkat,id',
            'kompetensi_keahlian_id' => 'nullable|exists:kompetensi_keahlian,id',
        ]);

        Bank_soal::create($request->all());
        return redirect()->route('bank-soal.index')->with('success', 'Bank Soal berhasil ditambahkan.');
    }

    public function edit(Bank_soal $bank_soal)
    {
        $tingkats = Tingkat::all();
        $kompetensis = Kompetensi_keahlian::all();
        return view('bank_soal.edit', compact('bank_soal', 'tingkats', 'kompetensis'));
    }

    public function update(Request $request, Bank_soal $bank_soal)
    {
        $request->validate([
            'kode_mapel' => 'required|unique:bank_soal,kode_mapel,' . $bank_soal->id,
            'nama_mapel' => 'required',
            'tingkat_id' => 'required|exists:tingkat,id',
            'kompetensi_keahlian_id' => 'nullable|exists:kompetensi_keahlian,id',
        ]);

        $bank_soal->update($request->all());
        return redirect()->route('bank-soal.index')->with('success', 'Bank Soal diperbarui.');
    }

    public function destroy(Bank_soal $bank_soal)
    {
        $bank_soal->delete();
        return redirect()->route('bank-soal.index')->with('success', 'Bank Soal dihapus.');
    }
}
