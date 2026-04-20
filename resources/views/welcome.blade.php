<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Dinamik: Mengambil nama sekolah dari DB/Config --}}
    <title>CBT Portal | {{ $school_name ?? config('app.name', 'SMK Muhammadiyah Kandanghaur') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            600: '#0f172a', // Diubah ke Navy agar lebih formal khas sekolah/instansi
                            700: '#1e293b',
                            800: '#334155',
                            950: '#020617',
                        },
                        school: {
                            primary: '#2563eb', // Warna identitas sekolah (bisa diganti)
                            secondary: '#fbbf24',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-[#f8fafc] dark:bg-brand-950 text-slate-900 dark:text-slate-100 antialiased font-sans">

    <nav class="fixed top-0 w-full z-50 border-b border-slate-100 bg-white/80 backdrop-blur-xl dark:border-slate-800/60 dark:bg-brand-950/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-4 group">
                    {{-- LOGO SEKOLAH: Diambil dari assets/img/logo.png --}}
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo {{ $school_name ?? 'Sekolah' }}" class="h-12 w-auto object-contain">
                    
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
                            <a href="{{ route('home') }}" class="bg-school-primary text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                                Ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-bold text-slate-600 hover:text-school-primary transition px-4">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-brand-600 text-white px-7 py-2.5 rounded-xl text-sm font-bold hover:bg-brand-950 transition-all active:scale-95">
                                    Daftar Akun
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="relative grid lg:grid-cols-2 gap-12 items-center">
                <div class="absolute -top-40 -right-20 w-[500px] h-[500px] bg-school-primary/5 blur-[120px] rounded-full"></div>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 border border-slate-200 text-slate-600 text-[11px] font-bold uppercase tracking-widest mb-8 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        Sistem Informasi Ujian Terpadu
                    </div>
                    
                    <h1 class="text-5xl lg:text-6xl font-extrabold leading-[1.1] mb-6 text-slate-900 dark:text-white">
                        Wujudkan Ujian <br> 
                        <span class="text-school-primary italic">Jujur & Mandiri.</span>
                    </h1>
                    
                    <p class="text-lg text-slate-500 dark:text-slate-400 mb-10 leading-relaxed max-w-xl">
                        Selamat datang di portal Computer Based Test (CBT) <strong>{{ $school_name ?? 'SMK Muhammadiyah Kandanghaur' }}</strong>. 
                        Silahkan masuk menggunakan akun yang telah terdaftar untuk mengikuti ujian.
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('login') }}" class="bg-school-primary text-white px-8 py-4 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-xl shadow-blue-600/20 active:scale-95">
                            Mulai Ujian Sekarang
                        </a>
                        <button class="flex items-center gap-2 px-8 py-4 rounded-2xl border border-slate-200 dark:border-slate-800 font-bold hover:bg-white dark:hover:bg-slate-800 transition-all text-slate-600 dark:text-slate-300">
                            Unduh Panduan
                        </button>
                    </div>
                </div>

                {{-- Bagian Dinamik: Card Info Tes --}}
                <div class="relative">
                    <div class="relative bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-3 rounded-[2.5rem] shadow-2xl">
                        <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-[2rem] p-8 text-white">
                            <div class="flex justify-between items-start mb-12">
                                <div>
                                    <p class="text-blue-200 text-xs uppercase tracking-widest mb-1">Status Sesi Saat Ini</p>
                                    <h3 class="text-2xl font-bold">{{ $active_exam_name ?? 'Ujian Belum Tersedia' }}</h3>
                                </div>
                                <div class="bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/10 text-xs font-mono">
                                    {{ date('d M Y') }}
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                                    <span class="text-[10px] uppercase opacity-60 block mb-1">Tahun Ajaran</span>
                                    <span class="font-bold text-lg">{{ $academic_year ?? '2025/2026' }}</span>
                                </div>
                                <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                                    <span class="text-[10px] uppercase opacity-60 block mb-1">Server</span>
                                    <span class="font-bold text-lg text-green-400">Online</span>
                                </div>
                            </div>

                            <div class="mt-8 pt-8 border-t border-white/10 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-school-secondary flex items-center justify-center text-brand-950 font-bold">
                                        !
                                    </div>
                                    <p class="text-[11px] leading-tight opacity-80">Pastikan koneksi internet stabil<br>sebelum menekan tombol mulai.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Dinamik (Contoh pengambilan dari DB) --}}
            <section class="mt-32 grid grid-cols-2 md:grid-cols-4 gap-8 py-12 border-y border-slate-100 dark:border-slate-800">
                <div class="text-center">
                    <p class="text-3xl font-bold text-brand-600 dark:text-white mb-1">{{ $siswaCount ?? '0' }}</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Siswa Terdaftar</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-brand-600 dark:text-white mb-1">{{ $mapelCount ?? '0' }}</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Mata Pelajaran</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-brand-600 dark:text-white mb-1">{{ $kelasCount ?? '0' }}</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kelas</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-brand-600 dark:text-white mb-1">Active</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Proctoring System</p>
                </div>
            </section>
        </div>
    </main>

    <footer class="py-10 text-center border-t border-slate-50 dark:border-slate-900">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">
            &copy; 2026 {{ $school_name ?? 'SMK Muhammadiyah Kandanghaur' }} &middot; Computer Based Test System v2.0
        </p>
    </footer>

</body>
</html>