@extends('layouts.mobile')
@section('title', 'Laporan Ujian')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 pb-24">
    <div class="bg-white px-5 pt-8 pb-5 border-b border-slate-200 shadow-sm sticky top-0 z-50">
        <div class="flex justify-between items-center mb-5">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Laporan Ujian</h1>
                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mt-1">
                    Hari ini, {{ date('d M Y') }}
                </p>
            </div>

            <button type="button"
                onclick="openModalExport()"
                class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200 active:scale-95 transition-transform">
                <i class="fas fa-file-excel fa-lg"></i>
            </button>
        </div>

        <div class="relative">
            <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 text-sm"></i>
            <input type="text"
                id="searchInput"
                placeholder="Cari nama siswa, NIS, atau kelas..."
                class="w-full h-12 bg-slate-100 border border-slate-200 rounded-2xl pl-11 pr-4 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white">
        </div>
    </div>

    <div class="px-5 pt-5 space-y-4" id="cardContainer">
        <div class="text-center py-10 text-slate-400 text-sm font-semibold">
            Memuat data...
        </div>
    </div>

    <div class="px-5 mt-5">
        <button type="button"
            id="loadMoreBtn"
            onclick="loadData(false)"
            class="hidden w-full h-12 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 active:scale-95 transition-transform">
            Muat Lagi
        </button>
    </div>
</div>

<div id="modalExport"
    class="fixed inset-0 bg-black/50 z-[999] hidden items-center justify-center px-5">
    <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden">
        <form action="{{ route('laporan.export') }}" method="GET">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                <h2 class="text-lg font-black text-slate-900">Download Excel</h2>
                <button type="button" onclick="closeModalExport()" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-5 space-y-4">
                <p class="text-xs text-slate-500 font-semibold">
                    Pilih mapel dan kelas untuk download nilai hari ini.
                </p>

                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2 mb-1 block">Mata Pelajaran</label>
                    <select name="mapel_id" required
                        class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-bold outline-none">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2 mb-1 block">Kelas</label>
                    <select name="kelas_id" required
                        class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-bold outline-none">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="p-5 border-t border-slate-100 flex gap-3">
                <button type="button" onclick="closeModalExport()"
                    class="flex-1 h-12 bg-slate-100 text-slate-600 rounded-2xl font-bold">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 h-12 bg-emerald-500 text-white rounded-2xl font-bold shadow-lg shadow-emerald-100">
                    Download
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let start = 0;
let length = 10;
let draw = 1;
let isLoading = false;
let hasMore = true;
let searchTimer = null;

function escapeHtml(text) {
    if (text === null || text === undefined) return '';

    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function statusClass(color) {
    if (color === 'success') return 'bg-emerald-100 text-emerald-600';
    if (color === 'danger') return 'bg-rose-100 text-rose-600';
    if (color === 'primary') return 'bg-indigo-100 text-indigo-600';
    if (color === 'warning') return 'bg-amber-100 text-amber-600';
    return 'bg-slate-100 text-slate-600';
}

function renderMapel(mapelList) {
    if (!mapelList || mapelList.length === 0) {
        return `<div class="text-xs text-slate-400 font-semibold">Belum ada mapel</div>`;
    }

    let html = '';

    mapelList.forEach(function (m) {
        let persen = m.total_soal > 0
            ? Math.round((m.dijawab / m.total_soal) * 100)
            : 0;

        html += `
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3 mb-2">
                <div class="flex justify-between items-start gap-2 mb-2">
                    <div class="font-black text-slate-800 text-sm leading-tight">
                        ${escapeHtml(m.nama_mapel)}
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase ${statusClass(m.status_color)}">
                        ${escapeHtml(m.status_label)}
                    </span>
                </div>

                <div class="flex justify-between text-[11px] text-slate-500 font-bold mb-2">
                    <span>Nilai: <b class="text-slate-800">${m.nilai}</b></span>
                    <span>Benar: <b class="text-emerald-600">${m.benar}</b></span>
                    <span>${m.dijawab}/${m.total_soal}</span>
                </div>

                <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full ${persen < 50 ? 'bg-rose-500' : 'bg-indigo-500'} rounded-full" style="width:${persen}%"></div>
                </div>
            </div>
        `;
    });

    return html;
}

function renderCard(data) {
    return `
        <div class="bg-white border border-slate-200 rounded-[2rem] p-5 shadow-sm active:scale-[0.98] transition-all">
            <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shrink-0 font-black text-xl">
                    ${escapeHtml(data.nama_siswa).charAt(0)}
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="text-slate-900 font-black text-base leading-tight">
                        ${escapeHtml(data.nama_siswa)}
                    </h3>

                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md uppercase">
                            ${escapeHtml(data.nama_kelas)}
                        </span>
                        <span class="text-[10px] font-semibold text-slate-400">
                            NIS: ${escapeHtml(data.nis)}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <span class="text-xs font-black text-slate-500 uppercase">
                    ${data.jumlah_mapel} Mapel
                </span>

                <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest ${statusClass(data.status_color)}">
                    ${escapeHtml(data.status_global)}
                </span>
            </div>

            <div>
                ${renderMapel(data.mapel_list)}
            </div>
        </div>
    `;
}

async function loadData(reset = false) {
    if (isLoading) return;

    if (reset) {
        start = 0;
        draw = 1;
        hasMore = true;
        document.getElementById('cardContainer').innerHTML = `
            <div class="text-center py-10 text-slate-400 text-sm font-semibold">
                Memuat data...
            </div>
        `;
    }

    if (!hasMore) return;

    isLoading = true;
    document.getElementById('loadMoreBtn').classList.add('hidden');

    let search = document.getElementById('searchInput').value || '';

    let params = new URLSearchParams();
    params.append('draw', draw);
    params.append('start', start);
    params.append('length', length);
    params.append('search[value]', search);
    params.append('order[0][column]', 0);
    params.append('order[0][dir]', 'asc');

    try {
        let response = await fetch(`{{ route('laporan.index') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        let json = await response.json();

        if (reset) {
            document.getElementById('cardContainer').innerHTML = '';
        }

        if (!json.data || json.data.length === 0) {
            if (start === 0) {
                document.getElementById('cardContainer').innerHTML = `
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-20 h-20 bg-slate-100 rounded-[2.5rem] flex items-center justify-center text-slate-300 mb-4">
                            <i class="fas fa-folder-open fa-2x"></i>
                        </div>
                        <h3 class="text-slate-900 font-bold">Tidak ada data</h3>
                        <p class="text-slate-400 text-xs px-10 mt-1">
                            Belum ada siswa yang mengerjakan ujian hari ini.
                        </p>
                    </div>
                `;
            }

            hasMore = false;
            return;
        }

        let html = '';
        json.data.forEach(function (item) {
            html += renderCard(item);
        });

        document.getElementById('cardContainer').insertAdjacentHTML('beforeend', html);

        start += json.data.length;
        draw++;

        hasMore = start < json.recordsFiltered;

        if (hasMore) {
            document.getElementById('loadMoreBtn').classList.remove('hidden');
        }

    } catch (e) {
        document.getElementById('cardContainer').innerHTML = `
            <div class="bg-rose-50 text-rose-600 p-4 rounded-2xl text-sm font-bold">
                Gagal memuat data laporan.
            </div>
        `;
    } finally {
        isLoading = false;
    }
}

function openModalExport() {
    document.getElementById('modalExport').classList.remove('hidden');
    document.getElementById('modalExport').classList.add('flex');
}

function closeModalExport() {
    document.getElementById('modalExport').classList.add('hidden');
    document.getElementById('modalExport').classList.remove('flex');
}

document.getElementById('searchInput').addEventListener('input', function () {
    clearTimeout(searchTimer);

    searchTimer = setTimeout(function () {
        loadData(true);
    }, 400);
});

document.addEventListener('DOMContentLoaded', function () {
    loadData(true);
});
</script>
@endsection