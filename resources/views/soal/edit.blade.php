@extends('layouts.app')

@section('title')
    Edit Soal
@endsection

@section('content')

<style>
    .card {
        border-radius: 15px;
        border: none;
    }

    .shadow-soft {
        box-shadow: 0 10px 30px rgba(0,0,0,.05) !important;
    }

    .section-title {
        font-size: 13px;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: 700;
        color: #858796;
    }

    .edit-jw-card {
        border: 1px solid #e3e6f0;
        border-left: 5px solid #4e73df;
        border-radius: 12px;
        background: #fff;
        padding: 18px;
    }

    .answer-badge {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .tox-tinymce {
        border-radius: 12px !important;
        border: 1px solid #e3e6f0 !important;
    }

    .upload-area {
        display: block;
        cursor: pointer;
        background: #f8f9fc;
        border: 2px dashed #d1d3e2;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: .3s;
    }

    .upload-area:hover {
        border-color: #4e73df;
        background: #f1f3f9;
    }

    .hidden-input {
        display: none;
    }

    .file-status {
        display: none;
    }

    .preview-image {
        max-height: 150px;
        object-fit: contain;
    }

    .bank-image {
        cursor: zoom-in;
        transition: .2s;
    }

    .bank-image:hover {
        transform: scale(1.02);
    }
</style>

<div class="container-fluid py-4">

    <div class="card shadow-soft">

        <!-- HEADER -->
        <div class="card-header bg-white border-0 py-4 px-4">

            <div class="d-flex align-items-center">

                <div class="bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center"
                     style="width:45px;height:45px;">

                    <i class="fas fa-edit"></i>

                </div>

                <div>

                    <h4 class="font-weight-bold mb-1">
                        Edit Soal
                    </h4>

                    <small class="text-muted">
                        Perbarui pertanyaan, jawaban, dan gambar
                    </small>

                </div>

            </div>

        </div>

        <!-- BODY -->
        <div class="card-body p-4">

            <form action="{{ route('soal.update', $soal->id) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <!-- PERTANYAAN -->
                <div class="form-group mb-5">

                    <label class="section-title mb-3">
                        Pertanyaan
                    </label>

                    <textarea name="pertanyaan"
                              id="editor_edit_soal"
                              class="form-control">{!! $soal->pertanyaan !!}</textarea>

                    <!-- UPLOAD GAMBAR -->
                    <label class="upload-area mt-3">

                        <input type="file"
                               name="gambar_soal"
                               class="hidden-input"
                               onchange="updateLabel(this)">

                        <i class="fas fa-image text-primary mb-2 d-block"
                           style="font-size:24px;"></i>

                        <span class="file-label font-weight-bold small text-uppercase text-primary">
                            Ganti / Tambah Gambar Soal
                        </span>

                    </label>

                    <!-- PREVIEW GAMBAR LAMA -->
                    @if($soal->gambar_soal)

                    <div class="mt-4 p-3 border rounded bg-light">

                        <img src="{{ asset('storage/soal/' . $soal->gambar_soal) }}"
                             class="img-thumbnail preview-image bank-image mb-3">

                        <div class="custom-control custom-checkbox">

                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="hapus_gambar_soal"
                                   name="hapus_gambar_soal">

                            <label class="custom-control-label text-danger small font-weight-bold"
                                   for="hapus_gambar_soal">

                                Hapus gambar soal

                            </label>

                        </div>

                    </div>

                    @endif

                </div>

                <!-- JAWABAN -->
                <div class="mb-4">

                    <label class="section-title mb-3">
                        Pilihan Jawaban
                    </label>

                    @foreach($soal->jawaban as $i => $jw)

                    <div class="edit-jw-card shadow-sm mb-4">

                        <div class="d-flex align-items-start">

                            <!-- RADIO -->
                            <div class="mr-3 pt-2">

                                <input type="radio"
                                       name="kunci_jawaban"
                                       value="{{ $jw->id }}"
                                       {{ $jw->jawaban_benar ? 'checked' : '' }}
                                       required>

                            </div>

                            <!-- LABEL -->
                            <div class="mr-3">

                                <div class="answer-badge bg-primary text-white">
                                    {{ chr(65+$i) }}
                                </div>

                            </div>

                            <!-- CONTENT -->
                            <div class="flex-grow-1">

                                <textarea name="jawaban[{{ $jw->id }}]"
                                          id="ed_jw_{{ $jw->id }}"
                                          class="form-control edit-tiny-jawaban">{!! $jw->teks_jawaban !!}</textarea>

                                <!-- PREVIEW GAMBAR JAWABAN -->
                                @if($jw->gambar_jawaban)

                                <div class="mt-3">

                                    <img src="{{ asset('storage/jawaban/' . $jw->gambar_jawaban) }}"
                                         class="img-thumbnail preview-image bank-image">

                                </div>

                                @endif

                                <!-- UPLOAD -->
                                <div class="mt-3">

                                    <label class="mb-0"
                                           style="cursor:pointer;">

                                        <input type="file"
                                               name="gambar_jawaban_edit[{{ $jw->id }}]"
                                               class="hidden-input"
                                               onchange="updateLabelSmall(this)">

                                        <small class="text-primary font-weight-bold">

                                            <i class="fas fa-camera mr-1"></i>
                                            Ganti Gambar

                                        </small>

                                    </label>

                                    <span class="file-status small ml-2 text-success font-weight-bold"></span>

                                </div>

                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

                <!-- BUTTON -->
                <div class="text-right border-top pt-4">

                    <a href="{{ url()->previous() }}"
                       class="btn btn-light rounded-pill px-4 mr-2">

                        Kembali

                    </a>

                    <button type="submit"
                            class="btn btn-primary rounded-pill px-5 shadow">

                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>

<script id="MathJax-script"
        async
        src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<script>

function updateLabel(input) {

    if (input.files[0]) {

        $(input)
            .closest('.form-group')
            .find('.file-label')
            .text(input.files[0].name);

        const reader = new FileReader();

        reader.onload = function(e) {

            if ($('#previewNewSoal').length === 0) {

                $('.upload-area').after(`
                    <div class="mt-3">
                        <img id="previewNewSoal"
                             class="img-thumbnail preview-image">
                    </div>
                `);

            }

            $('#previewNewSoal').attr('src', e.target.result);

        }

        reader.readAsDataURL(input.files[0]);

    }

}

function updateLabelSmall(input) {

    if (input.files[0]) {

        $(input)
            .closest('.flex-grow-1')
            .find('.file-status')
            .text(input.files[0].name)
            .fadeIn();

    }

}

function handlePasteSplit(editor, e) {

    let clipboardData = (e.clipboardData || window.clipboardData).getData('text');

    let lines = clipboardData
        .split(/\n|[a-eA-E][\.\)]\s?/)
        .map(s => s.trim())
        .filter(Boolean);

    if (lines.length > 1) {

        e.preventDefault();

        let allEditors = [];

        $('.edit-tiny-jawaban').each(function () {

            let instance = tinymce.get(this.id);

            if (instance) allEditors.push(instance);

        });

        lines.forEach((text, i) => {

            if (allEditors[i]) {

                allEditors[i].setContent(text);
                allEditors[i].save();

            }

        });

    }

}

const baseConfig = {

    menubar: false,

    plugins: 'lists link code table emoticons',

    toolbar: 'bold italic underline | forecolor backcolor | bullist numlist | removeformat | code',

    content_style: 'body { font-family:Inter,Arial,sans-serif; font-size:14px }',

    setup: function(editor) {

        editor.on('change', function () {
            editor.save();
        });

        editor.on('paste', function (e) {

            if (
                editor.getElement().classList.contains('edit-tiny-jawaban')
            ) {
                handlePasteSplit(editor, e);
            }

        });

    }

};

$(document).ready(function () {

    tinymce.init({
        ...baseConfig,
        selector: '#editor_edit_soal',
        height: 260
    });

    tinymce.init({
        ...baseConfig,
        selector: '.edit-tiny-jawaban',
        height: 140
    });

    if (window.MathJax) {
        MathJax.typesetPromise();
    }

});

</script>

@endpush