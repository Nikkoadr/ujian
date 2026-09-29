@extends('layouts.app')
@section('title', 'Data Siswa')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Siswa</h1>
        
        @if(Gate::allows('admin'))
        <div class="d-flex">
            <button class="btn btn-sm btn-success shadow-sm mr-2" data-toggle="modal" data-target="#modalImportSiswa" style="border-radius: 10px;">
                <i class="fas fa-file-import fa-sm text-white-50 mr-2"></i> Import Excel
            </button>
            <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahSiswa" style="border-radius: 10px;">
                <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Tambah Siswa
            </button>
        </div>
        @endif
    </div>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa & Status Akun</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 col-6 mb-2">
                    <select id="filterStatus" class="form-control font-weight-bold" style="border-radius: 10px;">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="diblokir">Terblokir</option>
                    </select>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <select id="filterKelas" class="form-control font-weight-bold" style="border-radius: 10px;">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="tabelSiswa" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-light text-dark">
                            <th width="5%">No</th>
                            <th width="30%">Info Detail</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL IMPORT SISWA --}}
@if(Gate::allows('admin'))
<div class="modal fade" id="modalImportSiswa" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-gray-800" id="modalImportLabel">
                    <i class="fas fa-file-excel text-success mr-2"></i>Import Siswa dari Excel
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <a href="{{ asset('assets/format_excel/siswa.xlsx') }}" class="btn btn-outline-primary btn-sm">
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

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahSiswa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold">Tambah Siswa Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('siswa.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">NISN</label>
                            <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn') }}" required>
                            @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">NIS</label>
                            <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis') }}" required>
                            @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Kelas</label>
                            <select name="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                            @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="laki-laki" {{ old('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ old('jenis_kelamin') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow" style="border-radius: 10px;">Simpan Data</button>
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
        const csrfToken = '{{ csrf_token() }}';

        // DataTable server-side: hanya 10-100 baris per halaman yang ditransfer,
        // search/filter/sort dikerjakan database (aman untuk 2000+ siswa).
        const tabelSiswa = $('#tabelSiswa').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('siswa.index') }}",
                data: function(d) {
                    d.filterStatus = $('#filterStatus').val();
                    d.filterKelas = $('#filterKelas').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'detail', name: 'detail', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'kelas', name: 'kelas', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' },
            ],
            order: [[2, 'asc']],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            "language": {
                "search": "Cari Siswa:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "processing": "Memuat data...",
                "zeroRecords": "Data tidak ditemukan.",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ siswa",
                "infoEmpty": "Tidak ada data siswa",
                "infoFiltered": "(disaring dari _MAX_ siswa)",
                "paginate": { "previous": "‹", "next": "›" }
            }
        });

        $('#filterStatus, #filterKelas').on('change', function() {
            tabelSiswa.ajax.reload();
        });

        // Toggle blokir via AJAX (baris dinamis -> event delegation).
        $('#tabelSiswa').on('click', '.btn-toggle-status', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Ubah Status?',
                text: 'Status akses siswa akan dibalik (Aktif ↔ Terblokir).',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#e74a3b',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.post(`/siswa/toggle/${id}`, { _token: csrfToken })
                    .done(function(res) {
                        tabelSiswa.ajax.reload(null, false);
                        Toast.fire({ icon: 'success', title: res.message || 'Status berhasil diubah.' });
                    })
                    .fail(function() {
                        Toast.fire({ icon: 'error', title: 'Gagal mengubah status. Coba lagi.' });
                    });
            });
        });

        // Hapus via AJAX (baris dinamis -> event delegation).
        $('#tabelSiswa').on('click', '.btn-delete-siswa', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data siswa " + nama + " akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#e74a3b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: `/siswa/${id}`,
                    type: 'DELETE',
                    data: { _token: csrfToken }
                })
                    .done(function() {
                        tabelSiswa.ajax.reload(null, false);
                        Toast.fire({ icon: 'success', title: 'Data siswa berhasil dihapus.' });
                    })
                    .fail(function() {
                        Toast.fire({ icon: 'error', title: 'Gagal menghapus data. Coba lagi.' });
                    });
            });
        });

        // Custom File Input Label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // SweetAlert2 Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        @endif

        @if(session('error'))
            Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
        @endif

        @if($errors->any())
            Toast.fire({ 
                icon: 'error', 
                title: 'Terjadi kesalahan, silakan periksa kembali.' 
            });

            // Cek apakah ada error spesifik milik Modal Import
            @if($errors->has('file_excel'))
                $('#modalImportSiswa').modal('show');
            
            // Cek apakah ada error milik Modal Tambah (nama, email, nisn, nis, dll)
            @elseif($errors->has('nama') || $errors->has('email') || $errors->has('nisn') || $errors->has('nis'))
                $('#modalTambahSiswa').modal('show');
            
            // Jika tidak yakin, buka modal yang paling mungkin (opsional)
            @else
                // Anda bisa biarkan kosong atau pilih salah satu
            @endif
        @endif
    });

</script>
@endpush