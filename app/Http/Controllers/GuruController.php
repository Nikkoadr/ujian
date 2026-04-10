<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;

class GuruController extends Controller
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
        // Eager load data user untuk menghindari masalah N+1 query
        $data = Guru::with('user')->orderBy('created_at', 'desc')->get();

        return view('guru.index', compact('data'));
    }
}
