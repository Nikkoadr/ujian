@extends('layouts.app')

@section('content')
<style>
    /* Global Soft UI */
    .card {
        border-radius: 15px;
        border: none;
        transition: 0.3s;
    }

    .shadow-soft {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important;
    }

    /* Upload Area Styling */
    .upload-area {
        display: block;
        cursor: pointer;
        background: #f8f9fc;
        border: 2px dashed #d1d3e2;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: 0.3s;
    }

    .upload-area:hover {
        border-color: #4e73df;
        background: #f1f3f9;
    }

    .hidden-input {
        display: none;
    }

    /* TinyMCE & Modal Fixes */
    .tox-tinymce {
        border-radius: 12px !important;
        border: 1px solid #e3e6f0 !important;
    }

    .tox-tinymce-aux {
        z-index: 9999 !important;
    }

    /* Input & Choice Styling */
    .pg-item {
        border-radius: 12px;
        border: 1px solid #e3e6f0;
        transition: 0.2s;
        background: #fff;
    }

    .pg-item:hover {
        border-color: #4e73df;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .form-control {
        border-radius: 10px;
    }

    /* Edit Jawaban Card */
    .edit-jw-card {
        border: 1px solid #e3e6f0;
        border-left: 5px solid #4e73df;
        border-radius: 12px;
        background: #fff;
        padding: 15px;
        margin-bottom: 15px;
    }

    .header-card {
        border-radius: 18px;
        background: linear-gradient(135deg, #4e73df, #224abe);
        color: white;
        overflow: hidden;
    }

    .header-card .icon-box {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        background: rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .section-title {
        font-size: 13px;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: 700;
        color: #858796;
    }

    .accordion .card {
        border-radius: 12px !important;
    }

    .question-item {
        transition: 0.2s;
    }

    .question-item:hover {
        background: #f8f9fc;
    }

    .answer-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>

<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="card header-card shadow-soft mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">

                <div class="col-lg-8">
                    <div class="d-flex align-items-center">

                        <div class="icon-box mr-4">
                            <i class="fas fa-book-open"></i>
                        </div>

                        <div>
                            <h2 class="font-weight-bold mb-2">
                                Manajemen Bank Soal
                            </h2>

                            <div class="d-flex flex-wrap align-items-center">

                                <span class="badge badge-light text-primary px-3 py-2 rounded-pill mr-2">
                                    <i class="fas fa-layer-group mr-1"></i>
                                    Mata Pelajaran
                                </span>

                                <h5 class="m-0 font-weight-bold text-white">
                                    {{ $mapel->nama_mapel ?? $mapel->nama }}
                                </h5>

                            </div>

                            <p class="mt-3 mb-0 text-white-50">
                                Kelola soal ujian dan jawaban pilihan ganda untuk mata pelajaran ini
                            </p>
                        </div>

                    </div>
                </div>

                <div class="col-lg-4 text-lg-right mt-4 mt-lg-0">

                    <div class="d-inline-flex align-items-center bg-white px-4 py-3 rounded-lg shadow-sm">

                        <div class="mr-3 text-primary">
                            <i class="fas fa-database fa-2x"></i>
                        </div>

                        <div class="text-left">
                            <div class="small text-muted font-weight-bold text-uppercase">
                                Total Soal
                            </div>

                            <div class="h3 font-weight-bold text-dark mb-0">
                                {{ count($soals) }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="row">

        <!-- FORM TAMBAH SOAL -->
        <div class="col-lg-7 mb-4">

            <div class="card shadow-soft">

                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">

                    <div class="d-flex align-items-center">

                        <div class="bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center"
                             style="width: 45px; height: 45px;">
                            <i class="fas fa-plus"></i>
                        </div>

                        <div>
                            <h5 class="font-weight-bold text-dark mb-1">
                                Tambah Butir Soal
                            </h5>

                            <small class="text-muted">
                                Tambahkan pertanyaan beserta jawaban pilihan ganda
                            </small>
                        </div>

                    </div>

                </div>

                <div class="card-body p-4">

                    <form action="{{ route('soal.store', $mapel->id) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <!-- PERTANYAAN -->
                        <div class="form-group mb-4">

                            <div class="d-flex justify-content-between align-items-center mb-2">

                                <label class="section-title m-0">
                                    Pertanyaan
                                </label>

                            </div>

                            <textarea name="pertanyaan"
                                      id="editor_tambah"
                                      class="form-control mb-3"></textarea>

                            <label class="upload-area mt-3">

                                <input type="file"
                                       name="gambar_soal"
                                       class="hidden-input"
                                       onchange="updateLabel(this)">

                                <i class="fas fa-image text-primary mb-2 d-block"
                                   style="font-size: 24px;"></i>

                                <span class="file-label font-weight-bold small text-uppercase text-primary">
                                    Lampirkan Gambar Soal (Opsional)
                                </span>

                            </label>

                        </div>

                        <!-- PILIHAN GANDA -->
                        <div id="section-pg" class="mb-4">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <label class="section-title m-0">
                                    Pilihan Ganda
                                </label>

                                <small class="text-muted">
                                    Paste di A untuk split otomatis
                                </small>

                            </div>

                            @foreach(['A', 'B', 'C', 'D', 'E'] as $i => $l)

                            <div class="pg-item p-3 mb-3 shadow-sm">

                                <div class="d-flex align-items-start">

                                    <!-- RADIO -->
                                    <div class="pt-2 mr-3">
                                        <input type="radio"
                                               name="kunci_jawaban"
                                               value="{{ $i }}"
                                               required>
                                    </div>

                                    <!-- LABEL -->
                                    <div class="mr-3">
                                        <div class="answer-badge bg-primary text-white">
                                            {{ $l }}
                                        </div>
                                    </div>

                                    <!-- TEXT -->
                                    <div class="flex-grow-1">

                                        <textarea name="jawaban[]"
                                                  class="form-control input-jawaban"></textarea>

                                        <div class="d-flex align-items-center mt-2">

                                            <label class="mb-0 mr-3"
                                                   style="cursor: pointer;">

                                                <input type="file"
                                                       name="gambar_jawaban[]"
                                                       class="hidden-input"
                                                       onchange="updateLabelSmall(this)">

                                                <small class="text-primary font-weight-bold">
                                                    <i class="fas fa-camera mr-1"></i>
                                                    Tambah Gambar
                                                </small>

                                            </label>

                                            <div class="small text-success font-weight-bold file-status"
                                                 style="display:none;font-size:11px;"></div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            @endforeach

                        </div>

                        <!-- BUTTON -->
                        <div class="text-right border-top pt-4">

                            <input type="hidden"
                                   name="jenis_soal"
                                   value="pg">

                            <button type="submit"
                                    class="btn btn-primary px-5 py-2 font-weight-bold shadow rounded-pill">

                                <i class="fas fa-save mr-2"></i>
                                SIMPAN DATA

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <!-- BANK SOAL -->
        <div class="col-lg-5 mb-4">

            <div class="card shadow-soft border-0">

                <!-- HEADER -->
                <div class="card-header bg-white py-4 px-4 border-0">

                    <div class="d-flex justify-content-between align-items-center">

                        <div class="d-flex align-items-center">

                            <div class="bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center"
                                 style="width: 42px; height: 42px;">
                                <i class="fas fa-database"></i>
                            </div>

                            <div>
                                <h5 class="m-0 font-weight-bold text-dark">
                                    Bank Soal
                                </h5>

                                <small class="text-muted">
                                    Daftar soal tersimpan
                                </small>
                            </div>

                        </div>

                        <span class="badge badge-primary badge-pill px-3 py-2">
                            {{ count($soals) }} Soal
                        </span>

                    </div>

                </div>

                <!-- BODY -->
                <div class="card-body p-3"
                     style="max-height: 80vh; overflow-y: auto;">

                    <div class="accordion" id="accSoal">

                        @foreach($soals as $item)

                        <div class="card shadow-none border mb-3 overflow-hidden question-item">

                            <div class="card-header p-0 border-0 bg-white">

                                <button class="btn btn-block text-left p-3 d-flex justify-content-between align-items-center shadow-none"
                                        data-toggle="collapse"
                                        data-target="#c{{ $item->id }}">

                                    <div class="d-flex align-items-center">

                                        <div class="badge badge-light text-primary px-3 py-2 mr-3">
                                            {{ $loop->iteration }}
                                        </div>

                                        <span class="small font-weight-bold text-dark text-truncate pr-2">
                                            {{ Str::limit(strip_tags($item->pertanyaan), 55) }}
                                        </span>

                                    </div>

                                    <i class="fas fa-chevron-down text-muted"
                                       style="font-size: 10px"></i>

                                </button>

                            </div>

                            <div id="c{{ $item->id }}"
                                 class="collapse"
                                 data-parent="#accSoal">

                                <div class="card-body bg-light border-top">

                                    <!-- ACTION -->
                                    <div class="d-flex justify-content-end mb-3">

                                        <button class="btn btn-sm btn-white shadow-sm text-primary mr-2 btn-edit-soal"
                                            data-id="{{ $item->id }}"
                                            data-pertanyaan="{{ $item->pertanyaan }}"
                                            data-gambar="{{ $item->gambar_soal }}"
                                            data-jawaban='@json($item->jawaban)'
                                            data-url="{{ route('soal.update', $item->id) }}">

                                            <i class="fas fa-pen mr-1"></i>
                                            Edit

                                        </button>

                                        <form action="{{ route('soal.destroy', $item->id) }}"
                                              method="POST">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-white shadow-sm text-danger"
                                                    onclick="return confirm('Hapus soal?')">

                                                <i class="fas fa-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                    <!-- PERTANYAAN -->
                                    <div class="small text-dark mb-4 math-render">
                                        {!! $item->pertanyaan !!}
                                    </div>

                                    <!-- JAWABAN -->
                                    <div class="list-group list-group-flush">

                                        @foreach($item->jawaban as $idx => $jw)

                                        <div class="list-group-item bg-transparent px-0 py-2 border-0 d-flex align-items-start">

                                            <span class="answer-badge {{ $jw->jawaban_benar ? 'bg-success text-white' : 'bg-light text-dark' }} mr-3">
                                                {{ chr(65 + $idx) }}
                                            </span>

                                            <div class="small math-render {{ $jw->jawaban_benar ? 'text-success font-weight-bold' : 'text-dark' }}">
                                                {!! $jw->teks_jawaban !!}
                                            </div>

                                        </div>

                                        @endforeach

                                    </div>

                                </div>

                            </div>

                        </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- MODAL EDIT -->
<div class="modal fade"
     id="modalEditSoal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content shadow-lg">

            <div class="modal-header border-0 p-4">

                <h5 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-edit mr-2 text-primary"></i>
                    Edit Soal
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form id="formEditSoal"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="modal-body px-4 pt-0">

                    <div class="form-group mb-4">

                        <label class="section-title">
                            Konten Pertanyaan
                        </label>

                        <textarea name="pertanyaan"
                                  id="editor_edit_soal"
                                  class="form-control"></textarea>

                        <div id="prev_g_s"
                             class="mt-3 d-none text-center bg-light p-3 rounded border">

                            <img src=""
                                 id="render_g_s"
                                 class="img-thumbnail mb-2"
                                 style="max-height: 100px">

                            <div class="custom-control custom-checkbox">

                                <input type="checkbox"
                                       class="custom-control-input"
                                       name="hapus_gambar_soal"
                                       id="hgs">

                                <label class="custom-control-label text-danger font-weight-bold small"
                                       for="hgs">

                                    Hapus Gambar Soal

                                </label>

                            </div>

                        </div>

                    </div>

                    <div id="e_j"></div>

                </div>

                <div class="modal-footer border-0 p-4 bg-light">

                    <button type="submit"
                            class="btn btn-primary px-5 font-weight-bold shadow rounded-pill">

                        <i class="fas fa-save mr-2"></i>
                        SIMPAN PERUBAHAN

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
function handlePasteSplit(editor, e) {

    let clipboardData = (e.clipboardData || window.clipboardData).getData('text');

    let lines = clipboardData
        .split(/\n|[a-eA-E][\.\)]\s?/)
        .map(s => s.trim())
        .filter(Boolean);

    if (lines.length > 1) {

        e.preventDefault();

        let targetClass = editor.getElement().classList.contains('input-jawaban')
            ? '.input-jawaban'
            : '.edit-tiny-jawaban';

        let allEditors = [];

        $(targetClass).each(function () {

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
                editor.getElement().classList.contains('input-jawaban') ||
                editor.getElement().classList.contains('edit-tiny-jawaban')
            ) {
                handlePasteSplit(editor, e);
            }

        });

    }

};

function updateLabel(input) {

    if (input.files[0]) {

        $(input)
            .closest('.card-body, .modal-body')
            .find('.file-label')
            .text(input.files[0].name);

    }

}

function updateLabelSmall(input) {

    if (input.files[0]) {

        $(input)
            .closest('.pg-item, .edit-jw-card')
            .find('.file-status')
            .text(input.files[0].name)
            .fadeIn();

    }

}

$(document).ready(function () {

    $(document).on('focusin', function (e) {

        if ($(e.target).closest(".tox-tinymce, .tox-tinymce-aux").length)
            e.stopImmediatePropagation();

    });

    tinymce.init({
        ...baseConfig,
        selector: '#editor_tambah',
        height: 250
    });

    tinymce.init({
        ...baseConfig,
        selector: '.input-jawaban',
        height: 140
    });

    $('.btn-edit-soal').click(function () {

        const d = $(this).data();

        $('#formEditSoal').attr('action', d.url);

        tinymce.remove('#editor_edit_soal');

        $('#editor_edit_soal').val(d.pertanyaan);

        if (d.gambar) {

            $('#render_g_s').attr('src', '/storage/soal/' + d.gambar);

            $('#prev_g_s').removeClass('d-none');

        } else {

            $('#prev_g_s').addClass('d-none');

        }

        let h = '';

        d.jawaban.forEach((jw, i) => {

            let check = jw.jawaban_benar ? 'checked' : '';

            h += `
            <div class="edit-jw-card shadow-sm">

                <div class="input-group mb-2">

                    <div class="input-group-prepend">

                        <span class="input-group-text bg-transparent border-0">

                            <input type="radio"
                                   name="kunci_jawaban"
                                   value="${jw.id}"
                                   ${check}
                                   required>

                            <b class="ml-2">${String.fromCharCode(65+i)}</b>

                        </span>

                    </div>

                    <div class="flex-grow-1">

                        <textarea name="jawaban[${jw.id}]"
                                  id="ed_jw_${jw.id}"
                                  class="form-control edit-tiny-jawaban">${jw.teks_jawaban}</textarea>

                    </div>

                </div>

                <div class="ml-5">

                    <label class="mb-0"
                           style="cursor: pointer;">

                        <input type="file"
                               name="gambar_jawaban_edit[${jw.id}]"
                               class="hidden-input"
                               onchange="updateLabelSmall(this)">

                        <small class="text-primary font-weight-bold">
                            <i class="fas fa-camera mr-1"></i>
                            Ganti Gambar
                        </small>

                    </label>

                    <span class="file-status small ml-2 text-success"
                          style="display:none"></span>

                </div>

            </div>`;
        });

        $('#e_j').html(h);

        $('#modalEditSoal').modal('show');

    });

    $('#modalEditSoal').on('shown.bs.modal', function () {

        tinymce.init({
            ...baseConfig,
            selector: '#editor_edit_soal',
            height: 200
        });

        tinymce.init({
            ...baseConfig,
            selector: '.edit-tiny-jawaban',
            height: 140
        });

    });

    $('#modalEditSoal').on('hidden.bs.modal', function () {

        tinymce.remove('#editor_edit_soal');
        tinymce.remove('.edit-tiny-jawaban');

    });

    $('#accSoal').on('shown.bs.collapse', function () {

        if (window.MathJax)
            MathJax.typesetPromise();

    });

});
</script>

@endpush