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
                        Perbarui pertanyaan dan jawaban
                    </small>
                </div>
            </div>
        </div>

        <!-- BODY -->
        <div class="card-body p-4">

            {{-- Hapus enctype karena tidak ada upload file manual --}}
            <form action="{{ route('soal.update', $soal->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- PERTANYAAN -->
                <div class="form-group mb-5">
                    <label class="section-title mb-3">Pertanyaan</label>
                    <textarea name="pertanyaan"
                              id="editor_edit_soal"
                              class="form-control">{!! $soal->pertanyaan !!}</textarea>
                    {{-- Tidak ada upload gambar soal manual --}}
                </div>

                <!-- JAWABAN -->
                <div class="mb-4">
                    <label class="section-title mb-3">Pilihan Jawaban</label>

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
                                {{-- Tidak ada upload gambar jawaban manual --}}
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

<script>

function renderMath() {
    if (window.MathJax && MathJax.typesetPromise) {
        MathJax.typesetPromise().catch(function(err) {
            console.log(err);
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
        $('.edit-tiny-jawaban').each(function () {
            let instance = tinymce.get(this.id);
            if (instance) allEditors.push(instance);
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
            const isJawaban =
                activeEditor.getElement().classList.contains('input-jawaban') ||
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

            if (editor.getElement().classList.contains('edit-tiny-jawaban')) {
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

    setTimeout(renderMath, 500);
});

</script>

@endpush