@extends('layouts.app')
@section('title', 'Data Guru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Tenaga Pendidik</h1>
        <button class="btn btn-sm btn-primary shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-user-plus fa-sm text-white-50 mr-2"></i> Tambah Guru
        </button>
    </div>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Guru / Staf</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabelGuru" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-light">
                            <th width="5%">No</th>
                            <th width="35%">Info Pendidik</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan / Mapel</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $guru)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="small">
                                    <span class="badge badge-dark mb-1">NIP: {{ $guru->nip ?? '-' }}</span><br>
                                    <span class="text-muted"><i class="fas fa-envelope fa-xs mr-1"></i> {{ $guru->user->email }}</span><br>
                                    <span class="text-muted"><i class="fas fa-phone fa-xs mr-1"></i> {{ $guru->user->no_telp ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $guru->user->nama }}</div>
                                <div class="small text-muted text-capitalize">{{ $guru->user->jenis_kelamin }}</div>
                            </td>
                            <td>
                                <span class="badge badge-outline-primary p-2 border border-primary text-primary" style="border-radius: 6px;">
                                    {{ $guru->jabatan }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <a href="#" class="btn btn-sm btn-white text-primary border-right" title="Detail/Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-white text-danger" title="Hapus Akun">
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
        $('#tabelGuru').DataTable({
            "language": {
                "search": "Cari Guru:",
                "lengthMenu": "Tampilkan _MENU_",
                "zeroRecords": "Data guru tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            }
        });
    });
</script>
@endpush