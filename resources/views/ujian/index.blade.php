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

    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
                processEscapes: true
            },
            chtml: {
                scale: 1.08,
                matchFontHeight: false
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --primary: #0284c7;
        --primary-dark: #0369a1;
        --primary-light: #e0f2fe;

        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;

        --bg: #f1f5f9;
        --card: #ffffff;

        --text: #1e293b;
        --text-soft: #64748b;

        --border: #e2e8f0;
        --border-soft: #f1f5f9;

        --shadow-sm: 0 1px 2px rgba(0,0,0,.04);
        --shadow-md: 0 4px 12px rgba(0,0,0,.06);
        --shadow-lg: 0 10px 30px rgba(0,0,0,.08);
    }

    * {
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--bg);
        color: var(--text);
        user-select: none;
        overflow: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    [x-cloak] {
        display: none !important;
    }

    button,
    input,
    textarea,
    select {
        font-family: inherit;
    }

    button {
        outline: none;
    }

    img {
        max-width: 100%;
        -webkit-user-drag: none;
        user-select: none;
    }

    /* =========================
       FONT SIZE
    ========================= */

    .font-size-small {
        font-size: 1rem;
    }

    .font-size-medium {
        font-size: 1.1rem;
    }

    .font-size-large {
        font-size: 1.3rem;
    }

    /* =========================
       SCROLLBAR
    ========================= */

    .custom-scroll::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    .custom-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .custom-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    /* =========================
       ANIMATION
    ========================= */

    .question-card {
        animation: fadeIn .2s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* =========================
       MATHJAX
    ========================= */

    .MathJax {
        font-size: 1.08em !important;
    }

    mjx-container[jax="CHTML"] {
        max-width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 4px;
    }

    mjx-container[display="true"] {
        display: block !important;
        text-align: left !important;
        margin: 0.8rem 0 !important;
    }

    mjx-container:not([display="true"]) {
        display: inline-block !important;
        vertical-align: middle !important;
        margin: 0 3px !important;
        max-width: none !important;
        overflow: visible !important;
    }

    mjx-container svg {
        display: inline-block;
        vertical-align: middle;
    }

    /* =========================
       EXAM CONTENT
    ========================= */

    .exam-text {
        line-height: 1.9;
        color: var(--text);
        word-break: break-word;
    }

    .exam-text p {
        margin-bottom: 1rem;
    }

    .exam-text strong {
        font-weight: 700;
    }

    .exam-text em {
        font-style: italic;
    }

    .exam-text img {
        display: block;
        margin: 1rem auto;
        border-radius: 0 !important;
        box-shadow: var(--shadow-md);
    }

    /* =========================
       TABLE
    ========================= */

    .exam-text table {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0;
        background: white;
        overflow: hidden;
        border-radius: 1rem;
    }

    .exam-text table,
    .exam-text th,
    .exam-text td {
        border: 1px solid var(--border);
    }

    .exam-text th {
        background: #f8fafc;
        font-weight: 700;
    }

    .exam-text th,
    .exam-text td {
        padding: 10px 12px;
        text-align: left;
    }

    /* =========================
       LIST
    ========================= */

    .exam-text ul,
    .exam-text ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }

    .exam-text ul {
        list-style-type: disc;
    }

    .exam-text ol {
        list-style-type: decimal;
    }

    .exam-text li {
        margin-bottom: .45rem;
    }

    /* =========================
       CODE
    ========================= */

    .exam-text code {
        background: #f8fafc;
        color: #e11d48;
        padding: 2px 6px;
        border-radius: 6px;
        font-size: .92em;
    }

    /* =========================
       BLOCKQUOTE
    ========================= */

    .exam-text blockquote {
        border-left: 4px solid var(--primary);
        background: #f8fafc;
        padding: 1rem;
        margin: 1rem 0;
        border-radius: 0 1rem 1rem 0;
    }

    /* =========================
       QUESTION IMAGE
    ========================= */

.question-image {
    width: 100%;
    max-height: 500px;
    object-fit: contain;
    border-radius: 0 !important;
    display: block;
    margin: auto;
}

    /* =========================
       OPTION CARD
    ========================= */

    .option-card {
        transition: all .2s ease;
        border: 2px solid transparent;
    }

    .option-card:hover {
        transform: translateY(-1px);
    }

    .option-selected {
        background: #e0f2fe !important;
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 4px rgba(2,132,199,.08);
    }

    /* =========================
       NAVIGATION NUMBER
    ========================= */

    .nav-number {
        transition: all .2s ease;
    }

    .nav-number:hover {
        transform: scale(1.05);
    }

    /* =========================
       BUTTON
    ========================= */

    .btn-transition {
        transition: all .2s ease;
    }

    .btn-transition:hover {
        transform: translateY(-1px);
    }

    .btn-transition:active {
        transform: scale(.98);
    }

    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 768px) {

        .font-size-small {
            font-size: .95rem;
        }

        .font-size-medium {
            font-size: 1rem;
        }

        .font-size-large {
            font-size: 1.15rem;
        }

        .exam-text {
            line-height: 1.8;
        }

        .exam-text table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        mjx-container {
            overflow-x: auto !important;
            overflow-y: hidden !important;
        }
    }

    /* =========================
       SWEET ALERT
    ========================= */

    .swal2-popup {
        border-radius: 1.5rem !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    .swal2-confirm,
    .swal2-cancel {
        border-radius: .9rem !important;
        font-weight: 700 !important;
    }
</style>
</head>

<body class="h-full overflow-hidden flex flex-col text-slate-700 bg-slate-100"
      x-data="examHandler()"
      x-init="init()"
      @contextmenu.prevent
      @keydown.f12.prevent
      @keydown.ctrl.u.prevent>

    <header class="bg-gradient-to-r from-sky-700 to-sky-900 text-white shadow-lg z-[100] relative">
        <div class="max-w-[1440px] mx-auto px-4 h-16 sm:h-20 flex items-center justify-between gap-3">

            <div class="flex items-center gap-3 min-w-0">
                <img src="{{ asset('assets/img/logo.png') }}" class="w-8 h-8 sm:w-10 sm:h-10 object-contain flex-shrink-0">

                <div class="leading-tight hidden sm:block min-w-0">
                    <h1 class="text-sm sm:text-lg font-extrabold tracking-tight truncate">
                        CBT - {{ $mapel->nama_mapel }}
                    </h1>
                    <p class="text-[8px] sm:text-[10px] font-bold opacity-70 uppercase tracking-widest">
                        Digital Assessment
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">

                <div class="flex items-center gap-2 px-3 py-2 rounded-xl border transition-all duration-300"
                     :class="{
                        'bg-red-500/20 border-red-400/30 text-red-200': !isOnline,
                        'bg-amber-500/20 border-amber-400/30 text-amber-200': isOnline && isSaving,
                        'bg-emerald-500/20 border-emerald-400/30 text-emerald-200': isOnline && !isSaving
                     }">

                    <span class="relative flex h-2 w-2">
                        <span x-show="isSaving || !isOnline"
                              class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
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

                <div x-show="unsavedCount > 0"
                     x-cloak
                     class="flex items-center gap-2 px-3 py-2 rounded-xl bg-red-500 border border-red-300 animate-pulse"
                     :title="unsavedCount + ' jawaban belum tersimpan ke server — otomatis dikirim ulang. Jangan keluar halaman.'">
                    <span class="text-[9px] font-black uppercase tracking-widest text-white">
                        Belum tersimpan: <span x-text="unsavedCount"></span>
                    </span>
                </div>

                <template x-if="settingAntiNyontek">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-red-500/20 border border-red-400/30">
                        <span class="text-xs sm:text-sm font-bold text-white" x-text="pelanggaran + '/' + maxPelanggaran"></span>
                    </div>
                </template>

                <div class="flex items-center gap-2 bg-black/20 px-3 py-2 rounded-xl border border-white/10">
                    <span x-text="formatTime(timeLeft)"
                          :class="settingTombolSelesai !== false && timeLeft < settingTombolSelesai ? 'text-red-400 animate-pulse' : 'text-white'"
                          class="font-mono font-bold text-sm sm:text-lg tracking-tighter"></span>
                </div>
            </div>

            <div class="relative flex-shrink-0" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open"
                        class="flex items-center gap-2 bg-white/10 p-1 pr-3 rounded-full border border-white/20 hover:bg-white/15 transition-all duration-200">

                    <div class="w-8 h-8 rounded-full bg-sky-500 flex items-center justify-center font-bold text-xs uppercase shadow-inner">
                        {{ substr(Auth::user()->nama, 0, 1) }}
                    </div>

                    <span class="text-xs font-bold hidden md:block max-w-[140px] truncate">
                        {{ Auth::user()->nama }}
                    </span>
                </button>

                <div x-show="open"
                     x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-[110]">

                    <button @click="logoutConfirm()"
                            class="w-full text-left px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 flex items-center gap-2">
                        Keluar Ujian
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-[1440px] w-full mx-auto p-0 sm:p-4 md:p-6 overflow-hidden flex flex-col md:flex-row gap-4">

        <section class="flex-1 bg-white md:rounded-[2rem] shadow-sm border border-slate-200 flex flex-col overflow-hidden relative z-10">

            <div class="px-4 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">

                <div class="flex items-center gap-3">
                    <span class="bg-sky-600 text-white px-4 py-2 rounded-xl font-black text-sm sm:text-base shadow-sm"
                          x-text="'SOAL ' + currentSoal.nomor"></span>

                    <div class="flex gap-1 bg-white p-1 rounded-xl border border-slate-200">
                        <button @click="setFont('small')"
                                :class="fontSize == 'small' ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-50 text-slate-500'"
                                class="w-8 h-8 rounded-lg font-bold text-[10px] transition-all duration-200">
                            A
                        </button>

                        <button @click="setFont('medium')"
                                :class="fontSize == 'medium' ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-50 text-slate-500'"
                                class="w-8 h-8 rounded-lg font-bold text-xs transition-all duration-200">
                            A
                        </button>

                        <button @click="setFont('large')"
                                :class="fontSize == 'large' ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-50 text-slate-500'"
                                class="w-8 h-8 rounded-lg font-bold text-sm transition-all duration-200">
                            A
                        </button>
                    </div>
                </div>

                <button @click="showMobileNav = true"
                        class="md:hidden px-4 py-2 bg-sky-50 text-sky-700 rounded-xl text-[10px] font-black border border-sky-100 uppercase">
                    Peta Soal
                </button>
            </div>

            <div id="exam-content"
                 class="flex-1 overflow-y-auto p-4 sm:p-8 md:p-10 custom-scroll"
                 :class="'font-size-' + fontSize">

                <div class="max-w-5xl mx-auto question-card">

                    <template x-if="currentSoal.gambar_soal">
                        <div class="mb-8 bg-slate-50 p-3 sm:p-4 rounded-3xl border border-slate-200 text-center">
                            <img :src="currentSoal.gambar_soal"
                                loading="lazy"
                                decoding="async"
                                 class="max-w-full max-h-[500px] object-contain mx-auto inline-block shadow-lg">
                        </div>
                    </template>

                    <div class="exam-text text-slate-800 leading-loose mb-10"
                    x-html="currentSoal.pertanyaan"></div>

                    <div class="space-y-4">
                        <template x-for="opt in currentSoal.pilihan" :key="opt.db_id">

                            <label class="flex items-start gap-4 p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer bg-white"
                                   :class="currentSoal.jawaban_terpilih == opt.db_id
                                        ? 'bg-sky-100 border-sky-500 shadow-md ring-2 ring-sky-200'
                                        : 'border-slate-100 hover:border-sky-300 hover:bg-sky-50/40 shadow-sm'">

                                <input type="radio"
                                       name="jawaban"
                                       class="hidden"
                                       @change="handleSelect(opt.db_id)"
                                       :checked="currentSoal.jawaban_terpilih == opt.db_id">

                                <div class="w-10 h-10 sm:w-11 sm:h-11 flex-shrink-0 flex items-center justify-center rounded-xl border-2 font-black text-sm sm:text-base transition-all duration-200"
                                     :class="currentSoal.jawaban_terpilih == opt.db_id
                                        ? 'bg-sky-600 border-sky-600 text-white shadow-sm'
                                        : 'bg-slate-50 border-slate-200 text-slate-400'">

                                    <span x-text="opt.label"></span>
                                </div>

                                <div class="pt-1 flex-1 leading-relaxed">
                                    <div class="exam-text font-semibold text-slate-700"
                                    x-html="opt.teks"></div>
                                </div>
                            </label>

                        </template>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-6 py-4 border-t border-slate-100 bg-white flex items-center justify-between gap-2">

                <button @click="prev()"
                        :disabled="currentIndex === 0"
                        class="flex-1 sm:flex-none px-5 py-3.5 bg-slate-100 text-slate-600 rounded-2xl font-bold text-xs disabled:opacity-20 uppercase transition-all duration-200 hover:bg-slate-200">
                    Kembali
                </button>

                <button @click="toggleRagu()"
                        class="flex-1 sm:flex-none px-8 py-3.5 rounded-2xl font-bold text-xs uppercase shadow-sm transition-all duration-200"
                        :class="currentSoal.is_ragu
                            ? 'bg-amber-500 text-white'
                            : 'bg-amber-100 text-amber-700 border border-amber-200 hover:bg-amber-200'">
                    Ragu
                </button>

                <template x-if="currentIndex === listSoal.length - 1">
                    <button @click="confirmSelesai()"
                            :disabled="settingTombolSelesai !== false && timeLeft > settingTombolSelesai"
                            :class="settingTombolSelesai !== false && timeLeft > settingTombolSelesai
                                ? 'bg-slate-300 cursor-not-allowed opacity-50'
                                : 'bg-red-500 text-white shadow-lg hover:bg-red-600 hover:scale-[1.02]'"
                            class="flex-1 sm:flex-none px-5 py-3.5 rounded-2xl font-bold text-xs uppercase transition-all duration-200">

                        <span x-text="settingTombolSelesai !== false && timeLeft > settingTombolSelesai ? 'Selesai (Kunci)' : 'Selesai'"></span>
                    </button>
                </template>

                <template x-if="currentIndex !== listSoal.length - 1">
                    <button @click="next()"
                            class="flex-1 sm:flex-none px-5 py-3.5 bg-sky-600 hover:bg-sky-700 text-white rounded-2xl font-bold text-xs shadow-lg uppercase transition-all duration-200 hover:scale-[1.02]">
                        Lanjut
                    </button>
                </template>
            </div>
        </section>

        <aside :class="showMobileNav ? 'translate-y-0' : 'translate-y-full md:translate-y-0'"
               class="fixed inset-0 z-[120] md:relative md:z-10 md:w-80 flex flex-col transition-transform duration-300 pointer-events-none md:pointer-events-auto">

            <div @click="showMobileNav = false"
                 class="md:hidden absolute inset-0 bg-slate-900/60 backdrop-blur-sm pointer-events-auto"></div>

            <div class="mt-auto md:mt-0 bg-white md:rounded-[2rem] border border-slate-200 h-[75vh] md:h-full flex flex-col pointer-events-auto overflow-hidden rounded-t-[2.5rem] relative">

                <div class="p-6 border-b bg-slate-50/50">
                    <div class="flex justify-between items-center">
                        <h2 class="font-extrabold text-slate-800 text-[10px] tracking-[0.2em] uppercase">
                            Peta Navigasi
                        </h2>

                        <button @click="showMobileNav = false"
                                class="md:hidden text-slate-400 font-black text-xs">
                            Tutup
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-3 mt-4 text-[10px] text-slate-500">

                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 rounded bg-sky-100 border border-sky-100"></div>
                            <span>Terjawab</span>
                        </div>

                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 rounded bg-amber-400"></div>
                            <span>Ragu</span>
                        </div>

                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 rounded bg-sky-600"></div>
                            <span>Aktif</span>
                        </div>

                        <div class="flex items-center gap-1">
                            <div class="w-3 h-3 rounded bg-red-400"></div>
                            <span>Belum tersimpan</span>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-5 grid grid-cols-5 gap-3 custom-scroll content-start">
                    <template x-for="(soal, index) in listSoal" :key="soal.id">

                        <button @click="
                                currentIndex = index;
                                showMobileNav = false;
                                refreshMath();
                                preloadNextImage();
                                scrollToTop();
                            "
                                class="aspect-square rounded-xl border-2 flex items-center justify-center text-xs font-black transition-all duration-200 hover:scale-105"
                                :class="getNavClass(soal, index)">

                            <span x-text="index + 1"></span>
                        </button>

                    </template>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100">

                    <div class="mb-4 text-[11px] text-slate-500 leading-relaxed">
                        Pastikan semua soal sudah terjawab sebelum menyelesaikan ujian.
                    </div>

                    <button @click="confirmSelesai()"
                            :disabled="settingTombolSelesai !== false && timeLeft > settingTombolSelesai"
                            :class="settingTombolSelesai !== false && timeLeft > settingTombolSelesai
                                ? 'bg-slate-300 cursor-not-allowed opacity-50'
                                : 'bg-red-500 hover:bg-red-600 shadow-xl hover:scale-[1.02]'"
                            class="w-full py-4 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all duration-200">

                        <span x-text="settingTombolSelesai !== false && timeLeft > settingTombolSelesai ? 'Selesai (Kunci)' : 'Selesai Ujian'"></span>
                    </button>
                </div>
            </div>
        </aside>
    </main>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <form id="form-selesai"
        action="{{ route('ujian.selesai', $jadwal->id) }}"
        method="POST"
        class="hidden">
        @csrf
    </form>

<script>
    function examHandler() {
        return {
            showMobileNav: false,
            fontSize: 'medium',
            timeLeft: Math.max(0, Math.floor({{ (int) $timeLeft }})),
            settingTombolSelesai: @json($settingTombolSelesai),
            settingAntiNyontek: @json($settingAntiNyontek),
            currentIndex: 0,
            listSoal: @json($listSoal),
            pelanggaran: {{ (int) $pelanggaran }},
            maxPelanggaran: @json($settingMaxPelanggaran),
            isBlocked: false,
            isExiting: false,
            isSaving: false,
            isSubmitting: false,
            isOnline: navigator.onLine,
            isProcessingViolation: false,
            saveTimers: {},
            pendingSaves: {},
            retryTimer: null,
            saveAlertAt: 0,
            // Batas tunggu 1x simpan ke server (ms). Lewat ini = dianggap gagal,
            // jawaban DITAHAN (tidak hilang) dan dikirim ulang otomatis.
            saveTimeoutMs: 12000,
            // Toleransi meninggalkan halaman (ms). Keluar di bawah ini (layar HP
            // mati sesaat, telepon masuk, tarik notifikasi) hanya ditegur ringan,
            // TIDAK dihitung pelanggaran.
            violationGraceMs: 5000,
            leaveTimer: null,
            rearmUntil: 0,

            get currentSoal() {
                return this.listSoal[this.currentIndex] || {};
            },

            get unsavedCount() {
                return this.listSoal.filter((s) => s.saveState === 'error').length;
            },

            init() {
                // Tandai semua jawaban bawaan server sebagai sudah tersimpan.
                this.listSoal.forEach((s) => {
                    s.saveState = 'saved';
                });

                this.startTimer();
                this.refreshMath();
                this.preloadNextImage();

                // Hanya aktifkan proteksi jika anti-contek aktif
                if (this.settingAntiNyontek) {
                    this.setupProtection();

                    if (this.maxPelanggaran > 0 && this.pelanggaran >= this.maxPelanggaran) {
                        this.blokirUser();
                    }
                }

                window.addEventListener('online', () => {
                    this.isOnline = true;
                    this.flushPendingSaves();
                });

                window.addEventListener('offline', () => {
                    this.isOnline = false;
                });
            },

            setupProtection() {
                if (!this.settingAntiNyontek) return;

                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'hidden') {
                        this.onPotentialLeave();
                    } else {
                        this.onReturnToExam();
                    }
                });

                window.addEventListener('blur', () => this.onPotentialLeave());
                window.addEventListener('focus', () => this.onReturnToExam());
            },

            onPotentialLeave() {
                if (!this.settingAntiNyontek || this.isBlocked || this.isProcessingViolation) return;
                // Hanya halaman yang benar-benar tersembunyi yang dijadwalkan.
                // blur tanpa hidden (mis. popup izin browser) diabaikan.
                if (document.visibilityState !== 'hidden') return;
                if (Date.now() < this.rearmUntil) return;
                if (this.leaveTimer) return;

                this.leaveTimer = setTimeout(() => {
                    this.leaveTimer = null;
                    if (document.visibilityState === 'hidden') {
                        this.handleViolation();
                    }
                }, this.violationGraceMs);
            },

            onReturnToExam() {
                // Kembali sebelum grace habis = kejadian jinak (layar mati sesaat,
                // notifikasi, dsb): batalkan hitungan, beri teguran ringan saja.
                if (this.leaveTimer) {
                    clearTimeout(this.leaveTimer);
                    this.leaveTimer = null;

                    this.rearmUntil = Date.now() + 3000;

                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'info',
                        title: 'Tetap di halaman ujian ya — keluar lebih dari 5 detik tercatat sebagai pelanggaran',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    return;
                }

                this.rearmUntil = Date.now() + 3000;
            },

            async handleViolation() {
                if (!this.settingAntiNyontek || this.isBlocked || this.isProcessingViolation) return;

                this.isProcessingViolation = true;
                this.rearmUntil = Date.now() + 3000;

                const controller = new AbortController();
                const timer = setTimeout(() => controller.abort(), this.saveTimeoutMs);

                try {
                    const response = await fetch("{{ route('ujian.pelanggaran') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            jadwal_id: {{ $jadwal->id }}
                        }),
                        signal: controller.signal
                    });

                    clearTimeout(timer);

                    if (response.status === 419) {
                        Swal.fire({
                            title: 'Sesi Berakhir',
                            text: 'Sesi Anda habis. Refresh halaman dan masuk kembali agar ujian tercatat dengan benar.',
                            icon: 'warning',
                            confirmButtonColor: '#0ea5e9'
                        });
                        return;
                    }

                    const data = await response.json();
                    this.pelanggaran = data.total;
                    if (data.max) this.maxPelanggaran = data.max;

                    if (data.blocked) {
                        this.blokirUser();
                    } else {
                        await Swal.fire({
                            title: 'Peringatan!',
                            text: `Anda terdeteksi keluar halaman ujian lebih dari 5 detik. Pelanggaran: (${this.pelanggaran}/${this.maxPelanggaran}). Tetap di halaman ujian ya.`,
                            icon: 'warning',
                            confirmButtonColor: '#0ea5e9'
                        });
                    }

                } catch (e) {
                    clearTimeout(timer);
                    console.error('Gagal mencatat pelanggaran:', e);
                    // Jangan diam: hitungan tetap di server, tapi siswa wajib tahu
                    // agar tidak bingung dan tetap di halaman ujian.
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'warning',
                        title: 'Koneksi bermasalah. Tetap di halaman ujian — jangan pindah tab.',
                        showConfirmButton: false,
                        timer: 4000
                    });
                } finally {
                    this.isProcessingViolation = false;
                }
            },

            blokirUser() {
                this.forceExitToDaftar(
                    'AKUN DIBLOKIR!',
                    'Pelanggaran telah mencapai batas maksimal. Mengalihkan ke daftar ujian...'
                );
            },

            forceExitToDaftar(title, text) {
                // Satu pintu keluar untuk semua kasus blokir: tanpa tombol OK,
                // tanpa logout, langsung kembali ke daftar ujian setelah jeda singkat.
                // Status tetap login agar siswa bisa lanjut lagi setelah pengawas membuka blokir.
                if (this.isExiting) return;
                this.isExiting = true;
                this.isBlocked = true;

                // Hentikan semua aktivitas latar agar tidak request sia-sia.
                if (this.retryTimer) {
                    clearInterval(this.retryTimer);
                    this.retryTimer = null;
                }
                if (this.leaveTimer) {
                    clearTimeout(this.leaveTimer);
                    this.leaveTimer = null;
                }
                Object.values(this.saveTimers).forEach((t) => clearTimeout(t));

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'error',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });

                setTimeout(() => {
                    window.location.href = "{{ route('home') }}";
                }, 2500);
            },

            confirmSelesai() {
                if (this.isSubmitting || this.isExiting) return;

                // Jika anti-contek nonaktif atau settingTombolSelesai bernilai false, tombol langsung bisa ditekan
                if (
                    this.settingAntiNyontek &&
                    this.settingTombolSelesai !== false &&
                    this.timeLeft > this.settingTombolSelesai
                ) {
                    return;
                }

                const belum = this.unsavedCount;

                Swal.fire({
                    title: 'Selesai Ujian?',
                    text: belum > 0
                        ? `Ada ${belum} jawaban belum tersimpan ke server. Saat konfirmasi, sistem akan mengirim semuanya dulu.`
                        : 'Pastikan semua jawaban sudah terisi dengan benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Selesai',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0ea5e9',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submitUjian();
                    }
                });
            },

            async submitUjian() {
                if (this.isSubmitting || this.isExiting) return;
                this.isSubmitting = true;

                try {
                    // Pastikan semua jawaban benar-benar sampai ke server dulu.
                    // Tanpa ini, jawaban terakhir bisa hilang karena balapan
                    // antara request simpan dan submit form selesai.
                    await this.flushPendingSaves();

                    const gagal = this.listSoal.filter((s) => s.saveState === 'error');

                    if (gagal.length > 0) {
                        const hasil = await Swal.fire({
                            title: 'Ada jawaban belum tersimpan!',
                            html: `<b>${gagal.length} jawaban</b> belum sampai ke server (soal nomor: ${gagal.map((s) => s.nomor).join(', ')}).<br><br>Coba kirim lagi, atau selesaikan dengan risiko jawaban tersebut bernilai kosong.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Coba Kirim Lagi',
                            cancelButtonText: 'Tetap Selesaikan',
                            confirmButtonColor: '#0ea5e9',
                            cancelButtonColor: '#64748b'
                        });

                        if (hasil.isConfirmed) {
                            return;
                        }
                    }

                    document.getElementById('form-selesai').submit();
                } finally {
                    this.isSubmitting = false;
                }
            },

            refreshSavingIndicator() {
                this.isSaving = this.listSoal.some((s) => s.saveState === 'saving');
            },

            async saveToDb(soal, options = {}) {
                const silent = options.silent === true;
                if (this.isBlocked || !soal) return false;

                soal.saveState = 'saving';
                this.refreshSavingIndicator();

                const payloadData = {
                    jadwal_id: {{ $jadwal->id }},
                    mapel_id: {{ $mapel->id }},
                    soal_id: soal.id,
                    jawaban_id: soal.jawaban_terpilih,
                    is_ragu: soal.is_ragu ? 1 : 0
                };

                const controller = new AbortController();
                const timer = setTimeout(() => controller.abort(), this.saveTimeoutMs);

                try {
                    const response = await fetch("{{ route('ujian.simpan') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payloadData),
                        signal: controller.signal
                    });

                    clearTimeout(timer);

                    // Sesi kedaluwarsa (CSRF tidak cocok): JANGAN anggap offline,
                    // siswa harus refresh agar token baru — bukan klik ulang sia-sia.
                    if (response.status === 419) {
                        soal.saveState = 'error';
                        delete this.pendingSaves[soal.id];
                        this.refreshSavingIndicator();
                        Swal.fire({
                            title: 'Sesi Berakhir',
                            text: 'Sesi Anda habis. REFRESH halaman ini lalu jawab ulang soal yang bertanda merah agar tersimpan.',
                            icon: 'warning',
                            confirmButtonColor: '#0ea5e9'
                        });
                        return false;
                    }

                    // Waktu habis / akun diblokir: tampilkan pesan server apa adanya.
                    if (response.status === 403) {
                        const data = await response.json().catch(() => ({}));

                        // Akun diblokir = sama seperti pelanggaran maks:
                        // tanpa tombol OK, langsung kembali ke daftar ujian.
                        if (data.blocked === true) {
                            this.forceExitToDaftar('Akun Diblokir', data.error || 'Akun diblokir. Mengalihkan ke daftar ujian...');
                            return false;
                        }

                        soal.saveState = 'error';
                        delete this.pendingSaves[soal.id];
                        this.refreshSavingIndicator();
                        Swal.fire({
                            title: 'Tidak Dapat Menyimpan',
                            text: data.error || 'Ujian tidak lagi valid.',
                            icon: 'warning',
                            confirmButtonColor: '#0ea5e9'
                        });
                        return false;
                    }

                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }

                    // SUKSES: jawaban aman di server.
                    soal.saveState = 'saved';
                    delete this.pendingSaves[soal.id];
                    this.isOnline = true;
                    this.refreshSavingIndicator();
                    return true;

                } catch (e) {
                    clearTimeout(timer);
                    console.error('Simpan gagal:', e);

                    const timeout = e && e.name === 'AbortError';
                    // Hanya masalah jaringan/timeout yang berarti koneksi bermasalah.
                    // Error HTTP (500/validasi) bukan urusan koneksi — jangan blokir siswa.
                    if (timeout || !navigator.onLine || (e instanceof TypeError)) {
                        this.isOnline = false;
                    }

                    // PENTING: pilihan siswa TIDAK dihapus (tidak di-revert).
                    // Ditandai merah + masuk antrean kirim ulang otomatis.
                    soal.saveState = 'error';
                    this.pendingSaves[soal.id] = true;
                    this.refreshSavingIndicator();
                    this.scheduleRetry();

                    if (!silent && Date.now() - this.saveAlertAt > 30000) {
                        this.saveAlertAt = Date.now();
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'warning',
                            title: timeout
                                ? 'Server lama merespons — jawaban DITAHAN dan dikirim otomatis. Jangan refresh dulu, tetap di halaman ini.'
                                : 'Jawaban belum tersimpan — DITAHAN dan dikirim otomatis saat koneksi pulih. Soal bermasalah bertanda merah.',
                            showConfirmButton: false,
                            timer: 5000
                        });
                    }

                    return false;
                }
            },

            scheduleRetry() {
                // Kirim ulang otomatis semua yang tertahan tiap 8 detik,
                // sampai antrean kosong. Berhenti sendiri bila tidak ada sisa.
                if (this.retryTimer) return;

                this.retryTimer = setInterval(async () => {
                    const ids = Object.keys(this.pendingSaves);

                    if (!ids.length || this.isBlocked) {
                        clearInterval(this.retryTimer);
                        this.retryTimer = null;
                        return;
                    }

                    for (const id of ids) {
                        const soal = this.listSoal.find((s) => String(s.id) === String(id));
                        if (soal && soal.saveState === 'error') {
                            await this.saveToDb(soal, { silent: true });
                        } else {
                            delete this.pendingSaves[id];
                        }
                    }
                }, 8000);
            },

            async flushPendingSaves() {
                const antre = this.listSoal.filter((s) => s.saveState !== 'saved');
                for (const soal of antre) {
                    await this.saveToDb(soal, { silent: true });
                }
            },

            queueSave(soal) {
                // Debounce per soal (bukan 1 timer global): klik cepat pindah-pindah
                // soal tidak lagi saling membatalkan simpanan.
                clearTimeout(this.saveTimers[soal.id]);
                this.saveTimers[soal.id] = setTimeout(() => {
                    this.saveToDb(soal);
                }, 400);
            },

            handleSelect(db_id) {
                // Optimistic UI: pilihan langsung tampil & TIDAK dihapus walau gagal.
                // Status kirim terlihat di indikator header + peta soal (merah = belum tersimpan).
                this.currentSoal.jawaban_terpilih = db_id;
                this.currentSoal.saveState = 'saving';
                this.refreshSavingIndicator();
                this.queueSave(this.currentSoal);
            },

            toggleRagu() {
                this.currentSoal.is_ragu = !this.currentSoal.is_ragu;
                this.currentSoal.saveState = 'saving';
                this.refreshSavingIndicator();
                this.queueSave(this.currentSoal);
            },

            startTimer() {
                const endTime = Date.now() + (this.timeLeft * 1000);

                const timer = setInterval(() => {
                    const remaining = Math.max(0, Math.floor((endTime - Date.now()) / 1000));
                    this.timeLeft = remaining;

                    if (remaining <= 0) {
                        clearInterval(timer);
                        this.submitUjian();
                    }
                }, 250);
            },

            formatTime(s) {
                const total = Math.max(0, Math.floor(s || 0));
                const h = Math.floor(total / 3600).toString().padStart(2, '0');
                const m = Math.floor((total % 3600) / 60).toString().padStart(2, '0');
                const sec = (total % 60).toString().padStart(2, '0');

                return `${h}:${m}:${sec}`;
            },

            next() {
                if (this.currentIndex < this.listSoal.length - 1) {
                    this.currentIndex++;
                    this.refreshMath();
                    this.preloadNextImage();
                    this.scrollToTop();
                }
            },

            prev() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                    this.refreshMath();
                    this.preloadNextImage();
                    this.scrollToTop();
                }
            },

            setFont(size) {
                this.fontSize = size;
                this.$nextTick(() => {
                    this.refreshMath();
                });
            },

            scrollToTop() {
                const target = document.getElementById('exam-content');
                if (target) {
                    target.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            },

            getNavClass(soal, index) {
                // Merah = jawaban BELUM tersimpan di server (gagal kirim / antre).
                // Ini sinyal ke siswa: jangan selesaikan dulu / tunggu kirim ulang otomatis.
                if (soal.saveState === 'error' && this.currentIndex !== index) {
                    return 'bg-red-100 border-red-400 text-red-600 shadow-sm animate-pulse';
                }
                if (this.currentIndex === index) {
                    return 'bg-sky-600 border-sky-600 text-white shadow-lg scale-110 z-10';
                }
                if (soal.is_ragu) {
                    return 'bg-amber-400 border-amber-400 text-white shadow-sm';
                }
                if (soal.jawaban_terpilih) {
                    return 'bg-sky-100 border-sky-100 text-sky-700 shadow-sm';
                }
                return 'bg-white border-slate-100 text-slate-300';
            },

            logoutConfirm() {
                Swal.fire({
                    title: 'Keluar?',
                    text: 'Sesi ujian akan tetap berjalan. Anda bisa masuk kembali selama waktu tersedia.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Keluar',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form').submit();
                    }
                });
            },

            preloadNextImage() {
                const nextSoal = this.listSoal[this.currentIndex + 1];
                if (!nextSoal) return;

                if (nextSoal.gambar_soal) {
                    const img = new Image();
                    img.src = nextSoal.gambar_soal;
                }

                if (nextSoal.pertanyaan) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = nextSoal.pertanyaan;
                    tempDiv.querySelectorAll('img').forEach((image) => {
                        const img = new Image();
                        img.src = image.src;
                    });
                }

                if (nextSoal.pilihan) {
                    nextSoal.pilihan.forEach((opt) => {
                        if (!opt.teks) return;
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = opt.teks;
                        tempDiv.querySelectorAll('img').forEach((image) => {
                            const img = new Image();
                            img.src = image.src;
                        });
                    });
                }
            },

            refreshMath() {
                this.$nextTick(() => {
                    if (!window.MathJax) return;
                    const target = document.getElementById('exam-content');
                    if (target) {
                        MathJax.typesetClear([target]);
                        MathJax.typesetPromise([target]).catch((err) => {
                            console.error('MathJax render error:', err);
                        });
                    }
                });
            }
        };
    }
</script>
</body>
</html>