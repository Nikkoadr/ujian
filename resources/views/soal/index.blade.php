@extends('layouts.app')

@section('title')
    Manajemen Bank Soal - {{ $mapel->nama_mapel ?? $mapel->nama }}
@endsection

@section('styles')
    <link href="{{ asset('assets/css/style-soal.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row d-flex align-items-stretch">

        <div class="col-lg-7 mb-4">
            <div class="card shadow-soft h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center"
                             style="width: 45px; height: 45px;">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold text-dark mb-1">
                                Tambah Butir Soal {{ $mapel->nama_mapel ?? $mapel->nama }}
                            </h5>
                            <small class="text-muted">
                                Tambahkan pertanyaan beserta jawaban pilihan ganda
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- Hapus enctype karena tidak ada upload file manual --}}
                    <form action="{{ route('soal.store', $mapel->id) }}" method="POST">
                        @csrf

                        <div class="form-group mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="section-title m-0">Pertanyaan</label>
                            </div>
                            <textarea name="pertanyaan" id="editor_tambah" class="form-control mb-3"></textarea>
                            {{-- Bagian upload gambar soal dihapus --}}
                        </div>

                        <div id="section-pg" class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="section-title m-0">Pilihan Ganda</label>
                                <small class="text-muted">Paste di A untuk split otomatis</small>
                            </div>

                            @foreach(['A', 'B', 'C', 'D', 'E'] as $i => $l)
                                <div class="pg-item mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="mr-3 mt-1">
                                            <div class="custom-control custom-radio">
                                                <input type="radio"
                                                       name="kunci_jawaban"
                                                       value="{{ $i }}"
                                                       class="custom-control-input"
                                                       id="kunci_{{ $i }}">
                                                <label class="custom-control-label font-weight-bold" for="kunci_{{ $i }}">
                                                    {{ $l }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <textarea name="jawaban[]"
                                                      class="form-control input-jawaban"
                                                      id="jawaban_{{ $i }}"
                                                      placeholder="Tulis jawaban {{ $l }}"></textarea>
                                            {{-- Input file untuk gambar jawaban dihapus --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-right border-top pt-4">
                            <input type="hidden" name="jenis_soal" value="pg">
                            <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold shadow rounded-pill">
                                <i class="fas fa-save mr-2"></i> SIMPAN DATA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow-soft border-0 h-100 d-flex flex-column" style="min-height: 0;">
                <div class="card-header bg-white py-4 px-4 border-0 flex-shrink-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center"
                                 style="width: 42px; height: 42px;">
                                <i class="fas fa-database"></i>
                            </div>
                            <div>
                                <h5 class="m-0 font-weight-bold text-dark">Bank Soal</h5>
                                <small class="text-muted">Daftar soal tersimpan</small>
                            </div>
                        </div>
                        <span class="badge badge-primary badge-pill px-3 py-2">
                            {{ count($soals) }} Soal
                        </span>
                    </div>
                </div>

                <div class="card-body p-0 d-flex flex-column" style="flex: 1; min-height: 0; position: relative;">
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto; padding: 1.25rem;">
                        <div class="accordion" id="accSoal">
                            @foreach($soals as $item)
                                <x-soal-preview :item="$item" :loop="$loop" />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>

<script>
    window.MathJax = {
        tex: {
            inlineMath: [['\\(', '\\)']],
            displayMath: [['$$', '$$']],
            processEscapes: true
        },
        startup: {
            typeset: false
        }
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    function renderMath() {
        if (window.MathJax && MathJax.typesetPromise) {
            MathJax.typesetPromise().catch(function (err) {
                console.log('MathJax error:', err.message);
            });
        }
    }

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function convertWordMatrixToLatex(text) {
        return String(text).replace(/\(■\((.*?)\)\)/g, function(match, content) {
            let rows = content.split('@');
            let latexRows = rows.map(row => row.split('&').join(' & '));
            return '\\begin{bmatrix}' + latexRows.join(' \\\\ ') + '\\end{bmatrix}';
        });
    }

    function cleanPasteText(text) {
        text = String(text || '');
        text = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
        text = convertWordMatrixToLatex(text);
        return text;
    }

    function insertPlainText(editor, text) {
        text = cleanPasteText(text);
        editor.insertContent(
            escapeHtml(text).replace(/\n/g, '<br>')
        );
        editor.save();
        setTimeout(renderMath, 150);
    }

    function handlePasteSplit(editor, e) {
        const clipboard = e.clipboardData || window.clipboardData;
        if (!clipboard) return false;

        let text = clipboard.getData('text/plain') || clipboard.getData('text') || '';
        text = cleanPasteText(text);

        let lines = text
            .split(/\n|[a-eA-E][\.\)]\s+/)
            .map(s => s.trim())
            .filter(Boolean);

        if (lines.length > 1) {
            e.preventDefault();

            let allEditors = [];
            $('.input-jawaban').each(function () {
                let instance = tinymce.get(this.id);
                if (instance) {
                    allEditors.push(instance);
                }
            });

            lines.forEach((text, i) => {
                if (allEditors[i]) {
                    allEditors[i].setContent(
                        escapeHtml(text).replace(/\n/g, '<br>')
                    );
                    allEditors[i].save();
                }
            });

            setTimeout(renderMath, 150);
            return true;
        }
        return false;
    }

    const baseConfig = {
        menubar: false,
        plugins: 'lists link code table emoticons image',
        toolbar: `
            bold italic underline |
            forecolor backcolor |
            bullist numlist |
            table image |
            removeformat |
            code
        `,
        paste_as_text: false,
        paste_data_images: true,
        automatic_uploads: true,
        smart_paste: false,

        images_upload_url: "{{ route('soal.tinymce.upload') }}",
        images_upload_credentials: true,

images_upload_handler: function (blobInfo, progress) {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        const activeEditor = tinymce.activeEditor;
        const isJawaban = activeEditor.getElement().classList.contains('input-jawaban') || 
                          activeEditor.getElement().classList.contains('edit-tiny-jawaban');

        formData.append('file', blobInfo.blob(), blobInfo.filename());
        formData.append('type', isJawaban ? 'jawaban' : 'soal');

        fetch("{{ route('soal.tinymce.upload') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.location) {
                resolve(result.location);
            } else {
                reject('Upload gambar gagal');
            }
        })
        .catch(() => {
            reject('Upload gambar gagal');
        });
    });
},

        extended_valid_elements: '*[*]',
        valid_elements: '*[*]',
        verify_html: false,
        entity_encoding: 'raw',

        content_style: `
            body {
                font-family: Inter, Arial, sans-serif;
                font-size: 14px;
                line-height: 1.6;
            }
            img {
                max-width: 100%;
                height: auto;
            }
        `,

        paste_preprocess: function(plugin, args) {
            if (args.content.includes('<img')) {
                return;
            }
            let plainText = $('<div>').html(args.content).text();
            args.content = cleanPasteText(plainText);
        },

        setup: function(editor) {
            editor.on('paste', function(e) {
                const clipboard = e.clipboardData || window.clipboardData;
                if (!clipboard) return;

                const hasImage = Array.from(clipboard.items || []).some(item => {
                    return item.type && item.type.indexOf('image') === 0;
                });
                if (hasImage) return;

                const text = clipboard.getData('text/plain') || clipboard.getData('text') || '';

                if (editor.getElement().classList.contains('input-jawaban')) {
                    let handled = handlePasteSplit(editor, e);
                    if (!handled) {
                        e.preventDefault();
                        insertPlainText(editor, text);
                    }
                } else {
                    e.preventDefault();
                    insertPlainText(editor, text);
                }
            });

            editor.on('change keyup input SetContent', function () {
                editor.save();
                setTimeout(renderMath, 150);
            });
        }
    };

    function deleteSoal(id) {
        Swal.fire({
            title: 'Hapus Soal?',
            text: 'Data tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
                Toast.fire({
                    icon: 'info',
                    title: 'Sedang menghapus...'
                });
            }
        });
    }

    $(document).ready(function () {
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        @if($errors->any())
            Toast.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                text: 'Periksa kembali inputan Anda.'
            });
        @endif

        $(document).on('focusin', function (e) {
            if ($(e.target).closest('.tox-tinymce, .tox-tinymce-aux').length) {
                e.stopImmediatePropagation();
            }
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

        $('#accSoal').on('shown.bs.collapse', function () {
            setTimeout(renderMath, 150);
        });

        setTimeout(renderMath, 500);

        @if(session('highlight'))
            setTimeout(() => {
                const el = document.getElementById('soal-{{ session('highlight') }}');
                if (el) {
                    el.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }, 300);
        @endif
    });
</script>
@endpush