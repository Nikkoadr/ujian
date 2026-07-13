<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Jawaban;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

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
            ->orderBy('created_at', 'asc')
            ->paginate(60);
        return view('soal.index', compact('mapel', 'soals'));
    }

    public function store(Request $request, $mapel_id)
    {
        $request->validate([
            'pertanyaan'    => 'required',
            'jenis_soal'    => 'required',
            'jawaban'       => 'nullable|array',
            'kunci_jawaban' => 'nullable',
        ]);

        $soal = new Soal();
        $soal->mapel_id = $mapel_id;
        $soal->pertanyaan = $request->pertanyaan;
        $soal->jenis_soal = $request->jenis_soal;
        $soal->bobot_nilai = $request->bobot_nilai ?? 1;

        // Tidak ada penyimpanan gambar soal manual
        $soal->save();

        if ($request->jenis_soal == 'pg' && $request->has('jawaban')) {
            foreach ($request->jawaban as $key => $teks) {
                $jawaban = new Jawaban();
                $jawaban->soal_id = $soal->id;
                $jawaban->teks_jawaban = $teks;
                $jawaban->jawaban_benar = ((string) $request->kunci_jawaban === (string) $key);
                // Tidak ada penyimpanan gambar jawaban manual
                $jawaban->save();
            }
        }

        $this->cleanOrphanEditorImages();

        return redirect()
            ->back()
            ->with('success', 'Soal berhasil disimpan!');
    }

    public function edit($id)
    {
        $soal = Soal::with('jawaban')->findOrFail($id);
        return view('soal.edit', compact('soal'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'jawaban'    => 'nullable|array',
        ]);

        $soal = Soal::with('jawaban')->findOrFail($id);

        $oldPertanyaan = $soal->pertanyaan;
        $oldJawabanHtml = $soal->jawaban->pluck('teks_jawaban')->toArray();

        $soal->pertanyaan = $request->pertanyaan;

        // Tidak ada update gambar soal manual
        $soal->save();

        if ($request->has('jawaban')) {
            foreach ($request->jawaban as $jawabanId => $teks) {
                $jawaban = Jawaban::where('soal_id', $soal->id)
                    ->where('id', $jawabanId)
                    ->first();

                if (!$jawaban) {
                    continue;
                }

                $jawaban->teks_jawaban = $teks;
                $jawaban->jawaban_benar = ((string) $request->kunci_jawaban === (string) $jawabanId);
                // Tidak ada update gambar jawaban manual
                $jawaban->save();
            }
        }

        $newJawabanHtml = Jawaban::where('soal_id', $soal->id)
            ->pluck('teks_jawaban')
            ->toArray();

        $oldHtml = implode(' ', array_merge([$oldPertanyaan], $oldJawabanHtml));
        $newHtml = implode(' ', array_merge([$request->pertanyaan], $newJawabanHtml));

        $this->cleanUnusedEditorImages($oldHtml, $newHtml, $soal->id);
        $this->cleanOrphanEditorImages();

        return redirect()
            ->route('soal.index', $soal->mapel_id)
            ->with([
                'success' => 'Soal dan Jawaban berhasil diperbarui!',
                'highlight' => $soal->id
            ]);
    }

    public function destroy($id)
    {
        $soal = Soal::with('jawaban')->findOrFail($id);

        $allHtml = [];
        $allHtml[] = $soal->pertanyaan;
        foreach ($soal->jawaban as $jw) {
            $allHtml[] = $jw->teks_jawaban;
        }

        // Tidak ada penghapusan file gambar manual (karena tidak disimpan)
        // Hanya hapus gambar yang diupload via editor jika tidak terpakai

        $editorImages = $this->getEditorImagesFromMultipleHtml($allHtml);
        $soalId = $soal->id;
        $soal->delete();

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

        return response()->json([
            'location' => asset('storage/dokumen/gambar/' . $filename)
        ]);
    }

    // ========== PEMBERSIHAN GAMBAR EDITOR ==========

    private function getEditorImagesFromHtml(?string $html): array
    {
        preg_match_all('/storage\/dokumen\/gambar\/([^"\']+)/', $html ?? '', $matches);
        return collect($matches[1] ?? [])
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
        $path = storage_path('app/public/dokumen/gambar/' . $filename);
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function isEditorImageUsedInDatabase(string $filename, ?int $exceptSoalId = null): bool
    {
        $usedInSoal = Soal::query()
            ->when($exceptSoalId, fn($q) => $q->where('id', '!=', $exceptSoalId))
            ->where('pertanyaan', 'like', '%' . $filename . '%')
            ->exists();

        $usedInJawaban = Jawaban::query()
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
        $folder = storage_path('app/public/dokumen/gambar');
        if (!is_dir($folder)) {
            return;
        }

        foreach (glob($folder . '/*') as $filePath) {
            $filename = basename($filePath);
            if (!$this->isEditorImageUsedInDatabase($filename)) {
                File::delete($filePath);
            }
        }
    }
}
