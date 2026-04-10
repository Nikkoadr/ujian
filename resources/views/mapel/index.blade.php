@extends('layouts.app')
@section('title', 'Jadwal Mata Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Jadwal Mata Pelajaran</h1>
            @if(Gate::allows('admin'))
            <div class="d-flex align-items-center mb-0">
                <button class="btn btn-sm btn-success shadow-sm mr-2" data-toggle="modal" data-target="#modalImport" style="border-radius: 10px; padding: 0.4rem 1rem;">
                    <i class="fas fa-file-excel fa-sm text-white-50 mr-2"></i> Import Excel
                </button>

                <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahMapel" style="border-radius: 10px; padding: 0.4rem 1rem;">
                    <i class="fas fa-calendar-plus fa-sm text-white-50 mr-2"></i> Buat Jadwal Ujian
                </button>
            </div>
            @endif
    </div>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Ujian & Progress Soal</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabelMapel" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-light text-dark">
                            <th>No</th>
                            <th>Kode & Mapel</th>
                            <th>Jadwal Ujian</th>
                            <th>Durasi</th>
                            <th class="text-center">Jumlah Soal</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mapels as $mapel)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="font-weight-bold text-primary">{{ $mapel->kode_mapel }}</div>
                                <div class="text-dark font-weight-bold">{{ $mapel->nama_mapel }}</div>
                            </td>
                            <td>
                                <div class="small">
                                    <i class="fas fa-calendar-day fa-xs mr-1 text-muted"></i> 
                                    {{ \Carbon\Carbon::parse($mapel->tanggal_ujian)->translatedFormat('d F Y') }}<br>
                                    <i class="fas fa-clock fa-xs mr-1 text-muted"></i> 
                                    {{ $mapel->jam_mulai }} - {{ $mapel->jam_selesai }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light p-2 border">
                                    <i class="far fa-hourglass mr-1"></i> {{ $mapel->durasi }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="h5 mb-0 font-weight-bold {{ $mapel->soals_count > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $mapel->soals_count ?? 0 }}
                                </div>
                                <div class="small text-uppercase font-weight-bold opacity-50" style="font-size: 10px;">Butir Soal</div>
                            </td>
                            <td>
                                @if($mapel->status == 'aktif')
                                    <span class="badge badge-success px-3 py-2">Aktif</span>
                                @else
                                    <span class="badge badge-danger px-3 py-2">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #eaecf4;">
                                    {{-- Kelola Soal (Admin & Pengawas) --}}
                                    <a href="{{ route('soal.index', $mapel->id) }}" 
                                       class="btn btn-sm btn-white text-success border-right px-3" 
                                       title="Kelola Soal Ujian">
                                        <i class="fas fa-file-signature"></i>
                                    </a>

                                    @if(Gate::allows('admin'))
                                    {{-- Edit Jadwal --}}
                                    <a href="{{ route('mapel.edit', $mapel->id) }}" 
                                       class="btn btn-sm btn-white text-primary border-right px-3" 
                                       title="Edit Jadwal Ujian">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Hapus (SweetAlert2 Trigger) --}}
                                    <form action="{{ route('mapel.destroy', $mapel->id) }}" method="POST" id="delete-form-{{ $mapel->id }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                class="btn btn-sm btn-white text-danger px-3" 
                                                onclick="confirmDelete({{ $mapel->id }}, '{{ $mapel->nama_mapel }}')"
                                                title="Hapus Mapel">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
@if(Gate::allows('admin'))
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-gray-800" id="modalImportLabel">
                    <i class="fas fa-file-excel text-success mr-2"></i>Import Jadwal dari Excel
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('mapel.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <a href="{{ asset('assets/format_excel/mapel.xlsx') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download mr-1"></i> Download Format Excel
                    </a>

                    <div class="form-group mt-4">
                        <label class="small font-weight-bold text-dark">Pilih File Excel</label>
                        <div class="custom-file">
                            <input type="file" name="file_excel" class="custom-file-input" id="importFile" required>
                            <label class="custom-file-label" for="importFile">Pilih file...</label>
                        </div>
                        <small class="text-muted mt-2 d-block">Gunakan format .xlsx atau .xls</small>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm" style="border-radius: 10px;">
                        Upload & Proses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@if(Gate::allows('admin'))
<div class="modal fade" id="modalTambahMapel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold text-gray-800">Tambah Jadwal Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('mapel.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Kode Mapel</label>
                            <input type="text" name="kode_mapel" class="form-control @error('kode_mapel') is-invalid @enderror" value="{{ old('kode_mapel') }}" placeholder="E.g. BIN-X-1" required>
                            @error('kode_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Nama Mata Pelajaran</label>
                            <input type="text" name="nama_mapel" class="form-control @error('nama_mapel') is-invalid @enderror" value="{{ old('nama_mapel') }}" placeholder="E.g. Bahasa Indonesia" required>
                            @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Tanggal Ujian</label>
                            <input type="date" name="tanggal_ujian" class="form-control @error('tanggal_ujian') is-invalid @enderror" value="{{ old('tanggal_ujian') }}" required>
                            @error('tanggal_ujian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" value="{{ old('jam_mulai') }}" required>
                            @error('jam_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror" value="{{ old('jam_selesai') }}" required>
                            @error('jam_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Durasi (HH:mm:ss)</label>
                        <input type="time" name="durasi" step="1" class="form-control @error('durasi') is-invalid @enderror" value="{{ old('durasi') }}" required>
                        @error('durasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Tingkat</label>
                        <select name="tingkat_id" class="form-control @error('tingkat_id') is-invalid @enderror" required>
                            <option value="">Pilih Tingkat</option>
                            @foreach($tingkat as $level)
                                <option value="{{ $level->id }}" {{ old('tingkat_id') == $level->id ? 'selected' : '' }}>{{ $level->nama_tingkat }}</option>
                            @endforeach
                        </select>
                        @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Kompetensi Keahlian</label>
                        <select name="kompetensi_keahlian_id" class="form-control @error('kompetensi_keahlian_id') is-invalid @enderror" required>
                            <option value="">Pilih Kompetensi Keahlian</option>
                            @foreach($kompetensi_keahlian as $keahlian)
                                <option value="{{ $keahlian->id }}" {{ old('kompetensi_keahlian_id') == $keahlian->id ? 'selected' : '' }}>{{ $keahlian->nama_kompetensi }}</option>
                            @endforeach
                        </select>
                        @error('kompetensi_keahlian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // DataTables
        $('#tabelMapel').DataTable({
            "language": {
                "search": "Cari Jadwal:",
                "lengthMenu": "Tampil _MENU_",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            }
        });

        // Toast Configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // Success Notification
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        // Error Notification
        @if(session('error') || $errors->any())
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') ?? 'Silakan periksa kembali inputan Anda.' }}"
            });
            @if($errors->any())
                $('#modalTambahMapel').modal('show');
            @endif
        @endif
    });

    // Delete Confirmation
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Jadwal?',
            text: "Jadwal " + name + " dan semua soal di dalamnya akan dihapus permanen!",
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
            // Custom File Input Label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
</script>
@endpush