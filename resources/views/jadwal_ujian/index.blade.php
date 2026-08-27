@extends('layouts.app')

@section('title', 'Jadwal Ujian')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Jadwal Ujian</h1>
        @if(Gate::allows('admin'))
        <div class="d-flex align-items-center">
            <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahUjian" style="border-radius: 10px; padding: 0.4rem 1rem;">
                <i class="fas fa-calendar-plus fa-sm mr-2"></i> Buat Jadwal Ujian
            </button>
        </div>
        @endif
    </div>

    {{-- Info periode aktif --}}
    @if($periodeAktif)
        <div class="alert alert-primary border-0 shadow-sm" style="border-radius: 15px;">
            <i class="fas fa-info-circle mr-2"></i> Menampilkan ujian untuk periode: <strong>{{ $periodeAktif->nama_periode }}</strong>
            ({{ \Carbon\Carbon::parse($periodeAktif->tanggal_mulai)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($periodeAktif->tanggal_selesai)->format('d-m-Y') }})
        </div>
    @else
        <div class="alert alert-warning border-0 shadow-sm" style="border-radius: 15px;">
            <i class="fas fa-exclamation-triangle mr-2"></i> Belum ada periode ujian yang aktif. Silakan aktifkan periode terlebih dahulu.
        </div>
    @endif

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Ujian & Progress Soal</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabelUjian" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-light text-dark">
                            <th>No</th>
                            <th>Kode & Nama Ujian</th>
                            <th>Tanggal & Jam</th>
                            <th>Durasi</th>
                            <th class="text-center">Jumlah Soal</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ujians as $ujian)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="font-weight-bold text-primary">{{ $ujian->kode_ujian }}</div>
                                <div class="text-dark font-weight-bold">{{ $ujian->nama_ujian }}</div>
                                <small class="text-muted">Periode: {{ $ujian->periodeUjian->nama_periode ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="small">
                                    <i class="fas fa-calendar-day fa-xs mr-1 text-muted"></i> 
                                    {{ \Carbon\Carbon::parse($ujian->tanggal_ujian)->translatedFormat('d F Y') }}<br>
                                    <i class="fas fa-clock fa-xs mr-1 text-muted"></i> 
                                    {{ \Carbon\Carbon::parse($ujian->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($ujian->jam_selesai)->format('H:i') }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light p-2 border">
                                    <i class="far fa-hourglass mr-1"></i> 
                                    {{-- Jika durasi berupa selisih jam_mulai dan jam_selesai --}}
                                    {{ \Carbon\Carbon::parse($ujian->jam_mulai)->diff(\Carbon\Carbon::parse($ujian->jam_selesai))->format('%H:%I') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="h5 mb-0 font-weight-bold {{ $ujian->soals_count > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $ujian->soals_count ?? 0 }}
                                </div>
                                <div class="small text-uppercase font-weight-bold opacity-50" style="font-size: 10px;">Butir Soal</div>
                            </td>
                            <td>
                                @if($ujian->status == 'aktif')
                                    <span class="badge badge-success px-3 py-2">Aktif</span>
                                @else
                                    <span class="badge badge-danger px-3 py-2">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #eaecf4;">
                                    <a href="{{ route('soal.index', $ujian->id) }}" 
                                       class="btn btn-sm btn-white text-success border-right px-3" 
                                       title="Kelola Soal Ujian">
                                        <i class="fas fa-file-signature"></i>
                                    </a>
                                    @if(Gate::allows('admin'))
                                    <a href="{{ route('ujian.edit', $ujian->id) }}" 
                                       class="btn btn-sm btn-white text-primary border-right px-3" 
                                       title="Edit Jadwal">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-white text-danger px-3" 
                                            onclick="confirmDelete({{ $ujian->id }}, '{{ $ujian->nama_ujian }}')"
                                            title="Hapus Ujian">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $ujian->id }}" action="{{ route('ujian.destroy', $ujian->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada jadwal ujian.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH UJIAN --}}
@if(Gate::allows('admin'))
<div class="modal fade" id="modalTambahUjian" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold text-gray-800"><i class="fas fa-calendar-plus text-primary mr-2"></i> Tambah Jadwal Ujian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('jadwal-ujian.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Periode Ujian</label>
                            <select name="periode_ujian_id" class="form-control @error('periode_ujian_id') is-invalid @enderror" required>
                                <option value="">Pilih Periode</option>
                                @foreach($periodeUjian as $periode)
                                    <option value="{{ $periode->id }}" {{ old('periode_ujian_id') == $periode->id ? 'selected' : '' }}>
                                        {{ $periode->nama_periode }} ({{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d-m-Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('periode_ujian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Kode Ujian</label>
                            <input type="text" name="kode_ujian" class="form-control @error('kode_ujian') is-invalid @enderror" value="{{ old('kode_ujian') }}" placeholder="E.g. BIN-X-1" required>
                            @error('kode_ujian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Nama Ujian</label>
                        <input type="text" name="nama_ujian" class="form-control @error('nama_ujian') is-invalid @enderror" value="{{ old('nama_ujian') }}" placeholder="E.g. Bahasa Indonesia" required>
                        @error('nama_ujian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Tanggal Ujian</label>
                            <input type="date" name="tanggal_ujian" class="form-control @error('tanggal_ujian') is-invalid @enderror" value="{{ old('tanggal_ujian') }}" required>
                            @error('tanggal_ujian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror" value="{{ old('jam_mulai') }}" required>
                            @error('jam_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="small font-weight-bold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror" value="{{ old('jam_selesai') }}" required>
                            @error('jam_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Durasi (HH:mm:ss)</label>
                        <input type="time" name="durasi" step="1" class="form-control @error('durasi') is-invalid @enderror" value="{{ old('durasi') }}" required>
                        @error('durasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Tingkat</label>
                            <select name="tingkat_id" class="form-control @error('tingkat_id') is-invalid @enderror" required>
                                <option value="">Pilih Tingkat</option>
                                @foreach($tingkat as $level)
                                    <option value="{{ $level->id }}" {{ old('tingkat_id') == $level->id ? 'selected' : '' }}>{{ $level->nama_tingkat }}</option>
                                @endforeach
                            </select>
                            @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Kompetensi Keahlian</label>
                            <select name="kompetensi_keahlian_id" class="form-control @error('kompetensi_keahlian_id') is-invalid @enderror">
                                <option value="">Pilih Kompetensi Keahlian</option>
                                @foreach($kompetensiKeahlian as $keahlian)
                                    <option value="{{ $keahlian->id }}" {{ old('kompetensi_keahlian_id') == $keahlian->id ? 'selected' : '' }}>{{ $keahlian->nama_kompetensi }}</option>
                                @endforeach
                            </select>
                            @error('kompetensi_keahlian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Status</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#tabelUjian').DataTable({
            "language": {
                "search": "Cari Ujian:",
                "lengthMenu": "Tampil _MENU_",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            }
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        @endif

        @if(session('error') || $errors->any())
            Toast.fire({ icon: 'error', title: "{{ session('error') ?? 'Periksa kembali inputan Anda.' }}" });
            @if($errors->any())
                $('#modalTambahUjian').modal('show');
            @endif
        @endif
    });

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Jadwal Ujian?',
            text: "Jadwal " + name + " dan semua soal di dalamnya akan dihapus permanen!",
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