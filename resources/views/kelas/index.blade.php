@extends('layouts.app')
@section('title', 'Data Siswa')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Siswa</h1>
        <button class="btn btn-sm btn-primary shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Tambah Siswa
        </button>
    </div>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabelSiswa" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-light">
                            <th width="5%">No</th>
                            <th width="40%">Info Detail</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswas as $siswa)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="small">
                                    <strong>NISN:</strong> {{ $siswa->nisn }} | <strong>NIS:</strong> {{ $siswa->nis }}<br>
                                    <span class="text-muted"><i class="fas fa-envelope fa-xs"></i> {{ $siswa->user->email }}</span><br>
                                    <span class="text-muted text-capitalize"><i class="fas fa-venus-mars fa-xs"></i> {{ $siswa->user->jenis_kelamin }}</span>
                                </div>
                            </td>
                            <td class="font-weight-bold text-dark">{{ $siswa->user->nama }}</td>
                            <td><span class="badge badge-light p-2">{{ $siswa->kelas->nama_kelas }}</span></td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <a href="#" class="btn btn-sm btn-white text-primary border-right" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-white text-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tabelSiswa').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Cari Siswa:",
                "lengthMenu": "Tampilkan _MENU_ data",
            },
            "columnDefs": [
                { "orderable": false, "targets": 4 }
            ]
        });
    });
</script>
@endpush