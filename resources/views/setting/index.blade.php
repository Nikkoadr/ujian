@extends('layouts.app')
@section('title', 'Konfigurasi Sistem Ujian')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                Pengaturan Ujian CBT
            </h1>
            <p class="text-muted small mb-0">
                Konfigurasi keamanan dan perilaku sistem ujian
            </p>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0">
                <li class="breadcrumb-item">
                    <a href="#">Admin</a>
                </li>
                <li class="breadcrumb-item active">
                    Setting
                </li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">

            <div class="card border-0 shadow-lg" style="border-radius: 24px; overflow:hidden;">

                <div class="card-header bg-white border-0 py-4">
                    <div class="d-flex align-items-center">

                        <div class="bg-primary text-white d-flex align-items-center justify-content-center mr-3"
                             style="width:60px; height:60px; border-radius:18px;">
                            <i class="fas fa-cogs fa-lg"></i>
                        </div>

                        <div>
                            <h5 class="font-weight-bold text-dark mb-1">
                                Konfigurasi CBT
                            </h5>

                            <p class="text-muted small mb-0">
                                Atur sistem keamanan dan perilaku ujian secara global
                            </p>
                        </div>

                    </div>
                </div>

                <form action="{{ route('setting.update') }}" method="POST">
                    @csrf

                    <div class="card-body px-5 py-4">

                        {{-- MAX PELANGGARAN --}}
                        <div class="setting-box mb-4">

                            <div class="row align-items-center">

                                <div class="col-md-8 mb-3 mb-md-0">

                                    <label class="font-weight-bold text-dark d-block mb-1">
                                        Maksimal Pelanggaran
                                    </label>

                                    <small class="text-muted">
                                        Jumlah maksimal siswa berpindah tab / keluar layar
                                        sebelum akun diblokir otomatis.
                                    </small>

                                </div>

                                <div class="col-md-4">

                                    <div class="input-group">

                                        <input
                                            type="number"
                                            name="max_pelanggaran"
                                            value="{{ $setting->max_pelanggaran ?? 5 }}"
                                            class="form-control form-control-lg text-center font-weight-bold"
                                            min="1"
                                            required
                                        >

                                        <div class="input-group-append">
                                            <span class="input-group-text bg-light">
                                                Kali
                                            </span>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- TOMBOL SELESAI --}}
                        <div class="setting-box mb-4">

                            <div class="row align-items-center">

                                <div class="col-md-8 mb-3 mb-md-0">

                                    <label class="font-weight-bold text-dark d-block mb-1">
                                        Kunci Tombol Selesai
                                    </label>

                                    <small class="text-muted">
                                        Tombol selesai hanya aktif saat sisa waktu mencapai batas tertentu.
                                    </small>

                                </div>

                                <div class="col-md-4">

                                    <div class="input-group">

                                        <input
                                            type="number"
                                            name="max_tombol_selesai"
                                            value="{{ $setting->max_tombol_selesai ?? 300 }}"
                                            class="form-control form-control-lg text-center font-weight-bold"
                                            min="0"
                                            required
                                        >

                                        <div class="input-group-append">
                                            <span class="input-group-text bg-light">
                                                Detik
                                            </span>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- ANTI NYONTEK --}}
                        <div class="setting-box mb-4">

                            <div class="row align-items-center">

                                <div class="col-md-8 mb-3 mb-md-0">

                                    <label class="font-weight-bold text-dark d-block mb-1">
                                        Sistem Anti Nyontek
                                    </label>

                                    <small class="text-muted">
                                        Aktifkan deteksi pindah tab, minimize browser,
                                        klik kanan, dan shortcut developer tools.
                                    </small>

                                </div>

                                <div class="col-md-4 text-md-right">

                                    <div class="custom-control custom-switch custom-switch-lg d-inline-block">

                                        <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            id="antiNyontek"
                                            name="anti_nyontek"
                                            {{ ($setting->anti_nyontek ?? true) ? 'checked' : '' }}
                                        >

                                        <label class="custom-control-label font-weight-bold"
                                               for="antiNyontek">
                                            Aktif
                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- INFO --}}
                        <div class="alert alert-primary border-0 shadow-sm mb-0"
                             style="border-radius:16px;">

                            <div class="d-flex align-items-start">

                                <div class="mr-3">
                                    <i class="fas fa-info-circle fa-lg"></i>
                                </div>

                                <div>
                                    <div class="font-weight-bold mb-1">
                                        Informasi Sistem
                                    </div>

                                    <small>
                                        Perubahan konfigurasi akan langsung diterapkan
                                        pada seluruh ujian yang sedang berjalan.
                                    </small>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="card-footer bg-light border-0 py-4 px-5 text-right">

                        <button type="reset"
                                class="btn btn-light px-4 font-weight-bold mr-2">
                            Reset
                        </button>

                        <button type="submit"
                                class="btn btn-primary px-5 font-weight-bold shadow">

                            <i class="fas fa-save mr-2"></i>
                            Simpan Konfigurasi

                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>

.setting-box{
    border:1px solid #e3e6f0;
    border-radius:18px;
    padding:24px;
    transition:all .25s ease;
    background:#fff;
}

.setting-box:hover{
    border-color:#4e73df;
    box-shadow:0 5px 20px rgba(78,115,223,.08);
}

.custom-switch-lg .custom-control-label::before{
    width:3rem;
    height:1.6rem;
    border-radius:2rem;
}

.custom-switch-lg .custom-control-label::after{
    width:calc(1.6rem - 4px);
    height:calc(1.6rem - 4px);
    border-radius:50%;
}

.custom-switch-lg .custom-control-input:checked ~ .custom-control-label::after{
    transform:translateX(1.35rem);
}

.form-control-lg{
    border-radius:12px;
}

.input-group-text{
    border-top-right-radius:12px;
    border-bottom-right-radius:12px;
}

.btn{
    border-radius:12px;
}

</style>
@endpush