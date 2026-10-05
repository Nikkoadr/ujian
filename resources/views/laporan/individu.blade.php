@extends('layouts.app')
@section('title', 'Cetak Laporan Individu')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Cetak Laporan Individu</h1>
            <p class="text-muted small mb-0">
                Pilih kelas, centang siswa, lalu cetak rapor per siswa.
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.individu') }}" class="row">
                <div class="col-md-4 mb-2">
                    <label class="small font-weight-bold">Periode Ujian</label>
                    <select name="periode_ujian_id" class="form-control" style="border-radius: 10px;">
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}" {{ (string) $periodeId === (string) $p->id || (! $periodeId && $p->is_active) ? 'selected' : '' }}>{{ $p->nama_periode }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="small font-weight-bold">Kelas</label>
                    <select name="kelas_id" class="form-control" style="border-radius: 10px;">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ (string) $kelasId === (string) $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block font-weight-bold" style="border-radius: 10px;">
                        <i class="fas fa-search mr-1"></i> Tampilkan Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($kelasId)
    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Siswa ({{ $siswas->count() }})
            </h6>
            <button type="button" class="btn btn-sm btn-success shadow-sm font-weight-bold" onclick="cetakTerpilih()" style="border-radius: 10px;">
                <i class="fas fa-print mr-1"></i> Cetak Terpilih
            </button>
        </div>
        <div class="card-body">
            <form id="formCetak" method="GET" action="{{ route('laporan.individu.cetak') }}">
                <input type="hidden" name="periode_ujian_id" value="{{ $periodeId }}">
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                <iframe id="frameCetak" title="Cetak Laporan" style="display:none;"></iframe>
                <div class="table-responsive">
                    <table class="table table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr class="bg-light text-dark">
                                <th width="5%" class="text-center">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th width="5%">No</th>
                                <th>Nama Lengkap</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswas as $siswa)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $siswa->id }}" class="checkItem">
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td class="font-weight-bold text-dark">{{ $siswa->nama_siswa }}</td>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->nisn }}</td>
                                <td>
                                    <span class="badge badge-light p-2 border">{{ $siswa->nama_kelas }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada siswa di kelas ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('checkAll')?.addEventListener('change', function () {
        document.querySelectorAll('.checkItem').forEach(function (box) {
            box.checked = document.getElementById('checkAll').checked;
        });
    });

    function cetakTerpilih() {
        const terpilih = [...document.querySelectorAll('.checkItem:checked')].map((box) => box.value);

        if (terpilih.length === 0) {
            alert('Centang minimal satu siswa terlebih dahulu.');
            return;
        }

        // Muat hasil cetak ke iframe lalu print dari sana (tanpa buka tab baru).
        const params = new URLSearchParams();
        params.append('periode_ujian_id', '{{ $periodeId }}');
        params.append('kelas_id', '{{ $kelasId }}');
        terpilih.forEach((id) => params.append('ids[]', id));

        const frame = document.getElementById('frameCetak');
        frame.onload = function () {
            frame.contentWindow.focus();
            frame.contentWindow.print();
        };
        frame.src = "{{ route('laporan.individu.cetak') }}?" + params.toString();
    }
</script>
@endpush
