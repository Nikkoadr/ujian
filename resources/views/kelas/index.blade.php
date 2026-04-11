@extends('layouts.app')
@section('title', 'Manajemen Data Kelas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Data Kelas</h1>
        <div class="d-flex align-items-center mb-0">
            <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahKelas" style="border-radius: 10px; padding: 0.4rem 1rem;">
                <i class="fas fa-plus-circle fa-sm text-white-50 mr-2"></i> Tambah Kelas Baru
            </button>
        </div>
    </div>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kelas & Informasi Tingkat</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="table-kelas" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-light text-dark">
                            <th width="50">No</th>
                            <th>Tingkat</th>
                            <th>Kompetensi Keahlian</th>
                            <th>Nama Kelas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data_kelas as $kelas)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="font-weight-bold text-primary">{{ $kelas->tingkat->nama_tingkat }}</div>
                            </td>
                            <td>
                                <div class="text-dark font-weight-bold">{{ $kelas->kompetensi_keahlian->nama_kompetensi }}</div>
                            </td>
                            <td>
                                <span class="badge badge-light p-2 border font-weight-bold text-info" style="border-radius: 8px;">
                                    <i class="fas fa-chalkboard mr-1"></i> {{ $kelas->nama_kelas }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #eaecf4;">
                                    {{-- Edit (Halaman Terpisah) --}}
                                    <a href="{{ route('kelas.edit', $kelas->id) }}" 
                                       class="btn btn-sm btn-white text-primary border-right px-3" 
                                       title="Edit Data Kelas">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Hapus (SweetAlert2 Trigger) --}}
                                    <form action="{{ route('kelas.destroy', $kelas->id) }}" method="POST" id="delete-form-{{ $kelas->id }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                class="btn btn-sm btn-white text-danger px-3" 
                                                onclick="confirmDelete({{ $kelas->id }}, '{{ $kelas->nama_kelas }}')"
                                                title="Hapus Kelas">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
<div class="modal fade" id="modalTambahKelas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-gray-800">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>Tambah Kelas Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold text-dark">Tingkat</label>
                            <select name="tingkat_id" class="form-control @error('tingkat_id') is-invalid @enderror" required>
                                <option value="">Pilih Tingkat</option>
                                @foreach($data_tingkat as $t)
                                    <option value="{{ $t->id }}" {{ old('tingkat_id') == $t->id ? 'selected' : '' }}>{{ $t->nama_tingkat }}</option>
                                @endforeach
                            </select>
                            @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold text-dark">Kompetensi Keahlian</label>
                            <select name="kompetensi_keahlian_id" class="form-control @error('kompetensi_keahlian_id') is-invalid @enderror" required>
                                <option value="">Pilih Keahlian</option>
                                @foreach($data_keahlian as $k)
                                    <option value="{{ $k->id }}" {{ old('kompetensi_keahlian_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kompetensi }}</option>
                                @endforeach
                            </select>
                            @error('kompetensi_keahlian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label class="small font-weight-bold text-dark">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control @error('nama_kelas') is-invalid @enderror" 
                               value="{{ old('nama_kelas') }}" placeholder="E.g. XII-RPL-1" maxlength="10" required>
                        @error('nama_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted mt-1 d-block italic text-xs">* Maksimal 10 karakter (Contoh: X-TKJ-1)</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light font-weight-bold px-4" data-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm" style="border-radius: 10px;">
                        Simpan Data Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // DataTables Configuration
        $('#table-kelas').DataTable({
            "language": {
                "search": "Cari Kelas:",
                "lengthMenu": "Tampil _MENU_",
                "zeroRecords": "Data kelas tidak ditemukan",
                "info": "Menampilkan _PAGE_ dari _PAGES_",
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

        // Error & Validation Handling
        @if(session('error') || $errors->any())
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') ?? 'Gagal menyimpan, periksa kembali inputan Anda.' }}"
            });
            @if($errors->any() && !old('_method'))
                $('#modalTambahKelas').modal('show');
            @endif
        @endif
    });

    // Delete Confirmation
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Kelas?',
            text: "Seluruh data siswa dan jadwal yang terkait dengan kelas " + name + " mungkin akan terpengaruh!",
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