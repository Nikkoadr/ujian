@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
        <h1 class="h4 mb-0 text-gray-800 font-weight-bold">Ringkasan Data CBT</h1>
        <a href="{{ route('laporan.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" style="border-radius: 10px; padding: 8px 15px;">
            <i class="fas fa-eye fa-sm text-white-50 mr-2"></i> Lihat Laporan Detail
        </a>
    </div>

    <div class="row">
        @php
            $cards = [
                ['title' => 'Total Siswa', 'value' => $total_siswa, 'icon' => 'user-graduate', 'color' => '#4e73df', 'text' => 'primary'],
                ['title' => 'Total Guru', 'value' => $total_guru, 'icon' => 'chalkboard-teacher', 'color' => '#1cc88a', 'text' => 'success'],
                ['title' => 'Total Pengawas', 'value' => $total_pengawas, 'icon' => 'user-shield', 'color' => '#36b9cc', 'text' => 'info'],
                ['title' => 'User Diblokir', 'value' => $status_blokir, 'icon' => 'user-slash', 'color' => '#e74a3b', 'text' => 'danger'],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 15px; border-left: 4px solid {{ $card['color'] }} !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-[10px] font-weight-bold text-{{ $card['text'] }} text-uppercase mb-1 tracking-wider">{{ $card['title'] }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($card['value']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-{{ $card['icon'] }} fa-2x text-gray-100"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                <div class="card-header py-3 bg-white border-0">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Keamanan Akun</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="small font-weight-bold text-muted">User Aktif <span class="float-right">{{ $p_aktif }}%</span></div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $p_aktif }}%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="small font-weight-bold text-muted">User Nonaktif <span class="float-right">{{ $p_non }}%</span></div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $p_non }}%"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="small font-weight-bold text-muted">Akun Diblokir <span class="float-right text-danger">{{ $p_blokir }}%</span></div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $p_blokir }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white;">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold">Statistik Infrastruktur</h5>
                    <p class="small opacity-75">Data ditarik secara dinamis dari database portal ujian untuk memantau beban sistem saat ini.</p>
                    <hr class="border-white opacity-25">
                    <div class="row text-center mt-4">
                        <div class="col-4">
                            <div class="font-weight-bold h4 mb-0">{{ $total_kelas }}</div>
                            <div class="text-[9px] text-uppercase opacity-75 tracking-tighter">Total Kelas</div>
                        </div>
                        <div class="col-4 border-left border-right border-white-50">
                            <div class="font-weight-bold h4 mb-0">{{ $total_mapel }}</div>
                            <div class="text-[9px] text-uppercase opacity-75 tracking-tighter">Mata Pelajaran</div>
                        </div>
                        <div class="col-4">
                            <div class="font-weight-bold h4 mb-0">{{ number_format($total_siswa) }}</div>
                            <div class="text-[9px] text-uppercase opacity-75 tracking-tighter">Siswa Terdaftar</div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="p-2 text-center" style="background: rgba(255,255,255,0.15); border-radius: 12px; font-size: 11px;">
                            <i class="fas fa-check-circle mr-2 text-success"></i> Database: <strong>SMK Muhammadiyah Kandanghaur</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-[10px] { font-size: 10px; }
    .text-[9px] { font-size: 9px; }
    .tracking-wider { letter-spacing: 0.1em; }
    .tracking-tighter { letter-spacing: -0.02em; }
    .opacity-75 { opacity: 0.75; }
    .opacity-25 { opacity: 0.25; }
    .border-white-50 { border-color: rgba(255,255,255,0.2) !important; }
</style>
@endsection