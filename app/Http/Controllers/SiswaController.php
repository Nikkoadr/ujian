<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;

class SiswaController extends Controller
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
        $siswas = Siswa::with([
            'user',
            'kelas.tingkat',
            'kelas.kompetensi'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('siswa.index', compact('siswas'));
    }
}
