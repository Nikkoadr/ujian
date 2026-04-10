<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengawas;

class PengawasController extends Controller
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
        $data = Pengawas::with(['guru.user'])->get();

        return view('pengawas.index', compact('data'));
    }
}
