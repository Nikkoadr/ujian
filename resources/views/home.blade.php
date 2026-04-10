@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
        <h1 class="h4 mb-0 text-gray-800 font-weight-bold">Ringkasan Data CBT</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-download fa-sm text-white-50 mr-2"></i> Laporan Cepat
        </a>
    </div>

    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 15px; border-left: 4px solid #4e73df !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-[10px] font-weight-bold text-primary text-uppercase mb-1 tracking-wider">Total Siswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">1,240</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-200"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 15px; border-left: 4px solid #1cc88a !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-[10px] font-weight-bold text-success text-uppercase mb-1 tracking-wider">Total Guru</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">86</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-200"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 15px; border-left: 4px solid #36b9cc !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-[10px] font-weight-bold text-info text-uppercase mb-1 tracking-wider">Total Pengawas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">24</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-200"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-radius: 15px; border-left: 4px solid #f6c23e !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-[10px] font-weight-bold text-warning text-uppercase mb-1 tracking-wider">Akun Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">1,350</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-cog fa-2x text-gray-200"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                <div class="card-header py-3 bg-white border-0">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Status Pengguna (Users)</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="small font-weight-bold text-muted">Aktif <span class="float-right">95%</span></div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 95%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="small font-weight-bold text-muted">Tidak Aktif <span class="float-right">3%</span></div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 3%"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="small font-weight-bold text-muted">Diblokir <span class="float-right">2%</span></div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 2%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white;">
                <div class="card-body p-4">
                    <h5 class="font-weight-bold">Informasi Sistem</h5>
                    <p class="small opacity-75">Data ini ditarik secara statis berdasarkan skema database terbaru (Users, Siswa, Guru, Pengawas).</p>
                    <hr class="border-white opacity-25">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="font-weight-bold h4 mb-0">12</div>
                            <div class="text-[9px] text-uppercase opacity-75">Kelas</div>
                        </div>
                        <div class="col-4 border-left border-right border-white-50">
                            <div class="font-weight-bold h4 mb-0">32</div>
                            <div class="text-[9px] text-uppercase opacity-75">Mapel</div>
                        </div>
                        <div class="col-4">
                            <div class="font-weight-bold h4 mb-0">1.4k</div>
                            <div class="text-[9px] text-uppercase opacity-75">Total User</div>
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
    .opacity-75 { opacity: 0.75; }
    .opacity-25 { opacity: 0.25; }
    .border-white-50 { border-color: rgba(255,255,255,0.2) !important; }
</style>
@endsection