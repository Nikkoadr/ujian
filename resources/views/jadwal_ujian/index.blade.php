@extends('layouts.app')

@section('title', 'Jadwal Ujian')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Jadwal Ujian</h1>
        @if(Gate::allows('admin'))
        <div class="d-flex align-items-center">
            <button class="btn btn-sm btn-success shadow-sm mr-2" data-toggle="modal" data-target="#modalImport" style="border-radius: 10px; padding: 0.4rem 1rem;">
                <i class="fas fa-file-excel fa-sm text-white-50 mr-2"></i> Import Excel
            </button>
            <button class="btn btn-sm btn-primary shadow-sm mr-2" data-toggle="modal" data-target="#modalTambahJadwal" style="border-radius: 10px; padding: 0.4rem 1rem;">
                <i class="fas fa-calendar-plus fa-sm mr-2"></i> Buat Jadwal Ujian
            </button>
            <button class="btn btn-sm btn-danger shadow-sm" id="btnHapusMasal" style="border-radius: 10px; padding: 0.4rem 1rem; display: none;">
                <i class="fas fa-trash fa-sm mr-2"></i> Hapus (<span id="jumlahTerpilih">0</span>)
            </button>
        </div>
        @endif
    </div>

    {{-- Info periode aktif --}}
    @if($periodeAktif)
        <div class="alert alert-primary border-0 shadow-sm" style="border-radius: 15px;">
            <i class="fas fa-info-circle mr-2"></i> Menampilkan ujian untuk periode: <strong>{{ $periodeAktif->nama_periode }}</strong>
            ({{ \Carbon\Carbon::parse($periodeAktif->tanggal_mulai)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($periodeAktif->tanggal_selesai)->format('d-m-Y') }})
        </div>
    @else
        <div class="alert alert-warning border-0 shadow-sm" style="border-radius: 15px;">
            <i class="fas fa-exclamation-triangle mr-2"></i> Belum ada periode ujian yang aktif. Silakan aktifkan periode terlebih dahulu.
        </div>
    @endif

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Ujian & Progress Soal</h6>
            @if(Gate::allows('admin'))
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="checkAll">
                <label class="form-check-label small font-weight-bold" for="checkAll">Pilih Semua</label>
            </div>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabelUjian" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-light text-dark">
                            @if(Gate::allows('admin'))
                            <th width="30" class="text-center">
                                <input type="checkbox" id="checkAllTable">
                            </th>
                            @endif
                            <th>No</th>
                            <th>Kode & Nama Ujian</th>
                            <th>Tanggal & Jam</th>
                            <th>Durasi</th>
                            <th class="text-center">Jumlah Soal</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mapels as $mapel)
                        @php
                            $jadwal = $mapel->jadwal->first();
                        @endphp
                        <tr data-id="{{ $jadwal->id ?? '' }}">
                            @if(Gate::allows('admin'))
                            <td class="text-center">
                                @if($jadwal)
                                <input type="checkbox" class="checkbox-item" value="{{ $jadwal->id }}">
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            @endif
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="font-weight-bold text-primary">{{ $mapel->kode_mapel }}</div>
                                <div class="text-dark font-weight-bold">{{ $mapel->nama_mapel }}</div>
                                <small class="text-muted">
                                    Periode: {{ $periodeAktif->nama_periode ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <div class="small">
                                    <i class="fas fa-calendar-day fa-xs mr-1 text-muted"></i> 
                                    {{ $jadwal ? \Carbon\Carbon::parse($jadwal->tanggal_ujian)->translatedFormat('d F Y') : '-' }}<br>
                                    <i class="fas fa-clock fa-xs mr-1 text-muted"></i> 
                                    {{ $jadwal ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '-' }} - 
                                    {{ $jadwal ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '-' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light p-2 border">
                                    <i class="far fa-hourglass mr-1"></i> 
                                    {{ $mapel->durasi ? substr($mapel->durasi, 0, 5) : '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="h5 mb-0 font-weight-bold {{ ($mapel->jumlah_soal_terpilih ?? 0) > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $mapel->jumlah_soal_terpilih ?? 0 }}
                                </div>
                                <div class="small text-uppercase font-weight-bold opacity-50" style="font-size: 10px;">Butir Soal</div>
                            </td>
                            <td>
                                @if($jadwal)
                                    @if($jadwal->status == 'aktif')
                                        <span class="badge badge-success px-3 py-2">Aktif</span>
                                    @else
                                        <span class="badge badge-danger px-3 py-2">Nonaktif</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary px-3 py-2">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #eaecf4;">
                                    @if($jadwal)
                                    <a href="{{ route('soal.manage', ['jadwalId' => $jadwal->id, 'mapelId' => $mapel->id]) }}" 
                                        class="btn btn-sm btn-white text-success border-right px-3" 
                                        title="Kelola Soal Ujian">
                                        <i class="fas fa-file-signature"></i>
                                    </a>

                                    @if(Gate::allows('admin'))
                                    <a href="{{ route('jadwal-ujian.edit', $jadwal->id) }}" 
                                        class="btn btn-sm btn-white text-primary border-right px-3" 
                                        title="Edit Jadwal">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button" 
                                            class="btn btn-sm btn-white text-danger px-3" 
                                            onclick="confirmDelete({{ $jadwal->id }}, '{{ addslashes($mapel->nama_mapel) }}')"
                                            title="Hapus Jadwal">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $jadwal->id }}" 
                                        action="{{ route('jadwal-ujian.destroy', $jadwal->id) }}" 
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endif
                                    @else
                                    <span class="btn btn-sm btn-light text-muted px-3">Tidak ada jadwal</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ Gate::allows('admin') ? '8' : '7' }}" class="text-center py-4">
                                <i class="fas fa-inbox fa-2x text-muted d-block mb-2"></i>
                                <span class="text-muted">Belum ada jadwal ujian untuk periode ini.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL IMPORT EXCEL --}}
@if(Gate::allows('admin'))
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-gray-800" id="modalImportLabel">
                    <i class="fas fa-file-excel text-success mr-2"></i>Import Jadwal Ujian
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('jadwal-ujian.import') }}" method="POST" enctype="multipart/form-data" id="formImport">
                @csrf
                <div class="modal-body px-4">
                    <a href="{{ asset('assets/format_excel/mapel.xlsx') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download mr-1"></i> Download Format Excel
                    </a>

                    <div class="form-group mt-4">
                        <label class="small font-weight-bold text-dark">Pilih File Excel</label>
                        <div class="custom-file">
                            <input type="file" name="file" class="custom-file-input" id="importFile" required>
                            <label class="custom-file-label" for="importFile" id="fileLabel">Pilih file...</label>
                        </div>
                        <small class="text-muted mt-2 d-block">Gunakan format .xlsx atau .xls</small>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm" style="border-radius: 10px;">
                        <i class="fas fa-upload mr-2"></i> Upload & Proses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- MODAL TAMBAH JADWAL UJIAN --}}
@if(Gate::allows('admin'))
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold text-gray-800">
                    <i class="fas fa-calendar-plus text-primary mr-2"></i> Tambah Jadwal Ujian
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('jadwal-ujian.store') }}" method="POST" id="formTambahJadwal">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Periode Ujian <span class="text-danger">*</span></label>
                            <select name="periode_ujian_id" class="form-control @error('periode_ujian_id') is-invalid @enderror" required>
                                <option value="">Pilih Periode</option>
                                @foreach($periodeUjian as $periode)
                                    <option value="{{ $periode->id }}" {{ old('periode_ujian_id') == $periode->id ? 'selected' : '' }}>
                                        {{ $periode->nama_periode }}
                                    </option>
                                @endforeach
                            </select>
                            @error('periode_ujian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Pilih Mata Pelajaran <span class="text-danger">*</span></label>
                            <select name="mapel_id" class="form-control @error('mapel_id') is-invalid @enderror" required>
                                <option value="">Pilih Mapel</option>
                                @foreach($semuaMapel as $m)
                                    <option value="{{ $m->id }}" {{ old('mapel_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->kode_mapel }} - {{ $m->nama_mapel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Tanggal Ujian <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_ujian" class="form-control @error('tanggal_ujian') is-invalid @enderror" value="{{ old('tanggal_ujian') }}" required>
                            @error('tanggal_ujian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" value="{{ old('jam_mulai') }}" required>
                            @error('jam_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror" value="{{ old('jam_selesai') }}" required>
                            @error('jam_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Durasi (HH:mm:ss) <span class="text-danger">*</span></label>
                        <input type="time" name="durasi" step="1" class="form-control @error('durasi') is-invalid @enderror" value="{{ old('durasi') }}" required>
                        @error('durasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal" style="border-radius: 10px;">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow" style="border-radius: 10px;">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- FORM DESTROY MULTIPLE --}}
@if(Gate::allows('admin'))
<form id="form-destroy-multiple" action="{{ route('jadwal-ujian.destroy-multiple') }}" method="POST" style="display: none;">
    @csrf
    <div id="destroy-ids-container"></div>
</form>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    'use strict';

    // ============================================
    // 1. CEK & DESTROY DATATABLES SEBELUMNYA
    // ============================================
    if ($.fn.dataTable && $.fn.dataTable.isDataTable('#tabelUjian')) {
        $('#tabelUjian').DataTable().destroy();
    }

    // ============================================
    // 2. INISIALISASI DATATABLES
    // ============================================
    var table = null;
    try {
        table = $('#tabelUjian').DataTable({
            "language": {
                "search": "Cari Ujian:",
                "lengthMenu": "Tampil _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "→",
                    "previous": "←"
                }
            },
            "columnDefs": [
                @if(Gate::allows('admin'))
                { "orderable": false, "targets": [0, -1] },
                @else
                { "orderable": false, "targets": -1 },
                @endif
            ],
            "drawCallback": function() {
                if (typeof updateCheckboxState === 'function') {
                    updateCheckboxState();
                }
            },
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
            "autoWidth": false,
            "responsive": true,
            "processing": false,
            "serverSide": false
        });
    } catch (e) {
        console.warn('DataTables initialization error:', e);
        $('#tabelUjian').removeClass('dataTable');
    }

    // ============================================
    // 3. TOAST NOTIFICATION
    // ============================================
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
        $('#modalTambahJadwal').modal('show');
        Toast.fire({
            icon: 'error',
            title: "Periksa kembali inputan Anda."
        });
    @endif

    // ============================================
    // 4. CUSTOM FILE INPUT
    // ============================================
    $('#importFile').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#fileLabel').text(fileName).addClass('selected');
        } else {
            $('#fileLabel').text('Pilih file...').removeClass('selected');
        }
    });

    $('#modalImport').on('hidden.bs.modal', function() {
        $('#importFile').val('');
        $('#fileLabel').text('Pilih file...').removeClass('selected');
    });

    $('#modalImport .btn-light').on('click', function() {
        setTimeout(function() {
            $('#importFile').val('');
            $('#fileLabel').text('Pilih file...').removeClass('selected');
        }, 100);
    });

    // ============================================
    // 5. CHECKBOX UNTUK DESTROY MULTIPLE
    // ============================================
    @if(Gate::allows('admin'))
    
    function updateCheckboxState() {
        var totalCheckboxes = $('.checkbox-item').length;
        var checkedCheckboxes = $('.checkbox-item:checked').length;
        
        if (totalCheckboxes > 0) {
            $('#checkAllTable').prop('checked', totalCheckboxes === checkedCheckboxes);
            $('#checkAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        }
        
        if (checkedCheckboxes > 0) {
            $('#btnHapusMasal').show();
            $('#jumlahTerpilih').text(checkedCheckboxes);
        } else {
            $('#btnHapusMasal').hide();
        }
    }

    $('#checkAllTable').on('change', function() {
        var isChecked = $(this).prop('checked');
        $('.checkbox-item').prop('checked', isChecked);
        updateCheckboxState();
    });

    $('#checkAll').on('change', function() {
        var isChecked = $(this).prop('checked');
        $('#checkAllTable').prop('checked', isChecked);
        $('.checkbox-item').prop('checked', isChecked);
        updateCheckboxState();
    });

    $(document).on('change', '.checkbox-item', function() {
        updateCheckboxState();
    });

    // ============================================
    // 6. DESTROY MULTIPLE
    // ============================================
    $('#btnHapusMasal').on('click', function() {
        var selectedIds = [];
        $('.checkbox-item:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Toast.fire({
                icon: 'warning',
                title: 'Tidak ada data yang dipilih.'
            });
            return;
        }

        Swal.fire({
            title: 'Hapus data terpilih?',
            text: 'Anda akan menghapus ' + selectedIds.length + ' jadwal ujian sekaligus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let container = $('#destroy-ids-container');
                container.empty();

                selectedIds.forEach(id => {
                    container.append(`<input type="hidden" name="ids[]" value="${id}">`);
                });

                $('#form-destroy-multiple').submit();
            }
        });
    });

    // Update checkbox saat page/search di DataTable
    if (table) {
        $('#tabelUjian').on('page.dt search.dt', function() {
            updateCheckboxState();
        });
    }

    // Inisialisasi pertama
    setTimeout(updateCheckboxState, 100);

    @endif

    // ============================================
    // 7. TOOLTIP
    // ============================================
    $('[title]').tooltip({
        placement: 'top',
        trigger: 'hover'
    });

});

// ============================================
// 8. DELETE CONFIRMATION
// ============================================
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Jadwal Ujian?',
        html: "Jadwal untuk <strong>" + name + "</strong> akan dihapus permanen!",
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
        }
    });
}
</script>
@endpush