@extends('layouts.app')
@section('title', 'Laporan Hasil Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Laporan Hasil Ujian</h1>
        <button onclick="window.print()" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-print fa-sm text-white-50"></i> Cetak Laporan
        </button>
    </div>

    <div class="card shadow mb-4 border-left-primary">
        <div class="card-body">
            <form action="{{ route('laporan.index') }}" method="GET" class="row">
                <div class="col-md-5 mb-2">
                    <label class="small font-weight-bold">Mata Pelajaran</label>
                    <select class="form-control select2" name="mapel_id">
                        <option value="">-- Semua Mata Pelajaran --</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="small font-weight-bold">Kelas</label>
                    <select class="form-control" name="kelas_id">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block shadow-sm">
                        <i class="fas fa-search mr-1"></i> Filter Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Mapel</th>
                            <th class="text-center">Benar</th>
                            <th class="text-center">Salah</th>
                            <th class="text-center">Nilai Akhir</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $res)
                        <tr>
                            <td>{{ $res->nis }}</td>
                            <td class="font-weight-bold text-gray-800">{{ $res->nama_siswa }}</td>
                            <td>{{ $res->nama_kelas }}</td>
                            <td><small class="badge badge-light border">{{ $res->nama_mapel }}</small></td>
                            <td class="text-center text-success">{{ $res->benar }}</td>
                            <td class="text-center text-danger">{{ $res->salah }}</td>
                            <td class="text-center">
                                <h5 class="mb-0 font-weight-bold {{ $res->nilai >= 75 ? 'text-primary' : 'text-warning' }}">
                                    {{ $res->nilai }}
                                </h5>
                            </td>
                            <td class="text-center text-uppercase">
                                @if($res->status_ujian == 'selesai')
                                    <span class="badge badge-success px-3">Selesai</span>
                                @else
                                    <span class="badge badge-warning px-3">Proses</span>
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