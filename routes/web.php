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
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UjianHandlerController;
use App\Http\Controllers\BankPertanyaanController;
use App\Http\Controllers\PeriodeUjianController;
use App\Http\Controllers\JadwalUjianController;

Route::get('/up', fn() => response()->json(['status' => 'ok']));

Route::get('/', function () {
    $siswaCount = \App\Models\Siswa::count();
    $mapelCount = \App\Models\Mapel::count();
    $kelasCount = \App\Models\Kelas::count();
    return view('welcome', compact('siswaCount', 'mapelCount', 'kelasCount'));
});

Route::get('/info', function () {
    dd(phpinfo());
});

Auth::routes(['register' => false, 'reset' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::resource('jadwal-ujian', JadwalUjianController::class);
Route::resource('guru', GuruController::class);
Route::resource('kelas', KelasController::class);

Route::resource('siswa', SiswaController::class);
Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
Route::patch('siswa/{id}/toggle-status', [SiswaController::class, 'toggleStatus'])->name('siswa.toggle-status');
Route::post('siswa/{id}/block', [SiswaController::class, 'toggleStatus'])->name('siswa.block');

Route::resource('pengawas', PengawasController::class);
Route::resource('periode_ujian', PeriodeUjianController::class);

Route::resource('mapel', MapelController::class);
Route::post('/mapel/import', [MapelController::class, 'import'])->name('mapel.import');
Route::get('soal/manage/{jadwalId}/{mapelId}', [SoalController::class, 'manage'])->name('soal.manage');
Route::put('soal/sync/{soal}', [SoalController::class, 'sync'])->name('soal.sync');

Route::post('bank-pertanyaan/upload-tinymce', [BankPertanyaanController::class, 'uploadTinyMceImage'])
    ->name('bank-pertanyaan.tinymce.upload');

Route::get('bank-soal/{mapel}', [BankPertanyaanController::class, 'index'])->name('bank-pertanyaan.index');
Route::post('bank-pertanyaan/{mapel}', [BankPertanyaanController::class, 'store'])->name('bank-pertanyaan.store');
Route::get('bank-pertanyaan/{bank_pertanyaan}/edit', [BankPertanyaanController::class, 'edit'])->name('bank-pertanyaan.edit');
Route::put('bank-pertanyaan/{bank_pertanyaan}', [BankPertanyaanController::class, 'update'])->name('bank-pertanyaan.update');
Route::delete('bank-pertanyaan/{bank_pertanyaan}', [BankPertanyaanController::class, 'destroy'])->name('bank-pertanyaan.destroy');

Route::get('/token', [TokenController::class, 'index'])->name('token.index');
Route::post('/token/refresh', [TokenController::class, 'refreshToken'])->name('token.refresh');

Route::post('/ujian/validasi', [TokenController::class, 'validasiToken'])->name('ujian.validasi');

Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');

Route::post('/siswa/toggle/{id}', [SiswaController::class, 'toggleStatus'])->name('siswa.toggle');

Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
Route::post('/setting/update', [SettingController::class, 'update'])->name('setting.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/ujian-handler', [UjianHandlerController::class, 'index'])->name('ujian-handler.index');

    Route::post('/ujian-handler/update-status', [UjianHandlerController::class, 'updateStatus'])
        ->name('ujian-handler.update-status');

    Route::post('/ujian-handler/update-waktu', [UjianHandlerController::class, 'updateWaktu'])
        ->name('ujian-handler.update-waktu');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard siswa
    Route::get('/dashboard', [UjianController::class, 'dashboard'])->name('dashboard');

    // Validasi token dan mulai ujian
    Route::post('/ujian/validasi', [UjianController::class, 'validasi'])->name('ujian.validasi');

    // Tampilan ujian (soal)
    Route::get('/ujian/{jadwal}', [UjianController::class, 'showExam'])->name('ujian.show');

    // Simpan jawaban (AJAX)
    Route::post('/ujian/simpan', [UjianController::class, 'simpan'])->name('ujian.simpan');

    // Catat pelanggaran (AJAX)
    Route::post('/ujian/pelanggaran', [UjianController::class, 'pelanggaran'])->name('ujian.pelanggaran');

    // Blokir user (AJAX)
    Route::post('/ujian/blokir', [UjianController::class, 'blokir'])->name('ujian.blokir');

    // Selesai ujian
    Route::post('/ujian/selesai/{jadwal}', [UjianController::class, 'selesai'])->name('ujian.selesai');
});
