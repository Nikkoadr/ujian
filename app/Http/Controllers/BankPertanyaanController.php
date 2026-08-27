<?php

namespace App\Http\Controllers;

use App\Models\Bank_soal;
use App\Models\Bank_pertanyaan;
use App\Models\Bank_jawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BankPertanyaanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman manajemen soal untuk bank soal tertentu
     */
    public function index(Bank_soal $bank_soal)
    {
        $bank_soal = Bank_soal::findOrFail($bank_soal->id);
        $bank_pertanyaan = Bank_pertanyaan::with('jawaban')
            ->where('bank_soal_id', $bank_soal->id)
            ->orderBy('created_at', 'asc')
            ->paginate(60);
        return view('bank_soal.bank_pertanyaan', compact('bank_soal', 'bank_pertanyaan'));
    }

    public function store(Request $request, $bank_soal_id)
    {
        $request->validate([
            'pertanyaan'    => 'required',
            'jenis_soal'    => 'required',
            'jawaban'       => 'nullable|array',
            'kunci_jawaban' => 'nullable',
        ]);
        $soal = new Bank_pertanyaan();
        $soal->bank_soal_id = $bank_soal_id;
        $soal->pertanyaan = $request->pertanyaan;
        $soal->jenis_soal = $request->jenis_soal;
        $soal->bobot_nilai = $request->bobot_nilai ?? 1;
        $soal->save();
        if ($request->jenis_soal == 'pg' && $request->has('jawaban')) {
            foreach ($request->jawaban as $key => $teks) {
                $jawaban = new Bank_jawaban();
                $jawaban->bank_pertanyaan_id = $soal->id;
                $jawaban->teks_jawaban = $teks;
                $jawaban->jawaban_benar = ((string) $request->kunci_jawaban === (string) $key);
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
        $soal = Bank_pertanyaan::with('bank_jawaban')->findOrFail($id);
        return view('bank_soal.edit_pertanyaan', compact('soal'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'jawaban'    => 'nullable|array',
        ]);

        $soal = Bank_pertanyaan::with('bank_jawaban')->findOrFail($id);

        $oldPertanyaan = $soal->pertanyaan;
        $oldJawabanHtml = $soal->bank_jawaban->pluck('teks_jawaban')->toArray();

        $soal->pertanyaan = $request->pertanyaan;
        $soal->save();

        if ($request->has('jawaban')) {
            foreach ($request->jawaban as $jawabanId => $teks) {
                $jawaban = Bank_jawaban::where('bank_pertanyaan_id', $soal->id)
                    ->where('id', $jawabanId)
                    ->first();

                if (!$jawaban) {
                    continue;
                }

                $jawaban->teks_jawaban = $teks;
                $jawaban->jawaban_benar = ((string) $request->kunci_jawaban === (string) $jawabanId);
                $jawaban->save();
            }
        }

        $newJawabanHtml = Bank_jawaban::where('bank_pertanyaan_id', $soal->id)
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
        $soal = Bank_pertanyaan::with('bank_jawaban')->findOrFail($id);

        $allHtml = [];
        $allHtml[] = $soal->pertanyaan;
        foreach ($soal->bank_jawaban as $jw) {
            $allHtml[] = $jw->teks_jawaban;
        }

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

        Storage::disk('r2')->putFileAs('dokumen/gambar', new \Illuminate\Http\File($path), $filename);

        if (file_exists($path)) {
            unlink($path);
        }

        return response()->json([
            'location' => Storage::disk('r2')->url('dokumen/gambar/' . $filename)
        ]);
    }


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
        $usedInSoal = Bank_pertanyaan::query()
            ->when($exceptSoalId, fn($q) => $q->where('id', '!=', $exceptSoalId))
            ->where('pertanyaan', 'like', '%' . $filename . '%')
            ->exists();

        $usedInJawaban = Bank_jawaban::query()
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
