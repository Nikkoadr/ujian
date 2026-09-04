@extends('layouts.app')

@section('title', 'Jadwal Ujian')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Jadwal Ujian</h1>
        @if(Gate::allows('admin'))
        <div class="d-flex align-items-center">
            <button class="btn btn-sm btn-success shadow-sm mr-2" data-toggle="modal" data-target="#modalImport" style="border-radius: 10px; padding: 0.4rem 1rem;">
                <i class="fas fa-file-excel fa-sm text-white-50 mr-2"></i> Import Excel
            </button>
            <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahJadwal" style="border-radius: 10px; padding: 0.4rem 1rem;">
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
                        @forelse($mapels as $mapel)
                        @php
                            $jadwal = $mapel->jadwal->first(); // ambil satu jadwal (asumsi satu mapel satu jadwal di periode aktif)
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="font-weight-bold text-primary">{{ $mapel->kode_mapel }}</div>
                                <div class="text-dark font-weight-bold">{{ $mapel->nama_mapel }}</div>
                                <small class="text-muted">
                                    Periode: {{ $periodeAktif->nama_periode ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <div class="small">
                                    <i class="fas fa-calendar-day fa-xs mr-1 text-muted"></i> 
                                    {{ $jadwal ? \Carbon\Carbon::parse($jadwal->tanggal_ujian)->translatedFormat('d F Y') : '-' }}<br>
                                    <i class="fas fa-clock fa-xs mr-1 text-muted"></i> 
                                    {{ $jadwal ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '-' }} - 
                                    {{ $jadwal ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '-' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light p-2 border">
                                    <i class="far fa-hourglass mr-1"></i> 
                                    {{ $mapel->durasi ? substr($mapel->durasi, 0, 5) : '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="h5 mb-0 font-weight-bold {{ ($mapel->jumlah_soal_terpilih ?? 0) > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $mapel->jumlah_soal_terpilih ?? 0 }}
                                </div>
                                <div class="small text-uppercase font-weight-bold opacity-50" style="font-size: 10px;">Butir Soal</div>
                            </td>
                            <td>
                                @if($jadwal)
                                    @if($jadwal->status == 'aktif')
                                        <span class="badge badge-success px-3 py-2">Aktif</span>
                                    @else
                                        <span class="badge badge-danger px-3 py-2">Nonaktif</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary px-3 py-2">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #eaecf4;">
                                    {{-- Kelola Soal (arah ke create soal dengan bawa jadwal_id & mapel_id) --}}
                                    @if($jadwal)
                                    <a href="{{ route('soal.manage', ['jadwalId' => $jadwal->id, 'mapelId' => $mapel->id]) }}" 
                                        class="btn btn-sm btn-white text-success border-right px-3" 
                                        title="Kelola Soal Ujian">
                                        <i class="fas fa-file-signature"></i>
                                    </a>

                                        @if(Gate::allows('admin'))
                                            {{-- Edit Jadwal --}}
                                            <a href="{{ route('jadwal-ujian.edit', $jadwal->id) }}" 
                                            class="btn btn-sm btn-white text-primary border-right px-3" 
                                            title="Edit Jadwal">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- Hapus Jadwal --}}
                                            <button type="button" 
                                                    class="btn btn-sm btn-white text-danger px-3" 
                                                    onclick="confirmDelete({{ $jadwal->id }}, '{{ $mapel->nama_mapel }}')"
                                                    title="Hapus Jadwal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $jadwal->id }}" 
                                                action="{{ route('jadwal-ujian.destroy', $jadwal->id) }}" 
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    @else
                                        <span class="btn btn-sm btn-light text-muted px-3">Tidak ada jadwal</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada jadwal ujian untuk periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- MODAL IMPORT EXCEL --}}
@if(Gate::allows('admin'))
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-gray-800" id="modalImportLabel">
                    <i class="fas fa-file-excel text-success mr-2"></i>Import Jadwal Ujian
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('jadwal-ujian.import') }}" method="POST" enctype="multipart/form-data" id="formImport">
                @csrf
                <div class="modal-body px-4">
                    <a href="{{ asset('assets/format_excel/mapel.xlsx') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download mr-1"></i> Download Format Excel
                    </a>

                    <div class="form-group mt-4">
                        <label class="small font-weight-bold text-dark">Pilih File Excel</label>
                        <div class="custom-file">
                            <input type="file" name="file" class="custom-file-input" id="importFile" required>
                            <label class="custom-file-label" for="importFile" id="fileLabel">Pilih file...</label>
                        </div>
                        <small class="text-muted mt-2 d-block">Gunakan format .xlsx atau .xls</small>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light font-weight-bold" data-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm" style="border-radius: 10px;">
                        <i class="fas fa-upload mr-2"></i> Upload & Proses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
{{-- MODAL TAMBAH JADWAL UJIAN --}}
@if(Gate::allows('admin'))
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-hidden="true">
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
                                        {{ $periode->nama_periode }}
                                    </option>
                                @endforeach
                            </select>
                            @error('periode_ujian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold">Pilih Mata Pelajaran</label>
                            <select name="mapel_id" class="form-control @error('mapel_id') is-invalid @enderror" required>
                                <option value="">Pilih Mapel</option>
                                @foreach($semuaMapel as $m)
                                    <option value="{{ $m->id }}" {{ old('mapel_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->kode_mapel }} - {{ $m->nama_mapel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
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
                $('#modalTambahJadwal').modal('show');
            @endif
        @endif

        $('#importFile').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('#fileLabel').text(fileName).addClass('selected');
            } else {
                $('#fileLabel').text('Pilih file...').removeClass('selected');
            }
        });

        // Reset saat modal ditutup
        $('#modalImport').on('hidden.bs.modal', function() {
            $('#importFile').val('');
            $('#fileLabel').text('Pilih file...').removeClass('selected');
        });

        // Reset saat tombol batal diklik
        $('#modalImport .btn-light').on('click', function() {
            setTimeout(function() {
                $('#importFile').val('');
                $('#fileLabel').text('Pilih file...').removeClass('selected');
            }, 100);
        });
    });

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Jadwal Ujian?',
            text: "Jadwal untuk " + name + " akan dihapus permanen!",
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