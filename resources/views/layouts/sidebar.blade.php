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

    <li class="nav-item {{ request()->routeIs('bank-soal.*') || request()->is('bank-soal/*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('bank-soal.index') }}">
            <i class="fas fa-fw fa-database"></i>
            <span>Bank Soal</span>
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('periode_ujian.*') || request()->is('periode_ujian/*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('periode_ujian.index') }}">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span>Periode Ujian</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('jadwal-ujian.*') || request()->is('jadwal-ujian/*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('jadwal-ujian.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Jadwal Ujian</span>
        </a>
    </li>

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

    <li class="nav-item {{ request()->routeIs('ujian-handler.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('ujian-handler.index') }}">
            <i class="fas fa-fw fa-user-cog"></i>
            <span>Handler Ujian</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('laporan.index') }}">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Laporan Nilai</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <li class="nav-item {{ request()->routeIs('setting.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('setting.index') }}">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Pengaturan</span>
        </a>
    </li>

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>