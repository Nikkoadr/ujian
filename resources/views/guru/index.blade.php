@extends('layouts.app')
@section('title', 'Manajemen Data Guru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Data Guru</h1>
        <div class="d-flex">
            <button class="btn btn-sm btn-success shadow-sm mr-2" data-toggle="modal" data-target="#modalImportGuru" style="border-radius: 10px; padding: 0.4rem 1rem;">
                <i class="fas fa-file-import fa-sm text-white-50 mr-2"></i> Import Excel
            </button>
            <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahGuru" style="border-radius: 10px; padding: 0.4rem 1rem;">
                <i class="fas fa-user-plus fa-sm text-white-50 mr-2"></i> Tambah Guru & Akun
            </button>
        </div>
    </div>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Guru & Hak Akses</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="table-guru" width="100%">
                    <thead>
                        <tr class="bg-light text-dark">
                            <th width="50">No</th>
                            <th>NIP</th>
                            <th>Nama Guru</th>
                            <th>Jenis Kelamin</th>
                            <th>Email (Username)</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data_guru as $guru)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge badge-light border px-2 py-1">{{ $guru->nip }}</span></td>
                            <td class="font-weight-bold text-primary">{{ $guru->nama_lengkap }}</td>
                            <td>
                                <i class="fas {{ $guru->user->jenis_kelamin == 'laki-laki' ? 'fa-mars text-primary' : 'fa-venus text-danger' }} mr-1"></i>
                                {{ ucfirst($guru->user->jenis_kelamin) }}
                            </td>
                            <td>{{ $guru->user->email }}</td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #eaecf4;">
                                    <a href="{{ route('guru.edit', $guru->id) }}" class="btn btn-sm btn-white text-primary border-right px-3"><i class="fas fa-edit"></i></a>
                                    <button type="button" class="btn btn-sm btn-white text-danger px-3" onclick="confirmDelete({{ $guru->id }}, '{{ $guru->user->name }}')"><i class="fas fa-trash"></i></button>
                                </div>
                                <form id="delete-form-{{ $guru->id }}" action="{{ route('guru.destroy', $guru->id) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
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
<div class="modal fade" id="modalTambahGuru" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-gray-800">Tambah Guru Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('guru.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">NIP Guru</label>
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" 
                                value="{{ old('nip') }}" placeholder="1980..." required style="border-radius: 8px;">
                            @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Nama Lengkap (tanpa gelar)</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                value="{{ old('nama') }}" placeholder="Nama Lengkap" required style="border-radius: 8px;">
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Gelar Depan <span class="text-muted font-weight-normal">(opsional, mis. Dr.)</span></label>
                            <input type="text" name="gelar_depan" class="form-control @error('gelar_depan') is-invalid @enderror" 
                                value="{{ old('gelar_depan') }}" placeholder="Dr." maxlength="50" style="border-radius: 8px;">
                            @error('gelar_depan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Gelar Belakang <span class="text-muted font-weight-normal">(opsional, mis. S.Pd.)</span></label>
                            <input type="text" name="gelar_belakang" class="form-control @error('gelar_belakang') is-invalid @enderror" 
                                value="{{ old('gelar_belakang') }}" placeholder="S.Pd." maxlength="50" style="border-radius: 8px;">
                            @error('gelar_belakang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required style="border-radius: 8px;">
                                <option value="laki-laki" {{ old('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ old('jenis_kelamin') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}" placeholder="guru@sekolah.com" required style="border-radius: 8px;">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-3 opacity-50">

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                placeholder="Minimal 6 karakter" required style="border-radius: 8px;">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" 
                                placeholder="Ulangi password" required style="border-radius: 8px;">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light font-weight-bold px-4" data-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm" style="border-radius: 10px;">
                        <i class="fas fa-save mr-2"></i>Simpan Guru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- MODAL IMPORT GURU --}}
<div class="modal fade" id="modalImportGuru" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-gray-800">
                    <i class="fas fa-file-excel text-success mr-2"></i>Import Guru dari Excel
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('guru.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <a href="{{ asset('assets/format_excel/guru.xlsx') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download mr-1"></i> Download Format Excel
                    </a>

                    @if($errors->has('file_excel'))
                        <div class="alert alert-danger border-0 shadow-sm" role="alert">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first('file_excel') }}
                        </div>
                    @endif

                    <div class="form-group mt-4">
                        <label class="small font-weight-bold text-dark">Pilih File Excel</label>
                        <div class="custom-file">
                            <input type="file" name="file_excel" class="custom-file-input @error('file_excel') is-invalid @enderror" id="importFileGuru" required>
                            <label class="custom-file-label" for="importFileGuru">Pilih file...</label>
                        </div>
                        <small class="text-muted mt-2 d-block">Gunakan format .xlsx atau .xls. Kolom: nip, nama, gelar_depan, gelar_belakang, jenis_kelamin (L/P), email, password.</small>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#table-guru').DataTable();

        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
        });

        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        @if(session('success')) Toast.fire({ icon: 'success', title: "{{ session('success') }}" }); @endif
        @if(session('error')) Toast.fire({ icon: 'error', title: "{{ session('error') }}" }); @endif
        @if($errors->any())
            @if($errors->has('file_excel'))
                $('#modalImportGuru').modal('show');
                Toast.fire({ icon: 'error', title: @json($errors->first('file_excel')) });
            @else
                $('#modalTambahGuru').modal('show');
                Toast.fire({ icon: 'error', title: 'Periksa inputan Anda' });
            @endif
        @endif
    });

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Guru?',
            text: "Menghapus " + name + " akan menghapus akun loginnya juga!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) { document.getElementById('delete-form-' + id).submit(); }
        });
    }
</script>
@endpush