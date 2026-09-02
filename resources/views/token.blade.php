@extends('layouts.app')
@section('title', 'Token Ujian')

@section('content')
<style>
    /* Styling Dasar Token */
    .token-display {
        font-size: 5rem;
        letter-spacing: 15px;
        font-family: 'Courier New', Courier, monospace;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.05);
    }

    /* Elemen Timer Hitung Mundur */
    .timer-badge {
        background: #f8f9fc;
        border: 2px solid #e3e6f0;
        border-radius: 50px;
        padding: 5px 20px;
        display: inline-flex;
        align-items: center;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .timer-text {
        font-family: 'Monaco', 'Consolas', monospace;
        font-weight: bold;
        color: #4e73df;
        font-size: 1.2rem;
        margin-left: 10px;
    }

    /* Status ketika waktu habis (Stale) */
    .timer-stale {
        background: #fff5f5;
        border-color: #feb2b2;
    }
    .text-stale {
        color: #e53e3e !important;
    }

    /* Responsivitas Mobile */
    @media (max-width: 576px) {
        .token-display {
            font-size: 2.5rem !important;
            letter-spacing: 5px !important;
        }
        .card-body {
            padding: 2rem 1rem !important;
        }
        h1.h3 { font-size: 1.25rem; }
        .timer-text { font-size: 1rem; }
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center mt-3 mt-md-5">
        <div class="col-12 col-md-8 col-lg-6 text-center">
            <h1 class="h3 mb-4 text-gray-800 font-weight-bold">Dashboard Token Admin</h1>

            <div class="card shadow border-0 mx-auto" style="border-radius: 20px; max-width: 100%;">
                <div class="card-header bg-dark py-3 d-flex justify-content-between align-items-center" style="border-radius: 20px 20px 0 0;">
                    <h6 class="m-0 font-weight-bold text-white text-uppercase small" style="letter-spacing: 1px;">
                        Token Akses Saat Ini
                    </h6>
                    <span class="badge badge-light text-dark">
                        {{ isset($jadwal) && $jadwal->mapel ? $jadwal->mapel->nama_mapel : 'Tidak Ada Jadwal Aktif' }}
                    </span>
                </div>
                
                <div class="card-body bg-white py-5">
                    <div id="timer-container" class="timer-badge shadow-sm {{ $isStale ? 'timer-stale' : '' }}">
                        <i id="timer-icon" class="fas fa-clock {{ $isStale ? 'text-danger' : 'text-gray-400' }}"></i>
                        <span id="countdown-timer" class="timer-text {{ $isStale ? 'text-stale' : '' }}">
                            {{ $isStale ? 'WAITING...' : '00:00' }}
                        </span>
                    </div>

                    <div class="py-3">
                        <span class="token-display font-weight-bold text-primary d-block text-truncate">
                            {{ $token }}
                        </span>
                    </div>
                </div>

                <div class="card-footer bg-light border-0 py-3 text-muted">
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="status-spinner" class="spinner-grow spinner-grow-sm {{ $isStale ? 'text-danger' : 'text-success' }} mr-2" role="status" style="width: 10px; height: 10px;"></div>
                        <small class="font-weight-bold text-uppercase" style="letter-spacing: 1px;">
                            Status: <span id="status-text">{{ $isStale ? 'Menunggu Update Sistem' : 'Terpantau Aktif' }}</span>
                        </small>
                    </div>
                </div>
            </div>

            <div class="mt-4 px-3">
                <p class="text-muted small">
                    <i class="fas fa-sync fa-spin mr-1 text-primary"></i> 
                    Token sinkron dengan database. Update otomatis setiap 5 menit via Cron Job.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const initialTimeLeft = Number({{ $secondsRemaining }});
    const endTime = Date.now() + (initialTimeLeft * 1000);

    let isStaleServer = {{ $isStale ? 'true' : 'false' }};
    let refreshAttempted = false;

    const timerDisplay = document.getElementById('countdown-timer');
    const timerContainer = document.getElementById('timer-container');
    const timerIcon = document.getElementById('timer-icon');
    const statusText = document.getElementById('status-text');
    const spinner = document.getElementById('status-spinner');

    function getTimeLeft() {
        return Math.max(0, Math.floor((endTime - Date.now()) / 1000));
    }

    function setStaleState() {
        timerDisplay.innerHTML = "MENUNGGU...";

        timerDisplay.classList.add('text-stale');
        timerContainer.classList.add('timer-stale');

        spinner.classList.remove('text-success');
        spinner.classList.add('text-danger');

        statusText.innerText = "MENUNGGU UPDATE SISTEM";
    }

    function tryReload() {
        if (refreshAttempted) return;

        refreshAttempted = true;

        setTimeout(() => {
            window.location.reload();
        }, 10000);
    }

    function updateTimer() {
        const timeLeft = getTimeLeft();

        if (timeLeft <= 0 || isStaleServer) {
            setStaleState();
            tryReload();
            return;
        }

        const minutes = Math.floor(timeLeft / 60)
            .toString()
            .padStart(2, '0');

        const seconds = (timeLeft % 60)
            .toString()
            .padStart(2, '0');

        timerDisplay.innerHTML = `${minutes}:${seconds}`;

        // warning 30 detik terakhir
        if (timeLeft < 30) {

            timerDisplay.classList.remove('text-primary');
            timerDisplay.classList.add('text-danger');

            if (!timerIcon.classList.contains('fa-beat-alpha')) {
                timerIcon.classList.add('fa-beat-alpha');
            }
        }
    }

    // update realtime berbasis jam asli
    setInterval(updateTimer, 250);

    updateTimer();
</script>
@endpush