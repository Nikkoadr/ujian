<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Aplikasi | CBT SMKMUHKDH</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        surface: '#ffffff',
                        background: '#f8fafc',
                    }
                }
            }
        }
    </script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f8fafc;
        }
        .login-card { 
            background: #ffffff;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.03), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        }
    </style>
</head>
<body class="h-full antialiased text-slate-600">

<div class="min-h-screen flex flex-col items-center justify-center px-6 py-12">
    
    <!-- HEADER -->
    <div class="mb-10 flex flex-col items-center gap-4">
        <div class="h-16 w-16 bg-white rounded-2xl shadow-sm flex items-center justify-center border border-slate-100">
            <img src="{{ asset('assets/img/logo.png') }}" class="w-10 h-10 object-contain" alt="Logo">
        </div>
        <div class="text-center">
            <h1 class="text-xl font-bold text-slate-900">
                CBT <span class="text-primary">SMKMUHKDH</span>
            </h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                SMK Muhammadiyah Kandanghaur
            </p>
        </div>
    </div>

    <!-- CARD -->
    <div class="w-full max-w-[400px]">
        <div class="login-card p-8 rounded-[2rem] border border-slate-100">
            
            <div class="mb-8">
                <h2 class="text-xl font-bold text-slate-900">Selamat Datang</h2>
                <p class="text-slate-400 text-sm mt-1">Silakan masuk dengan akun siswa anda</p>
            </div>

            <!-- GLOBAL ERROR -->
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200">
                    <ul class="text-xs text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- EMAIL -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-2 ml-1 italic">Email</label>
                    
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-5 py-3.5 bg-slate-50 border 
                        @error('email') border-red-500 @else border-slate-200 @enderror
                        rounded-2xl focus:outline-none focus:border-primary focus:bg-white text-sm font-medium text-slate-900"
                        placeholder="1234@smkmuhkandanghaur.sch.id">

                    @error('email')
                        <p class="text-xs text-red-500 mt-2 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PASSWORD -->
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-[11px] font-bold text-slate-500 italic">Kata Sandi</label>
                        <a href="#" class="text-[11px] font-bold text-primary hover:underline">Lupa?</a>
                    </div>

                    <div class="relative">
                        <input id="password" type="password" name="password" required
                            class="w-full px-5 py-3.5 pr-12 bg-slate-50 border 
                            @error('password') border-red-500 @else border-slate-200 @enderror
                            rounded-2xl focus:outline-none focus:border-primary focus:bg-white text-sm font-medium text-slate-900"
                            placeholder="••••••••">

                        <!-- TOGGLE BUTTON -->
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary">
                            
                            <!-- ICON -->
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 
                                    0 8.268 2.943 9.542 7-1.274 
                                    4.057-5.065 7-9.542 7-4.477 
                                    0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>

                    @error('password')
                        <p class="text-xs text-red-500 mt-2 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- REMEMBER -->
                <div class="flex items-center gap-2 px-1">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-slate-300 rounded">
                    <label class="text-xs text-slate-500">Ingat saya</label>
                </div>

                <!-- BUTTON -->
                <button type="submit" 
                    class="w-full bg-primary text-white py-4 rounded-2xl font-bold text-sm hover:bg-blue-700 transition active:scale-95 shadow-lg shadow-primary/20">
                    Masuk Ke Akun
                </button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <p class="text-[10px] text-slate-400 uppercase tracking-widest">
                &copy; 2026 SMK Muhammadiyah Kandanghaur. All rights reserved.
            </p>
        </div>
    </div>
</div>

<!-- SCRIPT -->
<script>
function togglePassword() {
    const input = document.getElementById("password");
    const icon = document.getElementById("eyeIcon");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.add("text-primary");
    } else {
        input.type = "password";
        icon.classList.remove("text-primary");
    }
}
</script>

</body>
</html>