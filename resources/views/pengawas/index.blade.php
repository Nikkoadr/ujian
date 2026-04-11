@extends('layouts.app')
@section('title', 'Data Pengawas Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Data Pengawas Ujian</h1>
        <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahPengawas" style="border-radius: 10px;">
            <i class="fas fa-user-shield fa-sm mr-2"></i> Tambah Pengawas
        </button>
    </div>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Guru yang Bertugas Sebagai Pengawas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="table-pengawas" width="100%">
                    <thead>
                        <tr class="bg-light text-dark">
                            <th width="50">No</th>
                            <th>NIP</th>
                            <th>Nama Pengawas</th>
                            <th>Jenis Kelamin</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data_pengawas as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge badge-light border">{{ $p->guru->nip }}</span></td>
                            <td class="font-weight-bold">{{ $p->guru->user->nama }}</td>
                            <td>
                                <i class="fas {{ $p->guru->user->jenis_kelamin == 'laki-laki' ? 'fa-mars text-primary' : 'fa-venus text-danger' }} mr-1"></i>
                                {{ ucfirst($p->guru->user->jenis_kelamin) }}
                            </td>
                            <td class="text-center">
                                <form action="{{ route('pengawas.destroy', $p->id) }}" method="POST" id="delete-form-{{ $p->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDelete({{ $p->id }}, '{{ $p->guru->user->nama }}')"
                                            style="border-radius: 8px;">
                                        <i class="fas fa-trash-alt mr-1"></i> Cabut Tugas
                                    </button>
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
<div class="modal fade" id="modalTambahPengawas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold">Pilih Guru Menjadi Pengawas</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('pengawas.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="small font-weight-bold">Cari Nama Guru / NIP</label>
                        <select name="guru_id" class="form-control select2" required style="width: 100%;">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($guru_tersedia as $g)
                                <option value="{{ $g->id }}">{{ $g->nip }} - {{ $g->user->nama }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-2 d-block small font-italic">
                            * Hanya guru yang belum terdaftar sebagai pengawas yang muncul di sini.
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light font-weight-bold px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow">Jadikan Pengawas</button>
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
        $('#table-pengawas').DataTable();
        // Inisialisasi Select2 jika kamu menggunakannya agar pencarian guru lebih mudah
        $('.select2').select2({ theme: 'bootstrap4' });
    });

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Cabut Tugas?',
            text: name + " tidak akan lagi memiliki akses sebagai pengawas ujian.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            confirmButtonText: 'Ya, Cabut!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) { document.getElementById('delete-form-' + id).submit(); }
        });
    }
</script>
@endpush