@extends('layouts.mobile')
@section('title', 'Token Aktif')

@push('styles')
<style>
    /* Font khusus untuk Token agar terlihat techy */
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@800&display=swap');
    
    .token-font {
        font-family: 'JetBrains Mono', monospace;
    }

    /* Animasi halus untuk denyut nadi saat waktu hampir habis */
    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .timer-urgent {
        animation: pulse-red 2s infinite;
        border-color: #fca5a5 !important;
        background-color: #fef2f2 !important;
        color: #ef4444 !important;
    }
</style>
@endpush

@section('content')
<div class="min-h-[80vh] flex flex-col px-6 py-10">
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Token Akses</h1>
        <p class="text-slate-500 text-sm mt-1 font-medium">Gunakan kode ini untuk masuk ke ujian.</p>
    </div>

    <div class="relative bg-white border border-slate-200 rounded-[40px] p-10 text-center shadow-xl shadow-indigo-100/50 overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-50 rounded-full opacity-50"></div>
        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-slate-50 rounded-full opacity-50"></div>

        <span class="relative z-10 text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-6 block">
            Kode Valid Sesi Ini
        </span>
        
        <div class="relative z-10 flex flex-col items-center">
            <span id="token-display" class="token-font text-5xl sm:text-6xl font-extrabold tracking-widest bg-gradient-to-br from-slate-900 via-indigo-900 to-indigo-600 bg-clip-text text-transparent drop-shadow-sm mb-2">
                {{ $token }}
            </span>
            
            <button onclick="copyToken()" class="mt-2 flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-600 rounded-full transition-all active:scale-95">
                <i class="fa-regular fa-copy text-xs"></i>
                <span class="text-[10px] font-bold uppercase tracking-wider">Salin Kode</span>
            </button>
        </div>

        <div class="mt-12 flex justify-center">
            <div id="timer-container" class="flex items-center gap-3 px-6 py-3 bg-slate-50 border border-slate-200 rounded-2xl transition-all duration-500">
                <i class="fa-solid fa-clock-rotate-left text-indigo-500" id="timer-icon"></i>
                <span id="timer" class="token-font text-xl font-bold text-slate-700 leading-none">
                    00:00
                </span>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-indigo-50/50 rounded-2xl p-4 border border-indigo-100/50">
        <div class="flex gap-3">
            <i class="fa-solid fa-circle-info text-indigo-500 mt-0.5"></i>
            <p class="text-[11px] text-indigo-900/70 leading-relaxed font-medium">
                Token diperbarui secara otomatis tiap sesi. Jika token tidak muncul atau tidak bisa digunakan, pastikan pengaturan waktu di HP Anda diset ke <strong>Otomatis</strong>.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let timeLeft = {{ $secondsRemaining }};
    const timerText = document.getElementById('timer');
    const timerCont = document.getElementById('timer-container');
    const timerIcon = document.getElementById('timer-icon');

    function update() {
        if(timeLeft <= 0) { 
            timerText.innerText = "REFRESH...";
            window.location.reload(); 
            return; 
        }

        const min = Math.floor(timeLeft / 60).toString().padStart(2, '0');
        const sec = (timeLeft % 60).toString().padStart(2, '0');
        
        timerText.innerText = `${min}:${sec}`;

        // Efek visual jika waktu kurang dari 60 detik (Urgent)
        if(timeLeft < 60) {
            timerCont.classList.add('timer-urgent');
            timerIcon.classList.replace('text-indigo-500', 'text-red-500');
            timerIcon.classList.add('fa-spin-pulse');
        }

        timeLeft--;
    }

    // Fungsi Copy to Clipboard
    function copyToken() {
        const token = "{{ $token }}";
        navigator.clipboard.writeText(token).then(() => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: 'Token berhasil disalin'
            });
        });
    }

    setInterval(update, 1000);
    update();
</script>
@endpush