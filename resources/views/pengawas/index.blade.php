@extends('layouts.app')
@section('title', 'Data Pengawas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Pengawas</h1>
        <button class="btn btn-sm btn-primary shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-user-shield fa-sm text-white-50 mr-2"></i> Tambah Pengawas
        </button>
    </div>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengawas Ujian</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabelPengawas" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-light">
                            <th width="5%">No</th>
                            <th width="35%">Info Identitas (NIP / Email)</th>
                            <th>Nama Pengawas</th>
                            <th>Status / Ruang</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $pengawas)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="small">
                                    {{-- Mengambil NIP dari relasi Guru --}}
                                    <span class="badge badge-secondary mb-1">NIP: {{ $pengawas->guru->nip ?? '-' }}</span><br>
                                    {{-- Mengambil Email dari relasi User melalui Guru --}}
                                    <span class="text-muted"><i class="fas fa-envelope fa-xs mr-1"></i> {{ $pengawas->guru->user->email }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $pengawas->guru->user->nama }}</div>
                                <div class="small text-muted">Guru {{ $pengawas->guru->jabatan }}</div>
                            </td>
                            <td>
                                <span class="badge badge-success p-2 shadow-sm" style="border-radius: 6px; font-size: 11px;">
                                    <i class="fas fa-check-circle mr-1"></i> Aktif
                                </span>
                            </td>
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
        $('#tabelPengawas').DataTable({
            "language": {
                "search": "Cari Pengawas:",
                "lengthMenu": "Tampilkan _MENU_",
                "zeroRecords": "Data pengawas tidak ditemukan"
            }
        });
    });
</script>
@endpush