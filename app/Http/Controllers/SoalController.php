<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Jawaban;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
    public function index($mapel_id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        $soals = Soal::with('jawaban')
            ->where('mapel_id', $mapel_id)
            ->latest()
            ->paginate(20);
        return view('soal.index', compact('mapel', 'soals'));
    }

    public function store(Request $request, $mapel_id)
    {
        $request->validate([
            'pertanyaan'   => 'required',
            'jenis_soal'   => 'required',
            'gambar_soal'  => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $manager = ImageManager::usingDriver(Driver::class);

        $soal = new Soal();
        $soal->mapel_id = $mapel_id;
        $soal->pertanyaan = $request->pertanyaan;
        $soal->jenis_soal = $request->jenis_soal;
        $soal->bobot_nilai = $request->bobot_nilai ?? 1;

        if ($request->hasFile('gambar_soal')) {
            $file = $request->file('gambar_soal');
            $filename = uniqid() . '.jpg';

            $image = $manager->decode($file->getPathname());

            // resize auto ratio
            $image->scaleDown(width: 800);

            $path = storage_path('app/public/soal/' . $filename);

            $image->save($path, quality: 35);

            $soal->gambar_soal = $filename;
        }

        $soal->save();

        if ($request->jenis_soal == 'pg' && $request->has('jawaban')) {
            foreach ($request->jawaban as $key => $teks) {

                $jawaban = new Jawaban();
                $jawaban->soal_id = $soal->id;
                $jawaban->teks_jawaban = $teks;
                $jawaban->jawaban_benar = ($request->kunci_jawaban == $key);

                if ($request->hasFile("gambar_jawaban.$key")) {
                    $fileJwb = $request->file("gambar_jawaban.$key");
                    $filenameJwb = uniqid() . '_jwb.jpg';

                    $imageJwb = $manager->decode($fileJwb->getPathname());

                    $imageJwb->scaleDown(width: 600);

                    $pathJwb = storage_path('app/public/jawaban/' . $filenameJwb);

                    $imageJwb->save($pathJwb, quality: 35);

                    $jawaban->gambar_jawaban = $filenameJwb;
                }

                $jawaban->save();
            }
        }

        return redirect()->back()->with('success', 'Soal berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan'   => 'required',
            'gambar_soal'  => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $manager = ImageManager::usingDriver(Driver::class);
        $soal = Soal::findOrFail($id);

        // 1. Update Data Utama Soal
        $soal->pertanyaan = $request->pertanyaan;

        // Logika Hapus Gambar Soal (Jika ada checkbox hapus)
        if ($request->has('hapus_gambar_soal') && $soal->gambar_soal) {
            if (file_exists(storage_path('app/public/soal/' . $soal->gambar_soal))) {
                unlink(storage_path('app/public/soal/' . $soal->gambar_soal));
            }
            $soal->gambar_soal = null;
        }

        // Logika Upload Gambar Soal Baru
        if ($request->hasFile('gambar_soal')) {
            // Hapus gambar lama jika ada
            if ($soal->gambar_soal && file_exists(storage_path('app/public/soal/' . $soal->gambar_soal))) {
                unlink(storage_path('app/public/soal/' . $soal->gambar_soal));
            }

            $file = $request->file('gambar_soal');
            $filename = uniqid() . '.jpg';
            $image = $manager->decode($file->getPathname());

            $image->scaleDown(width: 800);
            $path = storage_path('app/public/soal/' . $filename);
            $image->save($path, quality: 35);

            $soal->gambar_soal = $filename;
        }

        $soal->save();

        // 2. Update Jawaban (Khusus Pilihan Ganda)
        if ($request->has('jawaban')) {
            foreach ($request->jawaban as $jawabanId => $teks) {
                $jawaban = Jawaban::find($jawabanId);
                if ($jawaban) {
                    $jawaban->teks_jawaban = $teks;
                    $jawaban->jawaban_benar = ($request->kunci_jawaban == $jawabanId);

                    // Logika Hapus Gambar Jawaban (Jika dicentang)
                    if (isset($request->hapus_gambar_jawaban[$jawabanId]) && $jawaban->gambar_jawaban) {
                        if (file_exists(storage_path('app/public/jawaban/' . $jawaban->gambar_jawaban))) {
                            unlink(storage_path('app/public/jawaban/' . $jawaban->gambar_jawaban));
                        }
                        $jawaban->gambar_jawaban = null;
                    }

                    // Logika Upload Gambar Jawaban Baru
                    if ($request->hasFile("gambar_jawaban_edit.$jawabanId")) {
                        // Hapus gambar lama
                        if ($jawaban->gambar_jawaban && file_exists(storage_path('app/public/jawaban/' . $jawaban->gambar_jawaban))) {
                            unlink(storage_path('app/public/jawaban/' . $jawaban->gambar_jawaban));
                        }

                        $fileJwb = $request->file("gambar_jawaban_edit.$jawabanId");
                        $filenameJwb = uniqid() . '_jwb.jpg';
                        $imageJwb = $manager->decode($fileJwb->getPathname());

                        $imageJwb->scaleDown(width: 600);
                        $pathJwb = storage_path('app/public/jawaban/' . $filenameJwb);
                        $imageJwb->save($pathJwb, quality: 35);

                        $jawaban->gambar_jawaban = $filenameJwb;
                    }

                    $jawaban->save();
                }
            }
        }

        return redirect()->back()->with('success', 'Soal dan Jawaban berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $soal = Soal::findOrFail($id);
        if ($soal->gambar_soal) Storage::disk('public')->delete('soal/' . $soal->gambar_soal);
        foreach ($soal->jawaban as $jw) {
            if ($jw->gambar_jawaban) Storage::disk('public')->delete('jawaban/' . $jw->gambar_jawaban);
        }
        $soal->delete();
        return redirect()->back()->with('success', 'Soal berhasil dihapus!');
    }
}
