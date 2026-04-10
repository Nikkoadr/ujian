@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route($route_name) }}" method="GET" id="form-filter">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Jenis Lembaga</label>
                        <select name="jenis_lembaga_id" class="form-control">
                            <option value="">Semua Jenis</option>
                            @foreach($jenisLembaga as $jk)
                                <option value="{{ $jk->id }}" {{ request('jenis_lembaga_id') == $jk->id ? 'selected' : '' }}>
                                    {{ $jk->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Status Izin</label>
                        <select name="status" class="form-control" {{ $route_name == 'laporan.expired' ? 'disabled' : '' }}>
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="warning" {{ request('status') == 'warning' ? 'selected' : '' }}>Hampir Habis</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                        </select>
                        @if($route_name == 'laporan.expired')
                            <input type="hidden" name="status" value="expired">
                        @endif
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Masa Berlaku (Mulai)</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Masa Berlaku (Sampai)</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Tampilkan
                    </button>
                    
                    <a href="{{ route($route_name) }}" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Reset
                    </a>

                    <button type="button" class="btn btn-success float-right" onclick="cetakLaporan()">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="datatable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>NPSN</th>
                            <th>Nama Lembaga</th>
                            <th>Jenis</th>
                            <th>Pengelola</th>
                            <th>Sertifikat</th>
                            <th>Masa Berlaku</th>
                            <th>Status Izin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->npsn }}</td>
                            <td>{{ $item->nama_lembaga }}</td>
                            <td>{{ $item->jenis->nama ?? '-' }}</td>
                            <td>{{ $item->pengelola }}</td>
                            <td>{{ $item->izin->no_sertifikat ?? '-' }}</td>
                            <td>
                                {{ $item->izin && $item->izin->masa_berlaku ? \Carbon\Carbon::parse($item->izin->masa_berlaku)->format('d M Y') : '-' }}
                            </td>
                            <td>
                                @if($item->status_teks)
                                    <span class="badge badge-{{ $item->status_label }}">
                                        {{ $item->status_teks }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
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
        $('#datatable').DataTable();
    });

    function cetakLaporan() {
        let form = document.getElementById('form-filter');
        let baseUrl = "{{ route('laporan.cetak') }}";
        
        // Menggunakan URLSearchParams agar filter yang kosong tidak ikut dikirim
        let formData = new FormData(form);
        let params = new URLSearchParams();
        
        for (let pair of formData.entries()) {
            if (pair[1]) {
                params.append(pair[0], pair[1]);
            }
        }
        
        window.open(baseUrl + '?' + params.toString(), '_blank');
    }
</script>
@endpush