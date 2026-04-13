<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - CBT Mobile</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Reset tap highlight untuk mobile */
        * { -webkit-tap-highlight-color: transparent; }
        /* Hilangkan scrollbar default tapi tetap bisa scroll */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 pb-24">
    
    <main class="w-full">
        @yield('content')
    </main>

    <nav class="fixed bottom-0 left-0 right-0 h-20 bg-white border-t border-slate-200 flex items-center justify-around z-[100] px-4">
        <a href="{{ route('token.index') }}" class="flex flex-col items-center gap-1 {{ Route::is('token.index') ? 'text-indigo-600' : 'text-slate-400' }}">
            <i class="fa-solid fa-key text-xl"></i><span class="text-[10px] font-bold">Token</span>
        </a>
        <a href="{{ route('siswa.index') }}" class="flex flex-col items-center gap-1 {{ Route::is('siswa.index') ? 'text-indigo-600' : 'text-slate-400' }}">
            <i class="fa-solid fa-users text-xl"></i><span class="text-[10px] font-bold">Siswa</span>
        </a>
        <a href="{{ route('laporan.index') }}" class="flex flex-col items-center gap-1 {{ Route::is('laporan.index') ? 'text-indigo-600' : 'text-slate-400' }}">
            <i class="fa-solid fa-file-contract text-xl"></i><span class="text-[10px] font-bold">Laporan</span>
        </a>
        <button onclick="document.getElementById('logout-form').submit();" class="flex flex-col items-center gap-1 text-rose-500">
            <i class="fa-solid fa-right-from-bracket text-xl"></i><span class="text-[10px] font-bold">Logout</span>
        </button>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
    </nav>

    @stack('scripts')
</body>
</html>