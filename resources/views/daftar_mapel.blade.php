<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Dashboard Ujian - SMK Muhammadiyah Kandanghaur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full text-slate-700" x-data="dashboardHandler()">

    <header class="bg-sky-800 text-white shadow-md z-[100] relative">
        <div class="max-w-[1440px] mx-auto px-4 h-14 sm:h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/img/logo.png') }}" class="w-7 h-7 sm:w-9 sm:h-9 object-contain">
                <div class="leading-none">
                    <h1 class="text-sm sm:text-lg font-extrabold tracking-tight">CBT</h1>
                    <p class="text-[7px] sm:text-[9px] font-bold opacity-70 uppercase tracking-widest">Digital Assessment</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] font-bold text-sky-200 uppercase tracking-wider">{{ $user->nama }}</p>
                    <p class="text-[9px] font-bold text-sky-400 uppercase tracking-widest">{{ $kelas->nama_kelas }}</p>
                </div>
                <div class="h-8 w-[1px] bg-white/10 hidden sm:block"></div>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button class="bg-white/10 p-2 rounded-lg hover:bg-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @if(isset($error))
        <div class="mb-8 bg-amber-50 border-2 border-amber-100 p-5 rounded-[2rem] flex items-center gap-4">
             <div class="text-amber-600 font-bold text-sm">{{ $error }}</div>
        </div>
        @endif

        <div class="bg-white rounded-[2.5rem] p-6 mb-8 border border-slate-200 flex flex-col md:flex-row gap-6 justify-between items-center">
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="w-16 h-16 bg-sky-100 text-sky-600 rounded-3xl flex items-center justify-center text-2xl font-black">
                    {{ substr($user->nama, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-800 tracking-tight">{{ $user->nama }}</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        {{ $kelas->tingkat->nama_tingkat }} | {{ $kelas->kompetensi->nama_kompetensi }} | {{ $kelas->nama_kelas }}
                    </p>
                </div>
            </div>

            <div class="w-full md:w-80 relative">
                <input type="text" x-model="searchQuery" placeholder="Cari mata pelajaran..." 
                       class="w-full pl-12 pr-6 py-3.5 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-sky-500 focus:bg-white transition-all text-sm font-bold">
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        {{-- BANNER STATUS BLOKIR/TIDAK AKTIF --}}
        @if(Auth::user()->status === 'diblokir')
        <div class="mb-8 bg-red-50 border-2 border-red-100 p-5 rounded-[2rem] flex items-center gap-4">
            <div class="w-10 h-10 bg-red-500 text-white rounded-full flex-shrink-0 flex items-center justify-center shadow-lg shadow-red-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-red-600 uppercase">Akses Diblokir</h4>
                <p class="text-xs font-bold text-red-500 opacity-80">Akun Anda sedang ditangguhkan. Silakan hubungi operator/pengawas.</p>
            </div>
        </div>
        @elseif(Auth::user()->status === 'tidak_aktif')
        <div class="mb-8 bg-amber-50 border-2 border-amber-100 p-5 rounded-[2rem] flex items-center gap-4">
            <div class="w-10 h-10 bg-amber-500 text-white rounded-full flex-shrink-0 flex items-center justify-center shadow-lg shadow-amber-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-amber-600 uppercase">Akun Tidak Aktif</h4>
                <p class="text-xs font-bold text-amber-500 opacity-80">Akun Anda belum diaktifkan. Silakan hubungi operator/pengawas.</p>
            </div>
        </div>
        @endif

        <h2 class="text-xl font-extrabold text-slate-800 mb-6 flex items-center gap-2">
            <span class="w-2 h-8 bg-sky-600 rounded-full"></span>
            Daftar Ujian Aktif
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <template x-for="ujian in filteredUjian" :key="ujian.id">
                <div class="bg-white rounded-[2.5rem] p-6 shadow-sm border border-slate-200 hover:shadow-xl transition-all relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-sky-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="flex gap-2 items-center">
                                    <span class="px-3 py-1 bg-sky-100 text-sky-700 text-[10px] font-black rounded-lg uppercase tracking-wider" x-text="ujian.kode_mapel"></span>
                                    <span :class="ujian.kompetensi_keahlian_id ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'" 
                                          class="px-2 py-1 text-[9px] font-black rounded-lg uppercase"
                                          x-text="ujian.kompetensi_keahlian_id ? 'KK' : 'Umum'">
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-slate-800 mt-2" x-text="ujian.nama_mapel"></h3>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Durasi</p>
                                <p class="text-sm font-black text-sky-600" x-text="ujian.durasi_menit + ' Menit'"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 py-4 border-y border-slate-50 mb-6 text-xs font-bold">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Mulai</p>
                                <span x-text="ujian.jam_mulai_format"></span> WIB
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Selesai</p>
                                <span x-text="ujian.jam_selesai_format"></span> WIB
                            </div>
                        </div>

                        <div class="space-y-2">
                            @if(Auth::user()->status === 'aktif')
                                {{-- 1. BELUM MULAI --}}
                                <template x-if="!ujian.partisipasi">
                                    <button @click="openModal(ujian)" 
                                            class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-sky-600 transition-all shadow-lg">
                                        Mulai Ujian
                                    </button>
                                </template>

                                {{-- 2. LANJUTKAN (TETAP MINTA TOKEN) --}}
                                <template x-if="ujian.partisipasi && ujian.partisipasi.status === 'sedang mengerjakan'">
                                    <button @click="openModal(ujian)" 
                                            class="w-full py-4 bg-amber-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg flex flex-col items-center leading-tight">
                                        <span>Lanjutkan Ujian</span>
                                        <span class="text-[10px] font-bold opacity-80 mt-1" x-text="'Sisa: ' + calculateRemaining(ujian)"></span>
                                    </button>
                                </template>

                                {{-- 3. SELESAI --}}
                                <template x-if="ujian.partisipasi && ujian.partisipasi.status === 'selesai'">
                                    <button disabled 
                                            class="w-full py-4 bg-emerald-100 text-emerald-600 rounded-2xl font-black text-xs uppercase tracking-widest cursor-not-allowed flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        Ujian Selesai
                                    </button>
                                </template>
                            @else
                                <button disabled class="w-full py-4 bg-slate-100 text-slate-400 rounded-2xl font-black text-xs uppercase tracking-widest cursor-not-allowed">
                                    Ujian Terkunci
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="filteredUjian.length === 0" class="col-span-full py-20 text-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-200">
                <p class="font-bold text-slate-400">Tidak ada ujian yang sesuai pencarian atau jadwal.</p>
            </div>
        </div>
    </main>

    {{-- MODAL TOKEN --}}
    <div x-show="isModalOpen" class="fixed inset-0 z-[110] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="isModalOpen = false"></div>
            <div class="relative bg-white w-full max-w-md rounded-[3rem] shadow-2xl p-10">
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-sky-50 rounded-[2rem] flex items-center justify-center mx-auto mb-4 text-sky-600">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-black text-slate-800" x-text="selectedUjian?.partisipasi ? 'Konfirmasi Token Lanjutan' : 'Konfirmasi Token'"></h2>
                    <p class="text-xs font-bold text-slate-400 mt-2 uppercase" x-text="selectedUjian?.nama_mapel"></p>
                </div>
                <div class="space-y-6">
                    <input type="text" x-model="inputToken" placeholder="INPUT TOKEN" class="w-full px-6 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-sky-500 text-center font-black text-xl tracking-[0.3em] uppercase">
                    <p x-show="errorMessage" x-text="errorMessage" class="text-red-500 text-[10px] font-bold text-center"></p>
                    <div class="flex gap-3">
                        <button @click="isModalOpen = false" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black text-xs uppercase">Batal</button>
                        <button @click="prosesValidasi()" :disabled="isLoading || !inputToken" class="flex-[2] py-4 bg-sky-600 text-white rounded-2xl font-black text-xs uppercase shadow-lg hover:bg-sky-700">
                            <span x-text="isLoading ? 'Memproses...' : (selectedUjian?.partisipasi ? 'Lanjutkan' : 'Masuk Ujian')"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function dashboardHandler() {
            return {
                isModalOpen: false,
                isLoading: false,
                inputToken: '',
                errorMessage: '',
                selectedUjian: null,
                searchQuery: '',
                // Inisialisasi currentTime menggunakan waktu server agar sinkron
                currentTime: new Date('{{ now()->toDateTimeString() }}').getTime(),

                init() {
                    // Update timer tiap detik (berbasis waktu server yang ditambah manual)
                    setInterval(() => {
                        this.currentTime += 1000;
                    }, 1000);
                },
                
                allUjian: @json($daftarUjian).map(u => ({
                    ...u,
                    durasi_menit: Math.floor((new Date('1970-01-01T' + u.durasi) - new Date('1970-01-01T00:00:00')) / 60000),
                    jam_mulai_format: u.jam_mulai.substring(0, 5),
                    jam_selesai_format: u.jam_selesai.substring(0, 5),
                    // Menangani data partisipasi agar terbaca Alpine
                    partisipasi: u.partisipasi && u.partisipasi.length > 0 ? u.partisipasi[0] : (u.partisipasi || null)
                })),

                get filteredUjian() {
                    if (this.searchQuery === '') return this.allUjian;
                    return this.allUjian.filter(u => 
                        u.nama_mapel.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        u.kode_mapel.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                },

                calculateRemaining(ujian) {
                    if (!ujian.partisipasi || !ujian.partisipasi.mulai_ujian) return '00:00';
                    
                    // Gunakan format ISO agar browser tidak bingung dengan zona waktu
                    const startStr = ujian.partisipasi.mulai_ujian.replace(' ', 'T');
                    const start = new Date(startStr).getTime();
                    const durationMs = ujian.durasi_menit * 60 * 1000;
                    const end = start + durationMs;
                    
                    const diff = end - this.currentTime;

                    if (diff <= 0) return "Waktu Habis";

                    const h = Math.floor(diff / 3600000);
                    const m = Math.floor((diff % 3600000) / 60000);
                    const s = Math.floor((diff % 60000) / 1000);

                    return `${h > 0 ? h + 'j ' : ''}${m}m ${s}s`;
                },

                openModal(ujian) {
                    this.selectedUjian = ujian;
                    this.inputToken = '';
                    this.errorMessage = '';
                    this.isModalOpen = true;
                },

                async prosesValidasi() {
                    this.isLoading = true;
                    this.errorMessage = '';
                    try {
                        const response = await fetch("{{ route('ujian.validasi') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                ujian_id: this.selectedUjian.id,
                                token: this.inputToken
                            })
                        });
                        const result = await response.json();
                        if (result.success) {
                            window.location.href = result.redirect;
                        } else {
                            this.errorMessage = result.message;
                        }
                    } catch (error) {
                        this.errorMessage = "Terjadi kesalahan koneksi.";
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>