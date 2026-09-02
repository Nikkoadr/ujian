@extends('layouts.app')

@section('title', 'Daftar Paket Soal')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Daftar Paket Soal</h1>
        <a href="{{ route('soal.create') }}" class="btn btn-sm btn-primary shadow-sm" style="border-radius: 10px; padding: 0.4rem 1rem;">
            <i class="fas fa-plus fa-sm mr-2"></i> Tambah Paket Soal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Semua Paket Soal</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabelSoal" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-light text-dark">
                            <th>No</th>
                            <th>Periode</th>
                            <th>Jadwal</th>
                            <th>Mapel</th>
                            <th>Jumlah Pertanyaan</th>
                            <th>Pertanyaan Dipilih</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($soal as $key => $s)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $s->periodeUjian->nama_periode ?? '-' }}</td>
                            <td>{{ $s->jadwal->tanggal_ujian ?? '-' }} ({{ $s->jadwal->jam_mulai ?? '' }} - {{ $s->jadwal->jam_selesai ?? '' }})</td>
                            <td>{{ $s->mapel->nama_mapel ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge badge-primary badge-pill px-3 py-2">{{ $s->bankPertanyaan->count() }}</span>
                            </td>
                            <td>
                                @if($s->bankPertanyaan->count() > 0)
                                    <ul class="list-unstyled" style="max-height: 100px; overflow-y: auto;">
                                        @foreach($s->bankPertanyaan as $bp)
                                            <li><small>{{ $bp->pertanyaan }}</small></li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">Belum ada pertanyaan</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #eaecf4;">
                                    <a href="{{ route('soal.edit', $s) }}" class="btn btn-sm btn-white text-primary border-right px-3" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-white text-danger px-3" 
                                            onclick="confirmDeleteSoal({{ $s->id }}, '{{ $s->mapel->nama_mapel ?? '' }}')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-soal-{{ $s->id }}" action="{{ route('soal.destroy', $s) }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada paket soal dibuat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#tabelSoal').DataTable({
            "language": {
                "search": "Cari Paket Soal:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            }
        });
    });

    function confirmDeleteSoal(id, name) {
        Swal.fire({
            title: 'Hapus Paket Soal?',
            text: "Paket soal untuk " + name + " akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-soal-' + id).submit();
            }
        });
    }
</script>
@endpush