<?php

namespace App\Http\Controllers;

use App\Models\BankPertanyaan;
use App\Models\BankJawaban;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BankPertanyaanController extends Controller
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
     * Tampilkan daftar soal untuk suatu mapel.
     *
     * @param  int  $mapel_id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($mapel_id)
    {
        $mapel = Mapel::findOrFail($mapel_id);
        $bank_pertanyaan = BankPertanyaan::with('jawaban')
            ->where('mapel_id', $mapel_id)
            ->orderBy('created_at', 'asc')
            ->paginate(60);
        return view('mapel.bank_pertanyaan', compact('mapel', 'bank_pertanyaan'));
    }

    /**
     * Simpan soal baru beserta jawaban pilihan ganda.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $mapel_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $mapel_id)
    {
        $request->validate([
            'pertanyaan'    => 'required',
            'jenis_soal'    => 'required|in:pg,essay',
            'jawaban'       => 'nullable|array',
            'kunci_jawaban' => 'nullable',
        ]);

        // Buat soal
        $soal = new BankPertanyaan();
        $soal->mapel_id = $mapel_id;
        $soal->pertanyaan = $request->pertanyaan;
        $soal->jenis_soal = $request->jenis_soal;
        $soal->bobot_nilai = $request->bobot_nilai ?? 1;
        $soal->gambar_soal = null; // tidak digunakan di form saat ini
        $soal->kunci_jawaban_id = null; // akan diisi setelah jawaban disimpan
        $soal->save();

        // Jika jenis soal PG dan ada jawaban
        if ($request->jenis_soal == 'pg' && $request->has('jawaban')) {
            $kunciIndex = $request->kunci_jawaban; // index jawaban benar (0..4)

            foreach ($request->jawaban as $key => $teks) {
                $jawaban = new BankJawaban();
                $jawaban->bank_pertanyaan_id = $soal->id;
                $jawaban->urutan = $key + 1; // urutan 1..5
                $jawaban->teks_jawaban = $teks;
                $jawaban->gambar_jawaban = null;
                $jawaban->jawaban_benar = ((string) $kunciIndex === (string) $key);
                $jawaban->save();

                // Jika ini jawaban benar, simpan id-nya ke soal
                if ($jawaban->jawaban_benar) {
                    $soal->kunci_jawaban_id = $jawaban->id;
                }
            }

            $soal->save(); // update kunci_jawaban_id
        }

        $this->cleanOrphanEditorImages();

        return redirect()
            ->back()
            ->with('success', 'Soal berhasil disimpan!');
    }

    /**
     * Tampilkan form edit soal.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $soal = BankPertanyaan::with('jawaban')->findOrFail($id);
        return view('mapel.edit_pertanyaan', compact('soal'));
    }

    /**
     * Perbarui data soal dan jawaban.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'jawaban'    => 'nullable|array',
        ]);

        $soal = BankPertanyaan::with('jawaban')->findOrFail($id);

        // Kumpulkan HTML lama untuk pembersihan gambar
        $oldPertanyaan = $soal->pertanyaan;
        $oldJawabanHtml = $soal->jawaban->pluck('teks_jawaban')->toArray();

        // Update pertanyaan
        $soal->pertanyaan = $request->pertanyaan;
        $soal->save();

        // Update jawaban
        if ($request->has('jawaban')) {
            $kunciIndex = $request->kunci_jawaban;

            foreach ($request->jawaban as $jawabanId => $teks) {
                $jawaban = BankJawaban::where('bank_pertanyaan_id', $soal->id)
                    ->where('id', $jawabanId)
                    ->first();

                if (!$jawaban) {
                    continue;
                }

                $jawaban->teks_jawaban = $teks;
                $jawaban->jawaban_benar = ((string) $kunciIndex === (string) $jawabanId);
                $jawaban->save();

                // Jika jawaban ini benar, set kunci_jawaban_id
                if ($jawaban->jawaban_benar) {
                    $soal->kunci_jawaban_id = $jawaban->id;
                }
            }

            $soal->save();
        }

        // Bersihkan gambar yang tidak terpakai
        $newJawabanHtml = BankJawaban::where('bank_pertanyaan_id', $soal->id)
            ->pluck('teks_jawaban')
            ->toArray();

        $oldHtml = implode(' ', array_merge([$oldPertanyaan], $oldJawabanHtml));
        $newHtml = implode(' ', array_merge([$request->pertanyaan], $newJawabanHtml));

        $this->cleanUnusedEditorImages($oldHtml, $newHtml, $soal->id);
        $this->cleanOrphanEditorImages();

        return redirect()
            ->route('bank-pertanyaan.index', $soal->mapel_id)
            ->with([
                'success'   => 'Soal dan Jawaban berhasil diperbarui!',
                'highlight' => $soal->id
            ]);
    }

    /**
     * Hapus soal beserta jawaban dan gambar terkait.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $soal = BankPertanyaan::with('jawaban')->findOrFail($id);

        // Kumpulkan semua HTML untuk identifikasi gambar
        $allHtml = [];
        $allHtml[] = $soal->pertanyaan;
        foreach ($soal->jawaban as $jw) {
            $allHtml[] = $jw->teks_jawaban;
        }

        $editorImages = $this->getEditorImagesFromMultipleHtml($allHtml);
        $soalId = $soal->id;
        $soal->delete(); // cascade akan menghapus jawaban

        // Hapus gambar yang hanya digunakan oleh soal ini
        foreach ($editorImages as $filename) {
            if (!$this->isEditorImageUsedInDatabase($filename, $soalId)) {
                $this->deleteEditorImageFile($filename);
            }
        }

        $this->cleanOrphanEditorImages();

        return redirect()
            ->back()
            ->with('success', 'Soal berhasil dihapus!');
    }

    // ========== TINYMCE UPLOAD ==========

    public function uploadTinyMceImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $manager = ImageManager::usingDriver(Driver::class);

        $file = $request->file('file');
        $type = $request->get('type', 'soal');
        $prefix = $type === 'jawaban' ? 'jawaban_' : 'soal_';
        $filename = uniqid($prefix, true) . '.jpg';

        $image = $manager->decode($file->getPathname());
        $image->scaleDown(width: 900);

        $folder = storage_path('app/public/dokumen/gambar');
        if (!file_exists($folder)) {
            mkdir($folder, 0775, true);
        }

        $path = $folder . '/' . $filename;
        $image->save($path, quality: 45);

        // Upload ke R2
        Storage::disk('r2')->putFileAs('dokumen/gambar', new \Illuminate\Http\File($path), $filename);

        // Hapus file sementara lokal
        if (file_exists($path)) {
            unlink($path);
        }

        return response()->json([
            'location' => Storage::disk('r2')->url('dokumen/gambar/' . $filename)
        ]);
    }

    // ========== PEMBERSIHAN GAMBAR EDITOR ==========

    private function getEditorImagesFromHtml(?string $html): array
    {
        preg_match_all('/(?:soal_|jawaban_)[a-zA-Z0-9\._\-]+\.jpg/', $html ?? '', $matches);
        return collect($matches[0] ?? [])
            ->map(fn($file) => basename($file))
            ->unique()
            ->values()
            ->toArray();
    }

    private function getEditorImagesFromMultipleHtml(array $htmlList): array
    {
        $images = [];
        foreach ($htmlList as $html) {
            $images = array_merge($images, $this->getEditorImagesFromHtml($html));
        }
        return collect($images)->unique()->values()->toArray();
    }

    private function deleteEditorImageFile(string $filename): void
    {
        $r2Path = 'dokumen/gambar/' . $filename;
        if (Storage::disk('r2')->exists($r2Path)) {
            Storage::disk('r2')->delete($r2Path);
        }
    }

    private function isEditorImageUsedInDatabase(string $filename, ?int $exceptSoalId = null): bool
    {
        // Cek di tabel bank_pertanyaan
        $usedInSoal = BankPertanyaan::query()
            ->when($exceptSoalId, fn($q) => $q->where('id', '!=', $exceptSoalId))
            ->where('pertanyaan', 'like', '%' . $filename . '%')
            ->exists();

        // Cek di tabel bank_jawaban
        $usedInJawaban = BankJawaban::query()
            ->where('teks_jawaban', 'like', '%' . $filename . '%')
            ->exists();

        return $usedInSoal || $usedInJawaban;
    }

    private function cleanUnusedEditorImages(?string $oldHtml, ?string $newHtml, ?int $soalId = null): void
    {
        $oldImages = $this->getEditorImagesFromHtml($oldHtml);
        $newImages = $this->getEditorImagesFromHtml($newHtml);
        $deletedImages = array_diff($oldImages, $newImages);

        foreach ($deletedImages as $filename) {
            if (!$this->isEditorImageUsedInDatabase($filename, $soalId)) {
                $this->deleteEditorImageFile($filename);
            }
        }
    }

    private function cleanOrphanEditorImages(): void
    {
        $files = Storage::disk('r2')->files('dokumen/gambar');

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            if (!$this->isEditorImageUsedInDatabase($filename)) {
                Storage::disk('r2')->delete($filePath);
            }
        }
    }
}
