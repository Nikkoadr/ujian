@extends('layouts.app')

@section('title', 'Bank Soal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Bank Soal</h1>
        <div>
            <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahBankSoal" style="border-radius: 10px; padding: 0.4rem 1rem;">
                <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Tambah Bank Soal
            </button>
        </div>
    </div>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Bank Soal</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabelBankSoal" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-light text-dark">
                            <th>No</th>
                            <th>Kode Mapel</th>
                            <th>Nama Mapel</th>
                            <th>Tingkat</th>
                            <th>Kompetensi Keahlian</th>
                            <th class="text-center">Jumlah Pertanyaan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bankSoals as $bs)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge badge-primary">{{ $bs->kode_mapel }}</span></td>
                            <td>{{ $bs->nama_mapel }}</td>
                            <td>{{ $bs->tingkat->nama_tingkat ?? '-' }}</td>
                            <td>{{ $bs->kompetensiKeahlian->nama_kompetensi ?? '-' }}</td>
                            <td class="text-center">
                                <span class="h5 mb-0 font-weight-bold {{ $bs->pertanyaans_count > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $bs->pertanyaans_count ?? 0 }}
                                </span>
                                <div class="small text-uppercase font-weight-bold opacity-50" style="font-size: 10px;">Butir Soal</div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #eaecf4;">
                                    {{-- Detail / Kelola Soal --}}
                                    <a href="{{ route('bank-pertanyaan.index', $bs->id) }}" 
                                       class="btn btn-sm btn-white text-success border-right px-3" 
                                       title="Kelola Pertanyaan">
                                        <i class="fas fa-file-signature"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('bank-soal.edit', $bs->id) }}" 
                                       class="btn btn-sm btn-white text-primary border-right px-3" 
                                       title="Edit Bank Soal">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('bank-soal.destroy', $bs->id) }}" method="POST" id="delete-form-{{ $bs->id }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                class="btn btn-sm btn-white text-danger px-3" 
                                                onclick="confirmDelete({{ $bs->id }}, '{{ $bs->nama_mapel }}')"
                                                title="Hapus Bank Soal">
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

{{-- MODAL TAMBAH BANK SOAL --}}
<div class="modal fade" id="modalTambahBankSoal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold text-gray-800">Tambah Bank Soal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('bank-soal.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Kode Mapel</label>
                            <input type="text" name="kode_mapel" class="form-control @error('kode_mapel') is-invalid @enderror" value="{{ old('kode_mapel') }}" placeholder="E.g. BIN-10" required>
                            @error('kode_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Nama Mata Pelajaran</label>
                            <input type="text" name="nama_mapel" class="form-control @error('nama_mapel') is-invalid @enderror" value="{{ old('nama_mapel') }}" placeholder="E.g. Bahasa Indonesia" required>
                            @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Tingkat</label>
                            <select name="tingkat_id" class="form-control @error('tingkat_id') is-invalid @enderror" required>
                                <option value="">Pilih Tingkat</option>
                                @foreach($tingkats as $t)
                                    <option value="{{ $t->id }}" {{ old('tingkat_id') == $t->id ? 'selected' : '' }}>{{ $t->nama_tingkat }}</option>
                                @endforeach
                            </select>
                            @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Kompetensi Keahlian (Opsional)</label>
                            <select name="kompetensi_keahlian_id" class="form-control @error('kompetensi_keahlian_id') is-invalid @enderror">
                                <option value="">Pilih Kompetensi Keahlian</option>
                                @foreach($kompetensis as $k)
                                    <option value="{{ $k->id }}" {{ old('kompetensi_keahlian_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kompetensi }}</option>
                                @endforeach
                            </select>
                            @error('kompetensi_keahlian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow">Simpan</button>
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
        // DataTables
        $('#tabelBankSoal').DataTable({
            "language": {
                "search": "Cari Bank Soal:",
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
                $('#modalTambahBankSoal').modal('show');
            @endif
        @endif
    });

    // Delete Confirmation
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Bank Soal?',
            text: "Bank Soal " + name + " dan semua pertanyaan di dalamnya akan dihapus permanen!",
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