<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CBT SMK Muhammadiyah Kandanghaur</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; user-select: none; }
        [x-cloak] { display: none !important; }
        .font-size-small { font-size: 0.9rem; }
        .font-size-medium { font-size: 1.05rem; }
        .font-size-large { font-size: 1.3rem; }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="h-full overflow-hidden flex flex-col text-slate-700" 
      x-data="examHandler()" 
      x-init="init()"
      @contextmenu.prevent
      @keydown.f12.prevent
      @keydown.ctrl.u.prevent
      @keydown.ctrl.c.prevent>

    <header class="bg-sky-800 text-white shadow-md z-[100] relative">
        <div class="max-w-[1440px] mx-auto px-4 h-14 sm:h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/img/logo.png') }}" class="w-7 h-7 sm:w-9 sm:h-9 object-contain">
                <div class="leading-none">
                    <h1 class="text-sm sm:text-lg font-extrabold tracking-tight">CBT</h1>
                    <p class="text-[7px] sm:text-[9px] font-bold opacity-70 uppercase tracking-widest">Digital Assessment</p>
                </div>
            </div>

            <div class="flex items-center gap-2 bg-black/20 px-3 py-1.5 rounded-xl border border-white/10">
                <span x-text="formatTime(timeLeft)" class="font-mono font-bold text-sm sm:text-lg tracking-tighter"></span>
            </div>

            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="flex items-center gap-2 bg-white/10 p-1 pr-3 rounded-full border border-white/20 hover:bg-white/20 transition-all">
                    <div class="w-7 h-7 rounded-full bg-sky-500 flex items-center justify-center font-bold text-xs uppercase shadow-inner">
                        {{ substr(Auth::user()->nama, 0, 1) }}
                    </div>
                    <span class="text-xs font-bold hidden sm:block">{{ Auth::user()->nama }}</span>
                    <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" x-cloak 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-[110]">
                    <div class="px-4 py-2 border-b border-slate-50 mb-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Siswa</p>
                        <p class="text-xs font-black text-slate-700 truncate">{{ Auth::user()->nama }}</p>
                    </div>
                    <button @click="logoutConfirm()" class="w-full text-left px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar Ujian
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-[1440px] w-full mx-auto p-0 sm:p-4 md:p-6 overflow-hidden flex flex-col md:flex-row gap-4 lg:gap-6">
        <div class="flex-1 bg-white md:rounded-[2rem] shadow-sm border-b md:border border-slate-200 flex flex-col overflow-hidden relative z-10">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                <div class="flex items-center gap-3">
                    <span class="bg-sky-600 text-white px-3 py-1 rounded-lg font-black text-xs sm:text-sm" x-text="'SOAL ' + currentSoal.nomor"></span>
                    <div class="flex gap-1">
                        <button @click="setFont('small')" :class="fontSize == 'small' ? 'bg-sky-600 text-white' : 'bg-slate-100'" class="w-7 h-7 rounded-lg font-bold text-[10px]">A</button>
                        <button @click="setFont('medium')" :class="fontSize == 'medium' ? 'bg-sky-600 text-white' : 'bg-slate-100'" class="w-7 h-7 rounded-lg font-bold text-xs">A</button>
                        <button @click="setFont('large')" :class="fontSize == 'large' ? 'bg-sky-600 text-white' : 'bg-slate-100'" class="w-7 h-7 rounded-lg font-bold text-sm">A</button>
                    </div>
                </div>
                <button @click="showMobileNav = true" class="md:hidden px-4 py-2 bg-sky-50 text-sky-700 rounded-xl text-[10px] font-black border border-sky-100 uppercase">
                    Daftar Soal
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 sm:p-10 custom-scroll" :class="'font-size-' + fontSize">
                <div class="max-w-3xl mx-auto">
                    <template x-if="currentSoal.gambar_soal">
                        <div class="mb-6 bg-slate-50 p-2 rounded-2xl border border-slate-100 shadow-inner text-center">
                            <img :src="currentSoal.gambar_soal" class="max-w-full h-auto rounded-xl inline-block shadow-md">
                        </div>
                    </template>

                    <div class="text-slate-800 font-semibold leading-relaxed mb-8" x-html="currentSoal.pertanyaan"></div>

                    <div class="space-y-3">
                        <template x-for="opt in currentSoal.pilihan" :key="opt.db_id">
                            <label class="flex items-start gap-3 p-4 rounded-2xl border-2 transition-all cursor-pointer bg-white"
                                   :class="currentSoal.jawaban_terpilih == opt.db_id ? 'bg-sky-50 border-sky-500 shadow-sm' : 'border-slate-100 hover:border-sky-200 shadow-sm'">
                                <input type="radio" class="hidden" @change="handleSelect(opt.db_id)" :checked="currentSoal.jawaban_terpilih == opt.db_id">
                                <div class="w-8 h-8 sm:w-9 sm:h-9 flex-shrink-0 flex items-center justify-center rounded-xl border-2 font-black text-xs sm:text-sm"
                                     :class="currentSoal.jawaban_terpilih == opt.db_id ? 'bg-sky-600 border-sky-600 text-white' : 'bg-slate-50 border-slate-200 text-slate-400'">
                                    <span x-text="opt.label"></span>
                                </div>
                                <div class="pt-1.5 flex-1">
                                    <span class="font-bold text-slate-700 text-xs sm:text-sm" x-html="opt.teks"></span>
                                    <template x-if="opt.gambar">
                                        <div class="mt-2">
                                            <img :src="opt.gambar" class="max-w-full sm:w-48 h-auto rounded-lg border border-slate-100 shadow-sm">
                                        </div>
                                    </template>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            <div class="px-4 py-4 sm:px-6 sm:py-6 border-t border-slate-100 bg-white flex items-center justify-between gap-2">
                <button @click="prev()" :disabled="currentIndex === 0" class="flex-1 sm:flex-none px-5 py-3.5 bg-slate-100 text-slate-600 rounded-2xl font-bold text-xs disabled:opacity-20 uppercase">Kembali</button>
                <button @click="toggleRagu()" class="flex-1 sm:flex-none px-8 py-3.5 rounded-2xl font-bold text-xs uppercase shadow-sm"
                        :class="currentSoal.is_ragu ? 'bg-amber-500 text-white' : 'bg-amber-100 text-amber-700 border border-amber-200'">
                    Ragu
                </button>
                <button @click="next()" :disabled="currentIndex === listSoal.length - 1" class="flex-1 sm:flex-none px-5 py-3.5 bg-sky-600 text-white rounded-2xl font-bold text-xs shadow-lg uppercase">Lanjut</button>
            </div>
        </div>

        <aside :class="showMobileNav ? 'translate-y-0' : 'translate-y-full md:translate-y-0'" 
               class="fixed inset-0 z-[120] md:relative md:z-10 md:w-80 flex flex-col transition-transform duration-300 pointer-events-none md:pointer-events-auto">
            <div @click="showMobileNav = false" class="md:hidden absolute inset-0 bg-slate-900/60 backdrop-blur-sm pointer-events-auto"></div>
            <div class="mt-auto md:mt-0 bg-white md:rounded-[2rem] border border-slate-200 h-[75vh] md:h-full flex flex-col pointer-events-auto overflow-hidden rounded-t-[2.5rem] relative">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <h2 class="font-extrabold text-slate-800 text-[10px] tracking-[0.2em] uppercase">Peta Navigasi</h2>
                </div>
                
                <div class="flex-1 overflow-y-auto p-5 grid grid-cols-5 gap-3 custom-scroll">
                    <template x-for="(soal, index) in listSoal" :key="soal.id">
                        <button @click="currentIndex = index; showMobileNav = false; refreshMath()" 
                                class="aspect-square rounded-xl border-2 flex items-center justify-center text-[11px] font-black transition-all"
                                :class="getNavClass(soal, index)">
                            <span x-text="index + 1"></span>
                        </button>
                    </template>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100">
                    <button @click="confirmSelesai()" class="w-full py-4 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-red-100">
                        Selesai Ujian
                    </button>
                </div>
            </div>
        </aside>
    </main>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        function examHandler() {
            return {
                showMobileNav: false,
                fontSize: 'medium',
                timeLeft: Math.floor({{ $timeLeft }}),
                currentIndex: 0,
                listSoal: @json($soal),
                pelanggaran: parseInt(localStorage.getItem('cheat_count')) || 0,
                maxPelanggaran: 3,
                isBlocked: false,
                isProcessingViolation: false, // Flag debounce

                get currentSoal() { return this.listSoal[this.currentIndex]; },

                init() {
                    this.startTimer();
                    this.refreshMath();
                    this.setupProtection();
                },

                setupProtection() {
                    // 1. Deteksi Kehilangan Fokus (Cara paling ketat)
                    window.onblur = () => {
                        if (!this.isBlocked && !this.isProcessingViolation) {
                            this.isProcessingViolation = true;
                            this.handleViolation();
                            
                            // Debounce 2 detik agar tidak terpicu berkali-kali saat transisi
                            setTimeout(() => { this.isProcessingViolation = false; }, 2000);
                        }
                    };

                    // 2. Deteksi Keluar Jendela Jaringan (Opsional: jika mouse keluar area halaman)
                    document.addEventListener("mouseleave", (e) => {
                        // Bisa diaktifkan jika ingin lebih ketat lagi
                        // this.handleViolation();
                    });
                },

                async handleViolation() {
                    this.pelanggaran++;
                    localStorage.setItem('cheat_count', this.pelanggaran);

                    if (this.pelanggaran >= this.maxPelanggaran) {
                        this.isBlocked = true;
                        await this.blokirUser();
                    } else {
                        Swal.fire({
                            title: 'Peringatan!',
                            text: `Anda terdeteksi keluar dari halaman ujian (${this.pelanggaran}/${this.maxPelanggaran}). Jika mencapai limit, akun Anda akan diblokir!`,
                            icon: 'warning',
                            confirmButtonColor: '#0ea5e9',
                            allowOutsideClick: false
                        });
                    }
                },

                async blokirUser() {
                    localStorage.removeItem('cheat_count');
                    try {
                        // 1. Hit API Blokir (Gunakan await agar DB terupdate dulu)
                        await fetch("{{ route('ujian.blokir') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        Swal.fire({
                            title: 'AKUN DIBLOKIR!',
                            text: 'Pelanggaran batas maksimal. Mengeluarkan sesi...',
                            icon: 'error',
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });

                        // 2. Submit Logout Form (POST)
                        setTimeout(() => { document.getElementById('logout-form').submit(); }, 2500);
                    } catch (e) {
                        document.getElementById('logout-form').submit();
                    }
                },

                logoutConfirm() {
                    Swal.fire({
                        title: 'Keluar Ujian?',
                        text: "Ujian Anda masih berlangsung. Yakin ingin keluar?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Keluar',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#ef4444'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            localStorage.removeItem('cheat_count');
                            document.getElementById('logout-form').submit();
                        }
                    });
                },

                startTimer() { 
                    const timer = setInterval(() => { 
                        if(this.timeLeft > 0) this.timeLeft--; 
                        else {
                            clearInterval(timer);
                            this.forceSubmit();
                        }
                    }, 1000); 
                },

                formatTime(s) { 
                    let h = Math.floor(s/3600).toString().padStart(2,'0');
                    let m = Math.floor((s%3600)/60).toString().padStart(2,'0');
                    let sec = (s%60).toString().padStart(2,'0');
                    return `${h}:${m}:${sec}`; 
                },

                next() { if(this.currentIndex < this.listSoal.length - 1) { this.currentIndex++; this.refreshMath(); } },
                prev() { if(this.currentIndex > 0) { this.currentIndex--; this.refreshMath(); } },
                setFont(size) { this.fontSize = size; },

                async handleSelect(db_id) { 
                    this.currentSoal.jawaban_terpilih = db_id; 
                    await this.saveToDb();
                },

                async toggleRagu() { 
                    this.currentSoal.is_ragu = !this.currentSoal.is_ragu;
                    await this.saveToDb();
                },

                async saveToDb() {
                    try {
                        await fetch("{{ route('ujian.simpan') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                mapel_id: {{ $mapel->id }},
                                soal_id: this.currentSoal.id,
                                jawaban_id: this.currentSoal.jawaban_terpilih,
                                is_ragu: this.currentSoal.is_ragu
                            })
                        });
                    } catch (e) { console.error("Sinkronisasi gagal"); }
                },

                getNavClass(soal, index) {
                    if (this.currentIndex === index) return 'bg-sky-600 border-sky-600 text-white shadow-xl scale-110 z-10';
                    if (soal.is_ragu) return 'bg-amber-400 border-amber-400 text-white';
                    if (soal.jawaban_terpilih) return 'bg-sky-100 border-sky-100 text-sky-700';
                    return 'bg-white border-slate-100 text-slate-300';
                },

                confirmSelesai() {
                    Swal.fire({
                        title: 'Selesai Ujian?',
                        text: "Pastikan semua soal telah terjawab.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Selesai',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#0ea5e9'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submitUjian();
                        }
                    });
                },

                forceSubmit() {
                    Swal.fire({
                        title: 'Waktu Habis!',
                        text: 'Jawaban Anda tersimpan otomatis.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    }).then(() => { 
                        this.submitUjian();
                    });
                },

                // Gabungkan logika redirect ke satu fungsi agar konsisten
                submitUjian() {
                    localStorage.removeItem('cheat_count');
                    // Tambahkan indikator loading jika perlu
                    window.location.href = "{{ route('ujian.selesai', ['id' => $mapel->id]) }}";
                },

                refreshMath() { 
                    this.$nextTick(() => { 
                        if(window.MathJax) MathJax.typeset(); 
                    }); 
                }
            }
        }
    </script>
</body>
</html>