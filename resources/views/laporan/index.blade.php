@extends('layouts.app')
@section('title', 'Laporan Harian Hasil Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Laporan Hasil Ujian</h1>
            <p class="text-muted small mb-0">Memantau partisipasi dan nilai siswa secara real-time.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('laporan.export', request()->all()) }}" class="btn btn-sm btn-success shadow-sm">
                <i class="fas fa-file-excel fa-sm text-white-50 mr-1"></i> Download Excel
            </a>
        </div>
    </div>

    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter Data Harian</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.index') }}" method="GET" class="row">
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold">Tanggal Ujian</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggal ?? date('Y-m-d') }}">
                </div>
                
                <div class="col-md-3 mb-2">
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

                <div class="col-md-3 mb-2">
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
                    <button type="submit" class="btn btn-primary btn-block shadow-sm font-weight-bold">
                        <i class="fas fa-search mr-1"></i> Tampilkan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Peserta Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $results->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 20%;">Identitas Siswa</th>
                            <th style="width: 15%;">Mata Pelajaran</th>
                            <th class="text-center" style="width: 20%;">Progres Jawaban</th>
                            <th class="text-center" style="width: 10%;">B / S</th>
                            <th class="text-center" style="width: 10%;">Nilai</th>
                            <th class="text-center" style="width: 15%;">Status Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $res)
                        <tr>
                            <td>
                                <div class="font-weight-bold text-gray-800">{{ $res->nama_siswa }}</div>
                                <div class="small text-muted">NIS: {{ $res->nis }}</div>
                                <div class="badge badge-light border text-primary">{{ $res->nama_kelas }}</div>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $res->nama_mapel }}</div>
                                <div class="small text-muted">
                                    <i class="far fa-clock mr-1"></i> {{ date('H:i', strtotime($res->tanggal_ujian)) }} WIB
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Terisi: {{ $res->dijawab }} / {{ $res->total_soal }}</span>
                                    <span class="font-weight-bold">{{ round(($res->dijawab / max($res->total_soal, 1)) * 100) }}%</span>
                                </div>
                                <div class="progress progress-sm shadow-sm">
                                    @php $persen = ($res->total_soal > 0) ? ($res->dijawab / $res->total_soal) * 100 : 0; @endphp
                                    <div class="progress-bar {{ $persen < 50 ? 'bg-danger' : 'bg-info' }}" role="progressbar" style="width: {{ $persen }}%"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="text-success font-weight-bold">{{ $res->benar }}</span>
                                <span class="text-muted mx-1">/</span>
                                <span class="text-danger font-weight-bold">{{ $res->total_soal - $res->benar }}</span>
                            </td>
                            <td class="text-center">
                                <h5 class="mb-0 font-weight-bold {{ $res->nilai >= 75 ? 'text-primary' : 'text-orange' }}" style="color: {{ $res->nilai < 75 ? '#f6c23e' : '' }}">
                                    {{ $res->nilai }}
                                </h5>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $res->status_color }} px-3 py-2 text-uppercase shadow-sm" style="font-size: 10px; min-width: 100px;">
                                    <i class="fas fa-info-circle mr-1"></i> {{ $res->status_label }}
                                </span>
                                @if($res->status_label == 'DITINGGALKAN')
                                    <div class="mt-1" style="font-size: 9px; color: #e74a3b;">
                                        <i class="fas fa-exclamation-triangle"></i> Tidak ada aktivitas lanjut
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3"></i>
                                <p>Tidak ada data ujian pada tanggal ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .card-header, .sidebar, .navbar, .card-body form {
            display: none !important;
        }
        .card {
            border: none !important;
            shadow: none !important;
        }
        .table-responsive {
            overflow: visible !important;
        }
        body {
            background-color: white !important;
        }
    }
</style>
@endsection