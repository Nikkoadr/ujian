@extends('layouts.app')

@section('content')
<style>
    /* Global Soft UI */
    .card { border-radius: 15px; border: none; transition: 0.3s; }
    .shadow-soft { box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important; }
    
    /* Custom Upload Box Styling */
    .upload-area {
        display: block; position: relative; cursor: pointer; background: #f8f9fc;
        border: 2px dashed #d1d3e2; border-radius: 12px; padding: 20px;
        text-align: center; transition: all 0.3s; margin-bottom: 0;
    }
    .upload-area:hover { background: #f1f3f9; border-color: #4e73df; color: #4e73df; }
    .upload-area i { font-size: 24px; margin-bottom: 8px; display: block; opacity: 0.7; }
    .upload-area span { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    input[type="file"].hidden-input { display: none; }

    /* Input & Choice Styling */
    .pg-item { border-radius: 12px; border: 1px solid #e3e6f0; transition: 0.2s; background: #fff; }
    .pg-item:hover { border-color: #4e73df; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .input-group-text { background: transparent; border: none; }
    .form-control { border-radius: 10px; }
    
    /* Accordion & List */
    .accordion .card { background: #f8f9fc; margin-bottom: 10px; }
    .btn-collapse { font-size: 14px; font-weight: 700; color: #4e73df; text-decoration: none !important; }
    
    /* Modal Enhancement */
    .modal-content { border-radius: 20px; border: none; overflow: hidden; }
    .modal-header { background: #fff; border-bottom: 1px solid #f1f3f9; }
    .edit-jw-card { 
        border: 1px solid #e3e6f0; border-left: 5px solid #4e73df; 
        border-radius: 12px; background: #fff; padding: 15px; margin-bottom: 15px;
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-soft mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h5 class="font-weight-bold m-0 text-dark">Tambah Butir Soal</h5>
                    </div>

                    <form action="{{ route('soal.store', $mapel->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted text-uppercase mb-2">Konten Pertanyaan</label>
                            <textarea name="pertanyaan" class="form-control mb-3 shadow-sm" rows="4" required placeholder="Tuliskan soal ujian di sini..."></textarea>
                            
                            <label class="upload-area">
                                <input type="file" name="gambar_soal" class="hidden-input" onchange="updateLabel(this)">
                                <i class="fas fa-image text-primary"></i>
                                <span class="file-label">Lampirkan Gambar Soal (Opsional)</span>
                            </label>
                        </div>

                        <div id="section-pg" class="mb-4">
                            <label class="small font-weight-bold text-muted text-uppercase d-block mb-3">Pilihan Ganda & Kunci Jawaban</label>
                            @foreach(['A', 'B', 'C', 'D', 'E'] as $i => $l)
                            <div class="pg-item p-2 mb-2 shadow-sm">
                                <div class="input-group align-items-center">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="radio" name="kunci_jawaban" value="{{ $i }}" required style="transform: scale(1.2)">
                                            <b class="ml-3 text-dark">{{ $l }}</b>
                                        </div>
                                    </div>
                                    <input type="text" name="jawaban[]" class="form-control border-0 input-jawaban shadow-none" placeholder="Jawaban {{ $l }}...">
                                    <label class="mb-0 mx-2" style="cursor: pointer;">
                                        <input type="file" name="gambar_jawaban[]" class="hidden-input" onchange="updateLabelSmall(this)">
                                        <i class="fas fa-camera text-muted hover-primary"></i>
                                    </label>
                                </div>
                                <div class="small text-primary font-weight-bold mt-1 file-status" style="margin-left: 55px; display:none; font-size: 10px;"></div>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-top pt-4">
                            <div class="badge badge-light p-2 px-3 text-primary font-weight-bold" style="border-radius: 8px;">
                                <i class="fas fa-tasks mr-1"></i> Tipe: Pilihan Ganda
                                <input type="hidden" name="jenis_soal" value="pg">
                            </div>
                            <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow rounded-pill">SIMPAN DATA</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-soft border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-database mr-2 text-primary"></i>Bank Soal</h6>
                    <span class="badge badge-primary badge-pill">{{ count($soals) }} Soal</span>
                </div>
                <div class="card-body p-3" style="max-height: 80vh; overflow-y: auto;">
                    <div class="accordion" id="accSoal">
                        @foreach($soals as $item)
                        <div class="card shadow-none">
                            <div class="card-header p-0 border-0">
                                <button class="btn btn-block text-left p-3 d-flex justify-content-between align-items-center shadow-none" data-toggle="collapse" data-target="#c{{ $item->id }}">
                                    <span class="text-truncate small font-weight-bold pr-2 text-dark">{{ $loop->iteration }}. {{ Str::limit($item->pertanyaan, 40) }}</span>
                                    <i class="fas fa-chevron-down text-muted" style="font-size: 10px"></i>
                                </button>
                            </div>
                            <div id="c{{ $item->id }}" class="collapse" data-parent="#accSoal">
                                <div class="card-body bg-white pt-0 rounded-bottom">
                                    <div class="d-flex justify-content-end mb-3 pt-2 border-top">
                                        <button class="btn btn-sm btn-light text-primary font-weight-bold mr-2 btn-edit-soal" 
                                            data-id="{{ $item->id }}" data-pertanyaan="{{ $item->pertanyaan }}" 
                                            data-gambar="{{ $item->gambar_soal }}" data-jawaban='@json($item->jawaban)' 
                                            data-url="{{ route('soal.update', $item->id) }}">
                                            <i class="fas fa-pen mr-1"></i>Edit
                                        </button>
                                        <form action="{{ route('soal.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini secara permanen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger font-weight-bold">
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </button>
                                        </form>
                                    </div>

                                    <p class="small text-muted mb-3">{{ $item->pertanyaan }}</p>
                                    
                                    @if($item->gambar_soal)
                                        <img src="{{ asset('storage/soal/'.$item->gambar_soal) }}" class="img-fluid rounded border mb-3 mx-auto d-block" style="max-height: 120px">
                                    @endif
                                    
                                    <div class="bg-light p-2 rounded border-0">
                                        @foreach($item->jawaban as $jw)
                                            <div class="small mb-2 {{ $jw->jawaban_benar ? 'text-success font-weight-bold' : 'text-muted' }}">
                                                <i class="fas {{ $jw->jawaban_benar ? 'fa-check-circle' : 'fa-circle' }} mr-2"></i>
                                                {{ $jw->teks_jawaban }}
                                                @if($jw->gambar_jawaban)
                                                    <div class="mt-2 pl-4"><img src="{{ asset('storage/jawaban/'.$jw->gambar_jawaban) }}" class="rounded border" style="max-height: 40px"></div>
                                                @endif
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

<div class="modal fade" id="modalEditSoal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-edit mr-2 text-primary"></i>Perbarui Butir Soal</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formEditSoal" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body px-4 pt-0">
                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-muted text-uppercase">Konten Pertanyaan</label>
                        <textarea name="pertanyaan" id="e_p" class="form-control mb-3" rows="3" required></textarea>
                        
                        <div id="prev_g_s" class="mb-3 d-none text-center bg-light p-3 rounded border">
                            <img src="" id="render_g_s" class="img-thumbnail mb-2 shadow-sm" style="max-height: 120px">
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" class="custom-control-input" name="hapus_gambar_soal" id="hgs">
                                <label class="custom-control-label text-danger font-weight-bold small" for="hgs">Hapus Gambar Soal</label>
                            </div>
                        </div>

                        <label class="upload-area">
                            <input type="file" name="gambar_soal" class="hidden-input" onchange="updateLabel(this)">
                            <i class="fas fa-cloud-upload-alt text-primary"></i>
                            <span class="file-label text-primary">Ganti / Unggah Gambar Soal Baru</span>
                        </label>
                    </div>

                    <div id="e_j">
                        </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-white font-weight-bold px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow rounded-pill">SIMPAN PERUBAHAN</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Fungsi Update Label File
function updateLabel(input) {
    const fileName = input.files[0].name;
    $(input).closest('.upload-area').find('.file-label').text(fileName).addClass('text-dark');
}

function updateLabelSmall(input) {
    const fileName = input.files[0].name;
    const status = $(input).closest('.pg-item, .edit-jw-card').find('.file-status');
    status.html('<i class="fas fa-paperclip mr-1"></i>' + fileName).fadeIn();
}

$(document).ready(function() {
    // 1. Auto-Split Paste (Logic Tetap)
    $('.input-jawaban').on('paste', function(e) {
        let data = e.originalEvent.clipboardData.getData('text');
        let lines = data.split(/\n|[a-eA-E][\.\)]\s?/).map(s => s.trim()).filter(Boolean);
        if (lines.length > 1) {
            e.preventDefault();
            lines.forEach((t, i) => { if (i < 5) $('.input-jawaban').eq(i).val(t); });
        }
    });

    // 2. Dynamic Modal Edit (Redesigned Elements)
    $('.btn-edit-soal').click(function() {
        let d = $(this).data();
        $('#formEditSoal').attr('action', d.url);
        $('#e_p').val(d.pertanyaan);
        $('#hgs').prop('checked', false);
        
        // Reset Label Gambar Soal di Modal
        $('.file-label').text('Ganti / Unggah Gambar Soal Baru').removeClass('text-dark');

        if(d.gambar) {
            $('#render_g_s').attr('src', '/storage/soal/'+d.gambar);
            $('#prev_g_s').removeClass('d-none');
        } else { $('#prev_g_s').addClass('d-none'); }

        let h = '<label class="small font-weight-bold text-muted text-uppercase mb-3 d-block">Pilihan Jawaban & Kunci</label>';
        d.jawaban.forEach((jw, i) => {
            let isChecked = jw.jawaban_benar ? 'checked' : '';
            let label = String.fromCharCode(65 + i);

            h += `
            <div class="edit-jw-card shadow-sm">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text bg-transparent border-0">
                            <input type="radio" name="kunci_jawaban" value="${jw.id}" ${isChecked} required style="transform: scale(1.1)">
                            <b class="ml-3 text-dark">${label}</b>
                        </div>
                    </div>
                    <input type="text" name="jawaban[${jw.id}]" class="form-control border-0 bg-light font-weight-bold" value="${jw.teks_jawaban}" required>
                </div>
                <div class="d-flex align-items-center">
                    ${jw.gambar_jawaban ? `
                        <div class="mr-4 text-center">
                            <img src="/storage/jawaban/${jw.gambar_jawaban}" style="max-height: 50px" class="rounded border shadow-sm mb-2 d-block mx-auto">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="hapus_gambar_jawaban[${jw.id}]" id="hj_${jw.id}" value="1">
                                <label class="custom-control-label text-danger font-weight-bold" for="hj_${jw.id}" style="font-size: 10px">Hapus Gambar</label>
                            </div>
                        </div>
                    ` : ''}
                    <div class="flex-grow-1">
                        <label class="upload-area py-2 px-3 mb-0" style="border-width: 1px; border-style: solid;">
                            <input type="file" name="gambar_jawaban_edit[${jw.id}]" class="hidden-input" onchange="updateLabelSmall(this)">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-camera text-primary mr-2 mb-0" style="font-size: 14px"></i>
                                <span class="file-label text-primary mb-0" style="font-size: 9px">Update Gambar Jawaban</span>
                            </div>
                        </label>
                        <div class="small text-primary font-weight-bold mt-1 file-status text-center" style="font-size: 10px; display:none;"></div>
                    </div>
                </div>
            </div>`;
        });
        $('#e_j').html(h);
        $('#modalEditSoal').modal('show');
    });
});
</script>
@endpush