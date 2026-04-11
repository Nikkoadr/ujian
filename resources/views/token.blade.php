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
    }

    .timer-text {
        font-family: 'Monaco', 'Consolas', monospace;
        font-weight: bold;
        color: #4e73df;
        font-size: 1.2rem;
        margin-left: 10px;
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
        h1.h3 {
            font-size: 1.25rem;
        }
        .timer-text {
            font-size: 1rem;
        }
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center mt-3 mt-md-5">
        <div class="col-12 col-md-8 col-lg-6 text-center">
            <h1 class="h3 mb-4 text-gray-800 font-weight-bold">Token Akses Ujian</h1>

            <div class="card shadow border-0 mx-auto" style="border-radius: 20px; max-width: 100%;">
                <div class="card-header bg-dark py-3" style="border-radius: 20px 20px 0 0;">
                    <h6 class="m-0 font-weight-bold text-white text-uppercase small" style="letter-spacing: 1px;">
                        Token Aktif
                    </h6>
                </div>
                
                <div class="card-body bg-white py-5">
                    <div class="timer-badge shadow-sm">
                        <i class="fas fa-clock text-gray-400"></i>
                        <span id="countdown-timer" class="timer-text">00:00</span>
                    </div>

                    <div class="py-3">
                        <span class="token-display font-weight-bold text-primary d-block text-truncate">
                            {{ $token }}
                        </span>
                    </div>
                </div>

                <div class="card-footer bg-light border-0 py-3 text-muted">
                    <div class="d-flex justify-content-center align-items-center">
                        <div id="status-spinner" class="spinner-grow spinner-grow-sm text-success mr-2" role="status" style="width: 10px; height: 10px;"></div>
                        <small class="font-weight-bold text-uppercase" style="letter-spacing: 1px;">Terpantau aktif</small>
                    </div>
                </div>
            </div>

            <div class="mt-4 px-3">
                <p class="text-muted small">
                    <i class="fas fa-sync fa-spin mr-1 text-primary"></i> 
                    Token akan diperbarui otomatis saat waktu habis.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi waktu sisa dari server (dalam detik)
    let timeLeft = {{ $secondsRemaining }};
    const timerDisplay = document.getElementById('countdown-timer');
    const spinner = document.getElementById('status-spinner');

    function updateTimer() {
        if (timeLeft <= 0) {
            timerDisplay.innerHTML = "00:00";
            spinner.classList.remove('text-success');
            spinner.classList.add('text-danger');
            
            // Refresh halaman untuk mengambil token baru hasil update di database
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            return;
        }

        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;

        // Pad dengan angka 0 jika di bawah 10
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        timerDisplay.innerHTML = `${minutes}:${seconds}`;
        
        // Efek warna jika waktu hampir habis (di bawah 30 detik)
        if (timeLeft < 30) {
            timerDisplay.classList.remove('text-primary');
            timerDisplay.classList.add('text-danger');
        }

        timeLeft--;
    }

    // Jalankan timer setiap 1 detik
    setInterval(updateTimer, 1000);
    
    // Jalankan sekali saat load agar tidak menunggu 1 detik pertama
    updateTimer();
</script>
@endpush