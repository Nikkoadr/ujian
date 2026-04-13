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
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.min.js"></script>
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
                <div class="leading-none hidden sm:block">
                    <h1 class="text-sm sm:text-lg font-extrabold tracking-tight">CBT - {{ $mapel->nama_mapel }}</h1>
                    <p class="text-[7px] sm:text-[9px] font-bold opacity-70 uppercase tracking-widest">Digital Assessment</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl border transition-all duration-300"
                     :class="{
                        'bg-red-500/20 border-red-400/30 text-red-200': !isOnline,
                        'bg-amber-500/20 border-amber-400/30 text-amber-200': isOnline && isSaving,
                        'bg-emerald-500/20 border-emerald-400/30 text-emerald-200': isOnline && !isSaving
                     }">
                    
                    <span class="relative flex h-2 w-2">
                        <span x-show="isSaving || !isOnline" class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                              :class="!isOnline ? 'bg-red-400' : 'bg-amber-400'"></span>
                        
                        <span class="relative inline-flex rounded-full h-2 w-2"
                              :class="{
                                'bg-red-500': !isOnline,
                                'bg-amber-500': isOnline && isSaving,
                                'bg-emerald-500': isOnline && !isSaving
                              }"></span>
                    </span>

                    <span class="text-[9px] font-black uppercase tracking-widest">
                        <span x-show="!isOnline">Terputus</span>
                        <span x-show="isOnline && isSaving">Menyimpan</span>
                        <span x-show="isOnline && !isSaving">Terhubung</span>
                    </span>
                </div>

                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-red-500/20 border border-red-400/30">
                    <span class="text-sm font-bold text-white" x-text="pelanggaran + '/' + maxPelanggaran"></span>
                </div>

                <div class="flex items-center gap-2 bg-black/20 px-3 py-1.5 rounded-xl border border-white/10">
                    <span x-text="formatTime(timeLeft)" 
                          :class="timeLeft < 300 ? 'text-red-400 animate-pulse' : 'text-white'"
                          class="font-mono font-bold text-sm sm:text-lg tracking-tighter"></span>
                </div>
            </div>

            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="flex items-center gap-2 bg-white/10 p-1 pr-3 rounded-full border border-white/20">
                    <div class="w-7 h-7 rounded-full bg-sky-500 flex items-center justify-center font-bold text-xs uppercase shadow-inner">
                        {{ substr(Auth::user()->nama, 0, 1) }}
                    </div>
                    <span class="text-xs font-bold hidden md:block">{{ Auth::user()->nama }}</span>
                </button>

                <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-[110]">
                    <button @click="logoutConfirm()" class="w-full text-left px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 flex items-center gap-2">
                        Keluar Ujian
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-[1440px] w-full mx-auto p-0 sm:p-4 md:p-6 overflow-hidden flex flex-col md:flex-row gap-4">
        <div class="flex-1 bg-white md:rounded-[2rem] shadow-sm border border-slate-200 flex flex-col overflow-hidden relative z-10">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                <div class="flex items-center gap-3">
                    <span class="bg-sky-600 text-white px-3 py-1 rounded-lg font-black text-xs sm:text-sm" x-text="'SOAL ' + currentSoal.nomor"></span>
                    <div class="flex gap-1">
                        <button @click="setFont('small')" :class="fontSize == 'small' ? 'bg-sky-600 text-white' : 'bg-slate-100'" class="w-7 h-7 rounded-lg font-bold text-[10px]">A</button>
                        <button @click="setFont('medium')" :class="fontSize == 'medium' ? 'bg-sky-600 text-white' : 'bg-slate-100'" class="w-7 h-7 rounded-lg font-bold text-xs">A</button>
                        <button @click="setFont('large')" :class="fontSize == 'large' ? 'bg-sky-600 text-white' : 'bg-slate-100'" class="w-7 h-7 rounded-lg font-bold text-sm">A</button>
                    </div>
                </div>
                <button @click="showMobileNav = true" class="md:hidden px-4 py-2 bg-sky-50 text-sky-700 rounded-xl text-[10px] font-black border border-sky-100 uppercase">Peta Soal</button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 sm:p-10 custom-scroll" :class="'font-size-' + fontSize">
                <div class="max-w-3xl mx-auto">
                    <template x-if="currentSoal.gambar_soal">
                        <div class="mb-6 bg-slate-50 p-2 rounded-2xl border text-center">
                            <img :src="currentSoal.gambar_soal" class="max-w-full h-auto rounded-xl inline-block shadow-md">
                        </div>
                    </template>
                    <div class="text-slate-800 font-semibold leading-relaxed mb-8" x-html="currentSoal.pertanyaan"></div>
                    <div class="space-y-3">
                        <template x-for="opt in currentSoal.pilihan" :key="opt.db_id">
                            <label class="flex items-start gap-3 p-4 rounded-2xl border-2 transition-all cursor-pointer bg-white"
                                   :class="currentSoal.jawaban_terpilih == opt.db_id ? 'bg-sky-50 border-sky-500 shadow-sm' : 'border-slate-100 hover:border-sky-200 shadow-sm'">
                                <input type="radio" name="jawaban" class="hidden" @change="handleSelect(opt.db_id)" :checked="currentSoal.jawaban_terpilih == opt.db_id">
                                <div class="w-8 h-8 sm:w-9 sm:h-9 flex-shrink-0 flex items-center justify-center rounded-xl border-2 font-black text-xs sm:text-sm"
                                     :class="currentSoal.jawaban_terpilih == opt.db_id ? 'bg-sky-600 border-sky-600 text-white' : 'bg-slate-50 border-slate-200 text-slate-400'">
                                    <span x-text="opt.label"></span>
                                </div>
                                <div class="pt-1.5 flex-1">
                                    <div class="font-bold text-slate-700 text-xs sm:text-sm" x-html="opt.teks"></div>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            <div class="px-4 py-4 border-t border-slate-100 bg-white flex items-center justify-between gap-2">
                <button @click="prev()" :disabled="currentIndex === 0" class="flex-1 sm:flex-none px-5 py-3.5 bg-slate-100 text-slate-600 rounded-2xl font-bold text-xs disabled:opacity-20 uppercase">Kembali</button>
                <button @click="toggleRagu()" class="flex-1 sm:flex-none px-8 py-3.5 rounded-2xl font-bold text-xs uppercase shadow-sm"
                        :class="currentSoal.is_ragu ? 'bg-amber-500 text-white' : 'bg-amber-100 text-amber-700 border border-amber-200'">Ragu</button>
                
                <template x-if="currentIndex === listSoal.length - 1">
                    <button @click="confirmSelesai()" 
                            :disabled="timeLeft > 300"
                            :class="timeLeft > 300 ? 'bg-slate-300 cursor-not-allowed opacity-50' : 'bg-red-500 text-white shadow-lg'"
                            class="flex-1 sm:flex-none px-5 py-3.5 rounded-2xl font-bold text-xs uppercase transition-all">
                        <span x-text="timeLeft > 300 ? 'Selesai (Kunci)' : 'Selesai'"></span>
                    </button>
                </template>
                <template x-if="currentIndex !== listSoal.length - 1">
                    <button @click="next()" class="flex-1 sm:flex-none px-5 py-3.5 bg-sky-600 text-white rounded-2xl font-bold text-xs shadow-lg uppercase">Lanjut</button>
                </template>
            </div>
        </div>

        <aside :class="showMobileNav ? 'translate-y-0' : 'translate-y-full md:translate-y-0'" class="fixed inset-0 z-[120] md:relative md:z-10 md:w-80 flex flex-col transition-transform duration-300 pointer-events-none md:pointer-events-auto">
            <div @click="showMobileNav = false" class="md:hidden absolute inset-0 bg-slate-900/60 backdrop-blur-sm pointer-events-auto"></div>
            <div class="mt-auto md:mt-0 bg-white md:rounded-[2rem] border border-slate-200 h-[75vh] md:h-full flex flex-col pointer-events-auto overflow-hidden rounded-t-[2.5rem] relative">
                <div class="p-6 border-b flex justify-between items-center bg-slate-50/50">
                    <h2 class="font-extrabold text-slate-800 text-[10px] tracking-[0.2em] uppercase">Peta Navigasi - {{ $mapel->nama_mapel }}</h2>
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
                    <button @click="confirmSelesai()" 
                            :disabled="timeLeft > 300"
                            :class="timeLeft > 300 ? 'bg-slate-300 cursor-not-allowed opacity-50' : 'bg-red-500 hover:bg-red-600 shadow-xl'"
                            class="w-full py-4 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all">
                        <span x-show="timeLeft > 300">Selesai (Kunci)</span>
                        <span x-show="timeLeft <= 300">Selesai Ujian</span>
                    </button>
                </div>
            </div>
        </aside>
    </main>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

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
                isSaving: false,
                isOnline: navigator.onLine,

                get currentSoal() { return this.listSoal[this.currentIndex]; },

                init() {
                    this.startTimer();
                    this.refreshMath();
                    this.setupProtection();
                    window.addEventListener('online',  () => this.isOnline = true);
                    window.addEventListener('offline', () => this.isOnline = false);
                    if (this.pelanggaran >= this.maxPelanggaran) { this.blokirUser(); }
                },

                setupProtection() {
                    window.onblur = () => {
                        if (!this.isBlocked) { this.handleViolation(); }
                    };
                    document.addEventListener('visibilitychange', () => {
                        if (document.visibilityState === 'hidden' && !this.isBlocked) {
                            this.handleViolation();
                        }
                    });
                },

                handleViolation() {
                    this.pelanggaran++;
                    localStorage.setItem('cheat_count', this.pelanggaran);
                    if (this.pelanggaran >= this.maxPelanggaran) {
                        this.blokirUser();
                    } else {
                        Swal.fire({ title: 'Peringatan!', text: `Anda terdeteksi keluar dari halaman ujian (${this.pelanggaran}/${this.maxPelanggaran}). Jika mencapai limit, akun Anda akan diblokir!`, icon: 'warning' });
                    }
                },

                async blokirUser() {
                    this.isBlocked = true;
                    localStorage.removeItem('cheat_count');
                    Swal.fire({ title: 'AKUN DIBLOKIR!',
                                text: 'Pelanggaran batas maksimal. Mengeluarkan sesi...', 
                                icon: 'error', showConfirmButton: false });
                    try {
                        await fetch("{{ route('ujian.blokir') }}", {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                    } finally {
                        setTimeout(() => { document.getElementById('logout-form').submit(); }, 3000);
                    }
                },

                encrypt(data) { return btoa(JSON.stringify(data)); },

                async saveToDb() {
                    if (!this.isOnline || this.isBlocked) return;
                    this.isSaving = true; // AKAN MENGUBAH INDIKATOR JADI KUNING
                    try {
                        const payloadData = {
                            mapel_id: {{ $mapel->id }},
                            soal_id: this.currentSoal.id,
                            jawaban_id: this.currentSoal.jawaban_terpilih,
                            is_ragu: this.currentSoal.is_ragu
                        };
                        await fetch("{{ route('ujian.simpan') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ payload: this.encrypt(payloadData) })
                        });
                    } finally {
                        // Delay 400ms agar mata sempat melihat transisi kuning -> hijau
                        setTimeout(() => { this.isSaving = false; }, 400); 
                    }
                },

                async handleSelect(db_id) { 
                    this.currentSoal.jawaban_terpilih = db_id; 
                    await this.saveToDb();
                },

                async toggleRagu() { 
                    this.currentSoal.is_ragu = !this.currentSoal.is_ragu;
                    await this.saveToDb();
                },

                startTimer() { 
                    const timer = setInterval(() => { 
                        if(this.timeLeft > 0) this.timeLeft--; 
                        else { clearInterval(timer); this.submitUjian(); }
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

                getNavClass(soal, index) {
                    if (this.currentIndex === index) return 'bg-sky-600 border-sky-600 text-white shadow-lg scale-110 z-10';
                    if (soal.is_ragu) return 'bg-amber-400 border-amber-400 text-white';
                    if (soal.jawaban_terpilih) return 'bg-sky-100 border-sky-100 text-sky-700';
                    return 'bg-white border-slate-100 text-slate-300';
                },

                confirmSelesai() {
                    Swal.fire({
                        title: 'Selesai Ujian?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Selesai',
                        confirmButtonColor: '#0ea5e9'
                    }).then((result) => { if (result.isConfirmed) this.submitUjian(); });
                },

                submitUjian() {
                    localStorage.removeItem('cheat_count');
                    window.location.href = "{{ route('ujian.selesai', ['id' => $mapel->id]) }}";
                },

                logoutConfirm() {
                    Swal.fire({
                        title: 'Keluar?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            localStorage.removeItem('cheat_count');
                            document.getElementById('logout-form').submit();
                        }
                    });
                },

                refreshMath() { this.$nextTick(() => { if(window.MathJax) MathJax.typeset(); }); }
            }
        }
    </script>
</body>
</html>