<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Masuk Aplikasi | {{ config('app.name', 'CBT MUHKA') }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        .login-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="h-full antialiased text-slate-700">

<div class="min-h-screen flex flex-col items-center justify-center px-4 py-10 relative overflow-hidden">
    
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-sky-100 rounded-full opacity-50 blur-3xl"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-100 rounded-full opacity-50 blur-3xl"></div>

    <div class="mb-8 flex flex-col items-center gap-3 relative z-10">
        <div class="h-16 w-16 bg-white rounded-[2rem] shadow-xl shadow-sky-900/10 flex items-center justify-center border border-slate-100">
            <img src="{{ asset('assets/img/logo.png') }}" class="w-10 h-10 object-contain" alt="Logo">
        </div>
        <div class="text-center">
            <h1 class="text-xl font-black tracking-tight text-sky-900 uppercase">CBT <span class="text-sky-600">MUHKA</span></h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em]">SMK Muhammadiyah Kandanghaur</p>
        </div>
    </div>

    <div class="w-full max-w-[420px] relative z-10">
        <div class="login-card border border-white p-8 sm:p-10 rounded-[3rem] shadow-2xl shadow-sky-900/5">
            
            <div class="mb-10 text-center">
                <h2 class="text-2xl font-extrabold text-slate-800">Selamat Datang</h2>
                <p class="text-slate-400 mt-2 text-xs font-bold uppercase tracking-wider">Silahkan masuk ke akun Anda</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-[10px] font-black uppercase tracking-[0.15em] text-slate-400 mb-2 ml-4">Alamat Email / NISN</label>
                    <div class="relative">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl focus:outline-none focus:border-sky-500 focus:bg-white transition-all text-sm font-bold @error('email') border-red-500 @enderror"
                            placeholder="nama@siswa.com">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" /></svg>
                        </div>
                    </div>
                    @error('email')
                        <p class="mt-2 text-[10px] font-bold text-red-500 ml-4 italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2 ml-4">
                        <label for="password" class="block text-[10px] font-black uppercase tracking-[0.15em] text-slate-400">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[10px] font-extrabold text-sky-600 hover:text-sky-700">Lupa?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full pl-12 pr-6 py-4 bg-slate-50 border-2 border-slate-50 rounded-2xl focus:outline-none focus:border-sky-500 focus:bg-white transition-all text-sm font-bold @error('password') border-red-500 @enderror"
                            placeholder="••••••••">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                    </div>
                    @error('password')
                        <p class="mt-2 text-[10px] font-bold text-red-500 ml-4 italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 ml-4 py-2">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                        class="w-5 h-5 rounded-lg border-slate-200 text-sky-600 focus:ring-sky-500/20 transition-all cursor-pointer">
                    <label for="remember" class="text-xs font-bold text-slate-500 select-none cursor-pointer">
                        Ingat sesi saya
                    </label>
                </div>

                <button type="submit" 
                    class="w-full bg-sky-800 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-sky-900 transition-all shadow-xl shadow-sky-900/20 active:scale-[0.97] flex items-center justify-center gap-2">
                    Masuk Ke Sistem
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </button>
            </form>

            @if (Route::has('register'))
                <div class="mt-10 text-center pt-8 border-t border-slate-50">
                    <p class="text-xs font-bold text-slate-400">
                        Belum terdaftar? 
                        <a href="{{ route('register') }}" class="text-sky-600 hover:text-sky-700 underline decoration-2 underline-offset-4">Hubungi Admin IT</a>
                    </p>
                </div>
            @endif
        </div>
        
        <p class="mt-8 text-center text-[9px] font-bold text-slate-400 uppercase tracking-[0.4em]">
            Digital Assessment System &bull; Versi 5.0
        </p>
    </div>
</div>

</body>
</html>