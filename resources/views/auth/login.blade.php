<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Masuk Aplikasi | CBT MUHKA</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb', /* Royal Blue */
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
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.05) 0px, transparent 50%);
        }
        .login-card { 
            background: #ffffff;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.03), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        }
    </style>
</head>
<body class="h-full antialiased text-slate-600 overflow-x-hidden">

<div class="min-h-screen flex flex-col items-center justify-center px-6 py-12 relative">
    
    <div class="mb-10 flex flex-col items-center gap-4 relative z-10">
        <div class="h-16 w-16 bg-white rounded-2xl shadow-sm flex items-center justify-center border border-slate-100 transition-all hover:border-primary/20">
            <img src="{{ asset('assets/img/logo.png') }}" class="w-10 h-10 object-contain" alt="Logo">
        </div>
        <div class="text-center">
            <h1 class="text-xl font-bold tracking-tight text-slate-900">
                CBT <span class="text-primary">SMKMUHKDH</span>
            </h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">SMK Muhammadiyah Kandanghaur</p>
        </div>
    </div>

    <div class="w-full max-w-[400px] relative z-10">
        <div class="login-card p-8 sm:p-10 rounded-[2.5rem] border border-slate-100">
            
            <div class="mb-8">
                <h2 class="text-xl font-bold text-slate-900">Selamat Datang</h2>
                <p class="text-slate-400 text-sm mt-1">Silakan masuk dengan akun siswa anda</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-2 ml-1 italic">Email</label>
                    <input type="email" name="email" required autofocus
                        class="w-full px-5 py-3.5 bg-slate-50/50 border border-slate-200 rounded-2xl focus:outline-none focus:border-primary focus:bg-white transition-all text-sm font-medium text-slate-900 placeholder-slate-300"
                        placeholder="1234@smkmuhkandanghaur.sch.id">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-[11px] font-bold text-slate-500 italic">Kata Sandi</label>
                        <a href="#" class="text-[11px] font-bold text-primary hover:underline">Lupa?</a>
                    </div>
                    <input type="password" name="password" required
                        class="w-full px-5 py-3.5 bg-slate-50/50 border border-slate-200 rounded-2xl focus:outline-none focus:border-primary focus:bg-white transition-all text-sm font-medium text-slate-900 placeholder-slate-300"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center gap-2 px-1">
                    <input type="checkbox" id="remember" class="w-4 h-4 rounded-md border-slate-300 text-primary focus:ring-primary/20 cursor-pointer">
                    <label for="remember" class="text-xs font-medium text-slate-500 cursor-pointer select-none">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" 
                    class="w-full bg-primary text-white py-4 rounded-2xl font-bold text-sm hover:bg-blue-700 transition-all active:scale-[0.98] shadow-lg shadow-primary/20 mt-2 flex items-center justify-center gap-2 group">
                    Masuk Ke Akun
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </button>
            </form>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">
                &copy; 2026 Digital Assessment System
            </p>
        </div>
    </div>
</div>

</body>
</html>