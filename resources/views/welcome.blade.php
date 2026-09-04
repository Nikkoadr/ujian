<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CBT Portal | {{ $school_name ?? 'SMK Muhammadiyah Kandanghaur' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            950: '#0c4a6e',
                        },
                        school: {
                            primary: '#0ea5e9',
                            secondary: '#fbbf24',
                            accent: '#38bdf8',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9, #38bdf8, #0ea5e9);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientShift 4s ease-in-out infinite;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(14, 165, 233, 0.15);
        }
        .floating-shapes {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-sky-50 via-white to-blue-50 dark:from-brand-950 dark:via-brand-900 dark:to-slate-900 text-slate-900 dark:text-slate-100 antialiased font-sans min-h-screen">

    <!-- Floating Shapes Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="floating-shapes w-[600px] h-[600px] bg-sky-400/20 -top-48 -right-48"></div>
        <div class="floating-shapes w-[500px] h-[500px] bg-blue-400/20 -bottom-48 -left-48"></div>
        <div class="floating-shapes w-[400px] h-[400px] bg-cyan-400/20 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
    </div>

    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 border-b border-slate-100/50 bg-white/70 backdrop-blur-xl dark:border-slate-800/50 dark:bg-brand-950/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-4 group">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-12 w-auto object-contain">
                    <div class="flex flex-col">
                        <span class="text-lg font-bold tracking-tight text-brand-600 dark:text-white leading-none">
                            {{ $school_name ?? 'SMK Muhammadiyah Kandanghaur' }}
                        </span>
                        <span class="text-[10px] font-semibold text-slate-500 tracking-[0.2em] uppercase">
                            Computer Based Test System
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('home') }}" class="bg-school-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-sky-600 transition-all shadow-lg shadow-sky-500/20">
                                🚀 Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-school-primary transition px-4 dark:text-slate-300">
                                Masuk
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-school-primary text-white px-7 py-2.5 rounded-xl text-sm font-bold hover:bg-sky-600 transition-all shadow-lg shadow-sky-500/20 active:scale-95">
                                    Daftar
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="relative pt-32 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Hero Section -->
            <div class="relative grid lg:grid-cols-2 gap-12 items-center">
                
                <!-- Left Column -->
                <div class="relative z-10">

                    
                    <h1 class="text-5xl lg:text-6xl font-extrabold leading-[1.1] mb-6">
                        Wujudkan Ujian <br>
                        <span class="gradient-text">Jujur & Mandiri.</span>
                    </h1>
                    
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-10 leading-relaxed max-w-xl">
                        Selamat datang di portal Computer Based Test (CBT) 
                        <strong class="text-slate-800 dark:text-white">{{ $school_name ?? 'SMK Muhammadiyah Kandanghaur' }}</strong>. 
                        Silahkan masuk menggunakan akun yang telah terdaftar untuk mengikuti ujian dengan nyaman.
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('login') }}" class="bg-school-primary text-white px-8 py-4 rounded-2xl font-bold hover:bg-sky-600 transition-all shadow-xl shadow-sky-600/30 active:scale-95 flex items-center gap-2">
                            <span>Mulai Ujian</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <button class="flex items-center gap-2 px-8 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 font-semibold hover:bg-white dark:hover:bg-slate-800 transition-all text-slate-600 dark:text-slate-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Panduan
                        </button>
                    </div>
                </div>

                <!-- Right Column - Card Info -->
                <div class="relative animate-float">
                    <div class="relative bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-3 rounded-[2.5rem] shadow-2xl">
                        <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-blue-600 rounded-[2rem] p-8 text-white">
                            
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <div class="flex items-center gap-2 mb-3 flex-wrap">
                                        <span class="px-3 py-1.5 bg-white/20 rounded-full text-xs font-semibold backdrop-blur-sm border border-white/10">
                                            📋 Periode Aktif
                                        </span>
                                        <span class="px-3 py-1.5 bg-emerald-500/30 rounded-full text-xs font-semibold backdrop-blur-sm border border-emerald-400/30 animate-pulse-slow">
                                            ● {{ $periodeAktif ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </div>
                                    <h3 class="text-2xl font-bold leading-tight">
                                        {{ $periodeAktif ? $periodeAktif->nama_periode : 'Belum Ada Periode' }}
                                    </h3>
                                    @if($periodeAktif)
                                        <p class="text-sm text-white/80 mt-2 flex items-center gap-2">
                                            <span>📅</span> 
                                            {{ \Carbon\Carbon::parse($periodeAktif->tanggal_mulai)->translatedFormat('l, d F Y') }} 
                                            <span class="mx-2">→</span> 
                                            {{ \Carbon\Carbon::parse($periodeAktif->tanggal_selesai)->translatedFormat('l, d F Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Stats Grid - Hanya 3 Kolom (Tanpa Jadwal Count) -->
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl border border-white/10 text-center card-hover">
                                    <div class="text-2xl">👨‍🎓</div>
                                    <div class="text-xl font-bold">{{ $siswaCount }}</div>
                                    <div class="text-[10px] uppercase tracking-wider text-white/60">Siswa</div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl border border-white/10 text-center card-hover">
                                    <div class="text-2xl">📚</div>
                                    <div class="text-xl font-bold">{{ $mapelCount }}</div>
                                    <div class="text-[10px] uppercase tracking-wider text-white/60">Mapel</div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl border border-white/10 text-center card-hover">
                                    <div class="text-2xl">🏫</div>
                                    <div class="text-xl font-bold">{{ $kelasCount }}</div>
                                    <div class="text-[10px] uppercase tracking-wider text-white/60">Kelas</div>
                                </div>
                            </div>

                            <!-- Info Waktu & Server -->
                            <div class="mt-6 grid grid-cols-2 gap-3">
                                <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl border border-white/10">
                                    <span class="text-[10px] uppercase opacity-60 block mb-1">📆 Tanggal</span>
                                    <span class="font-mono text-sm font-semibold">{{ date('d M Y') }}</span>
                                </div>
                                <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl border border-white/10">
                                    <span class="text-[10px] uppercase opacity-60 block mb-1">🕐 Waktu</span>
                                    <span class="font-mono text-sm font-semibold" id="clock">{{ date('H:i:s') }}</span>
                                </div>
                            </div>

                            <!-- Footer - Tanpa Info Sesi -->
                            <div class="mt-6 pt-6 border-t border-white/10 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/10">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold">Server Status</p>
                                        <p class="text-[10px] text-green-300">● Online & Siap</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 bg-green-400 rounded-full animate-pulse"></span>
                                    <span class="text-[10px] text-white/70">Siap Digunakan</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- Stats Section - Tanpa Jadwal Count -->
            <section class="mt-32 grid grid-cols-2 md:grid-cols-3 gap-6 py-12 px-8 rounded-3xl bg-white/50 backdrop-blur-sm border border-slate-100/50 dark:bg-slate-800/30 dark:border-slate-700/50">
                <div class="text-center group">
                    <div class="text-4xl font-bold text-sky-600 dark:text-sky-400 mb-2 group-hover:scale-110 transition-transform">
                        {{ $siswaCount ?? '0' }}
                    </div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Siswa Terdaftar</p>
                </div>
                <div class="text-center group">
                    <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2 group-hover:scale-110 transition-transform">
                        {{ $mapelCount ?? '0' }}
                    </div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Mata Pelajaran</p>
                </div>
                <div class="text-center group">
                    <div class="text-4xl font-bold text-cyan-600 dark:text-cyan-400 mb-2 group-hover:scale-110 transition-transform">
                        {{ $kelasCount ?? '0' }}
                    </div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Kelas</p>
                </div>
            </section>

        </div>
    </main>

    <!-- Footer -->
    <footer class="py-10 text-center border-t border-slate-100 dark:border-slate-800 bg-white/30 backdrop-blur-sm dark:bg-slate-900/30">
        <div class="max-w-7xl mx-auto px-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">
                &copy; {{ date('Y') }} {{ $school_name ?? 'SMK Muhammadiyah Kandanghaur' }} &middot; Computer Based Test System v2.0
            </p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Real-time Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toTimeString().slice(0, 8);
        }
        setInterval(updateClock, 1000);

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>