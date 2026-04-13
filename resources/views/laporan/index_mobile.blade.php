@extends('layouts.mobile')
@section('title', 'Laporan Ujian')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    /* Menghilangkan scrollbar pada filter horizontal */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 pb-24">
    <div class="bg-white px-5 pt-8 pb-6 border-b border-slate-200 shadow-sm sticky top-0 z-50">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Laporan Ujian</h1>
                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mt-1">Real-time Monitoring</p>
            </div>
            <a href="{{ route('laporan.export', request()->all()) }}" class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200 active:scale-95 transition-transform">
                <i class="fas fa-file-excel fa-lg"></i>
            </a>
        </div>

        <div class="bg-indigo-600 rounded-3xl p-4 flex items-center justify-between shadow-xl shadow-indigo-100">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <p class="text-indigo-100 text-[10px] font-bold uppercase tracking-tight">Total Peserta</p>
                    <p class="text-white text-xl font-black">{{ $results->count() }} <span class="text-xs font-normal opacity-80">Siswa</span></p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-indigo-100 text-[10px] font-bold uppercase tracking-tight">Hari Ini</p>
                <p class="text-white text-sm font-bold">{{ date('d M Y') }}</p>
            </div>
        </div>
    </div>

    <div class="px-5 py-4">
        <form action="{{ route('laporan.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div class="relative">
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2 mb-1 block">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal ?? date('Y-m-d') }}" 
                        class="w-full h-11 bg-white border border-slate-200 rounded-xl px-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm">
                </div>
                <div class="relative">
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2 mb-1 block">Kelas</label>
                    <select name="kelas_id" onchange="this.form.submit()"
                        class="w-full h-11 bg-white border border-slate-200 rounded-xl px-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm appearance-none">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="relative">
                <label class="text-[10px] font-bold text-slate-400 uppercase ml-2 mb-1 block">Mata Pelajaran</label>
                <select name="mapel_id" onchange="this.form.submit()"
                    class="w-full h-11 bg-white border border-slate-200 rounded-xl px-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm appearance-none">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach($mapel as $m)
                        <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div class="px-5 space-y-4">
        @forelse($results as $res)
        <div class="bg-white border border-slate-200 rounded-[2rem] p-5 shadow-sm active:scale-[0.98] transition-all overflow-hidden relative">
            <div class="absolute top-5 right-5 text-right">
                <span class="text-xs font-bold text-slate-400 block uppercase tracking-tighter mb-1">Nilai</span>
                <span class="text-2xl font-black {{ $res->nilai >= 75 ? 'text-indigo-600' : 'text-amber-500' }}">
                    {{ $res->nilai }}
                </span>
            </div>

            <div class="flex items-start gap-4 mb-5 pr-12">
                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-500 shrink-0 font-black text-xl">
                    {{ substr($res->nama_siswa, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <h3 class="text-slate-900 font-bold text-base truncate">{{ $res->nama_siswa }}</h3>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md uppercase tracking-tight">{{ $res->nama_kelas }}</span>
                        <span class="text-[10px] font-medium text-slate-400">NIS: {{ $res->nis }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 py-3 border-y border-slate-50 mb-4">
                <div class="flex-1">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Mata Pelajaran</p>
                    <p class="text-xs font-bold text-slate-700">{{ $res->nama_mapel }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider text-right">Waktu</p>
                    <p class="text-xs font-bold text-slate-700">{{ date('H:i', strtotime($res->tanggal_ujian)) }} WIB</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Progres Jawaban</span>
                        <span class="text-[10px] font-black text-indigo-600">{{ round(($res->dijawab / max($res->total_soal, 1)) * 100) }}%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        @php $persen = ($res->total_soal > 0) ? ($res->dijawab / $res->total_soal) * 100 : 0; @endphp
                        <div class="h-full {{ $persen < 50 ? 'bg-rose-500' : 'bg-indigo-500' }} rounded-full" style="width: {{ $persen }}%"></div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-2">
                    <div class="flex gap-4">
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-xs font-bold text-slate-600">{{ $res->benar }} <span class="text-[10px] font-medium text-slate-400">Benar</span></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                            <span class="text-xs font-bold text-slate-600">{{ $res->total_soal - $res->benar }} <span class="text-[10px] font-medium text-slate-400">Salah</span></span>
                        </div>
                    </div>
                    
                    <span class="px-4 py-1.5 bg-{{ $res->status_color == 'success' ? 'emerald' : ($res->status_color == 'danger' ? 'rose' : 'slate') }}-100 text-{{ $res->status_color == 'success' ? 'emerald' : ($res->status_color == 'danger' ? 'rose' : 'slate') }}-600 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm">
                        {{ $res->status_label }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-[2.5rem] flex items-center justify-center text-slate-300 mb-4">
                <i class="fas fa-folder-open fa-2x"></i>
            </div>
            <h3 class="text-slate-900 font-bold">Tidak ada data</h3>
            <p class="text-slate-400 text-xs px-10 mt-1">Gunakan filter di atas untuk melihat data pada tanggal atau kelas lain.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection