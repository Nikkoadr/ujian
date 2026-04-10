<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart CBT | {{ config('app.name', 'Laravel') }}</title>

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
                            600: '#2563eb', // Primary Blue
                            700: '#1d4ed8',
                            800: '#1e40af',
                            950: '#030712',
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

    <nav class="fixed top-0 w-full z-50 border-b border-blue-100/50 bg-white/80 backdrop-blur-xl dark:border-slate-800/60 dark:bg-brand-950/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3 group cursor-pointer">
                    <div class="h-10 w-10 bg-brand-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-brand-800 dark:text-white uppercase">Edu<span class="text-brand-600">CBT</span></span>
                </div>

                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('home') }}" class="bg-brand-600 text-white px-6 py-2.5 rounded-full text-sm font-bold hover:bg-brand-700 transition-all shadow-md shadow-blue-500/20">
                                Masuk Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-600 transition px-4">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-brand-600 text-white px-7 py-2.5 rounded-full text-sm font-bold hover:bg-brand-700 transition-all hover:shadow-lg active:scale-95">
                                    Registrasi
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
                <div class="absolute -top-40 -right-20 w-[500px] h-[500px] bg-blue-400/10 blur-[120px] rounded-full"></div>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-brand-600 text-[11px] font-bold uppercase tracking-widest mb-8 dark:bg-blue-900/20 dark:border-blue-800">
                        Sistem Ujian Nasional Berbasis Komputer
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-extrabold leading-[1.1] mb-6 text-slate-900 dark:text-white">
                        Ujian Lebih <span class="text-brand-600">Fokus</span> & <span class="text-blue-400">Terintegritas.</span>
                    </h1>
                    <p class="text-lg text-slate-500 dark:text-slate-400 mb-10 leading-relaxed">
                        Kelola ujian daring dengan standar keamanan tinggi. Fitur pemantauan real-time, pengacakan soal cerdas, dan hasil otomatis yang transparan.
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        <a href="#mulai" class="bg-brand-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-brand-700 transition-all shadow-xl shadow-blue-600/20 active:scale-95">
                            Mulai Ujian Sekarang
                        </a>
                        <button class="flex items-center gap-2 px-8 py-4 rounded-2xl border border-slate-200 dark:border-slate-800 font-bold hover:bg-white dark:hover:bg-slate-800 transition-all">
                            Lihat Jadwal
                        </button>
                    </div>
                </div>

                <div class="relative">
                    <div class="relative bg-white dark:bg-slate-900 border border-blue-50 dark:border-slate-800 p-2 rounded-[3rem] shadow-2xl shadow-blue-900/10">
                        <div class="bg-brand-600 rounded-[2.5rem] p-8 text-white">
                            <div class="flex justify-between items-start mb-12">
                                <div>
                                    <p class="text-blue-100 text-xs uppercase tracking-widest mb-1">Informasi Tes</p>
                                    <h3 class="text-2xl font-bold">Seleksi Kompetensi</h3>
                                </div>
                                <div class="bg-white/20 backdrop-blur-md p-3 rounded-2xl border border-white/20 text-xs">
                                    ID: CBT-2026
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-white/10 rounded-2xl border border-white/10">
                                    <span class="text-sm opacity-80">Durasi Ujian</span>
                                    <span class="font-bold tracking-tight text-lg">120 Menit</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-white/10 rounded-2xl border border-white/10">
                                    <span class="text-sm opacity-80">Total Soal</span>
                                    <span class="font-bold tracking-tight text-lg">100 Butir</span>
                                </div>
                            </div>

                            <div class="mt-8 pt-8 border-t border-white/10 flex items-center gap-4">
                                <div class="flex -space-x-2">
                                    <div class="w-8 h-8 rounded-full border-2 border-brand-600 bg-blue-200"></div>
                                    <div class="w-8 h-8 rounded-full border-2 border-brand-600 bg-blue-300"></div>
                                    <div class="w-8 h-8 rounded-full border-2 border-brand-600 bg-blue-400"></div>
                                </div>
                                <p class="text-[11px] font-medium text-blue-100">850 Peserta bergabung sesi ini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="mt-32 grid grid-cols-2 md:grid-cols-4 gap-8 py-12 border-y border-slate-100 dark:border-slate-800">
                <div class="text-center">
                    <p class="text-3xl font-bold text-brand-600 mb-1">99.9%</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Uptime Server</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-brand-600 mb-1">AES</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Data Encryption</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-brand-600 mb-1">10k+</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Concurrent Users</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-brand-600 mb-1">Instant</p>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Result Analysis</p>
                </div>
            </section>
        </div>
    </main>

    <footer class="py-10 text-center">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-[0.2em]">
            &copy; 2026 Portal CBT &middot; Dikembangkan dengan Standar Keamanan Nasional
        </p>
    </footer>

</body>
</html>