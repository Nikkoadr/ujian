@extends('layouts.app')

@section('title')
    Manajemen Periode Ujian
@endsection

@push('styles')
    {{-- Jika perlu style tambahan untuk modal --}}
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-soft border-0">
                <div class="card-header bg-white py-4 px-4 border-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h5 class="m-0 font-weight-bold text-dark">Daftar Periode Ujian</h5>
                            <small class="text-muted">Kelola periode ujian yang tersedia</small>
                        </div>
                    </div>
                    {{-- Tombol buka modal tambah --}}
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-4" data-toggle="modal" data-target="#modalTambahPeriode">
                        <i class="fas fa-plus mr-2"></i> Tambah Periode
                    </button>
                </div>

                <div class="card-body p-4">
                    {{-- Alert sukses --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Alert error global (misal dari validasi) --}}
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Periode</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periodeUjian as $index => $periode)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $periode->nama_periode }}</td>
                                    <td>{{ $periode->tanggal_mulai ? \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d-m-Y') : '-' }}</td>
                                    <td>{{ $periode->tanggal_selesai ? \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d-m-Y') : '-' }}</td>
                                    <td>
                                        @if($periode->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('periode_ujian.show', $periode->id) }}" class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('periode_ujian.edit', $periode->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Tombol hapus dengan SweetAlert2 --}}
                                        <button type="button" class="btn btn-sm btn-danger" title="Hapus" onclick="confirmDelete({{ $periode->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        {{-- Form delete tersembunyi --}}
                                        <form id="delete-form-{{ $periode->id }}" action="{{ route('periode_ujian.destroy', $periode->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada periode ujian.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL TAMBAH PERIODE UJIAN --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalTambahPeriode" tabindex="-1" role="dialog" aria-labelledby="modalTambahPeriodeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-soft border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalTambahPeriodeLabel">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Periode Ujian
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('periode_ujian.store') }}" method="POST" id="formTambahPeriode">
                @csrf
                <div class="modal-body">
                    {{-- Tampilkan error validasi di dalam modal (jika ada) --}}
                    @if($errors->any() && old('_token'))
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="nama_periode" class="font-weight-bold">Nama Periode <span class="text-danger">*</span></label>
                        <input type="text" name="nama_periode" id="nama_periode" class="form-control" value="{{ old('nama_periode') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_mulai" class="font-weight-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_selesai" class="font-weight-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi" class="font-weight-bold">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active') ? 'checked' : '' }}>
                        <label class="form-check-label font-weight-bold" for="is_active">Aktifkan periode ini</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Fungsi konfirmasi hapus dengan SweetAlert2
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Periode Ujian?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form delete
                document.getElementById('delete-form-' + id).submit();
                // Tampilkan toast info (opsional)
                Swal.fire({
                    icon: 'info',
                    title: 'Sedang menghapus...',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }

    // Jika ada error validasi, buka modal otomatis
    $(document).ready(function() {
        @if($errors->any() && old('_token'))
            $('#modalTambahPeriode').modal('show');
        @endif
    });
</script>
@endpush