<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" style="width:40px;">
        </div>
        <div class="sidebar-brand-text mx-3">CBT<sup>SMK</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-th-large"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Data Master
    </div>

    <li class="nav-item {{ request()->routeIs('guru.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('guru.index') }}">
            <i class="fas fa-fw fa-user-tie"></i>
            <span>Data Guru</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('pengawas.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pengawas.index') }}">
            <i class="fas fa-fw fa-user-shield"></i>
            <span>Data Pengawas</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Akademik
    </div>

    <li class="nav-item {{ request()->routeIs('kelas.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kelas.index') }}">
            <i class="fas fa-fw fa-door-open"></i>
            <span>Manajemen Kelas</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('siswa.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Data Siswa</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('mapel.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('mapel.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Mata Pelajaran</span>
        </a>
    </li>

    {{-- MENU TOKEN BARU --}}
    <li class="nav-item {{ request()->routeIs('token.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('token.index') }}">
            <i class="fas fa-fw fa-key"></i>
            <span>Token Ujian</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Hasil & Laporan
    </div>

    <li class="nav-item {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('laporan.index') }}">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Laporan Nilai</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>