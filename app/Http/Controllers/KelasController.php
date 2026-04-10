<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
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
        $data = Kelas::with([
            'tingkat',
            'kompetensi'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kelas.index', compact('kelas'));
    }
}
