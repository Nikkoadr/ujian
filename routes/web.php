<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\PengawasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\TokenController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/info', function () {
    dd(phpinfo());
});

Auth::routes(['register' => false, 'reset' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::post('/ujian/validasi', [MapelController::class, 'validasiToken'])->name('ujian.validasi');
Route::post('/mapel/import', [MapelController::class, 'import'])->name('mapel.import');

Route::get('/ujian/{id}/mulai', [UjianController::class, 'index'])->name('ujian.mulai');
Route::post('/ujian/simpan', [UjianController::class, 'simpan'])->name('ujian.simpan');
Route::post('/ujian/blokir', [UjianController::class, 'blokirSiswa'])->name('ujian.blokir');
Route::get('/ujian/{id}/selesai', [UjianController::class, 'selesai'])->name('ujian.selesai');

Route::resource('guru', GuruController::class);
Route::resource('kelas', KelasController::class);

Route::resource('siswa', SiswaController::class);
Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
Route::patch('siswa/{id}/toggle-status', [SiswaController::class, 'toggleStatus'])->name('siswa.toggle-status');
Route::post('siswa/{id}/block', [SiswaController::class, 'toggleStatus'])->name('siswa.block');

Route::resource('pengawas', PengawasController::class);
Route::resource('mapel', MapelController::class);

Route::prefix('soal')->name('soal.')->group(function () {
    Route::get('/mapel/{mapel_id}', [SoalController::class, 'index'])->name('index');

    Route::post('/mapel/{mapel_id}/store', [SoalController::class, 'store'])->name('store');

    Route::get('/{id}/edit', [SoalController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SoalController::class, 'update'])->name('update');

    Route::delete('/{id}', [SoalController::class, 'destroy'])->name('destroy');
});

Route::get('/token', [TokenController::class, 'index'])->name('token.index');

Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');
Route::post('/siswa/toggle/{id}', [SiswaController::class, 'toggleStatus'])->name('siswa.toggle');