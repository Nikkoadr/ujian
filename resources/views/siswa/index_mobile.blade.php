@extends('layouts.mobile')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    /* DataTables Clean-up */
    thead { display: none; }
    table.dataTable.no-footer { border-bottom: none !important; }
    
    /* Search Box styling */
    .dataTables_filter { width: 100%; padding: 0 1.25rem; margin-top: 1.25rem; }
    .dataTables_filter input {
        width: 100% !important; height: 3.25rem; border-radius: 1rem !important;
        padding-left: 3rem !important; border: 1.5px solid #e2e8f0 !important;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'%3E%3C/path%3E%3C/svg%3E") no-repeat 1.1rem center / 1.25rem !important;
    }

    /* Pagination Styling */
    .dataTables_paginate { display: flex; justify-content: center; padding: 1.5rem 0; }
    .pagination { display: flex !important; gap: 0.4rem; flex-wrap: wrap; justify-content: center; }
    .page-item .page-link {
        border-radius: 0.75rem !important; border: 1px solid #e2e8f0 !important;
        color: #475569 !important; font-weight: 700; padding: 0.6rem 1rem !important;
    }
    .page-item.active .page-link { background-color: #4f46e5 !important; border-color: #4f46e5 !important; color: white !important; }
</style>
@endpush

@section('content')
<div class="flex flex-col min-h-screen">
    <div class="sticky top-0 z-[90] bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm px-5 py-6">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-4">Monitoring Siswa</h1>
        
        <div class="flex gap-2">
            <select id="filterStatus" class="flex-1 h-12 rounded-2xl border-slate-200 bg-slate-50 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none transition-all px-3">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="diblokir">Terblokir</option>
            </select>
            <select id="filterKelas" class="flex-1 h-12 rounded-2xl border-slate-200 bg-slate-50 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none transition-all px-3">
                <option value="">Semua Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="px-5 py-2">
        <table id="siswaTable" class="w-full border-separate border-spacing-y-3">
            <thead><tr><th>Data</th></tr></thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    let table = $('#siswaTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        pageLength: 10,
        ajax: {
            url: "{{ route('siswa.index') }}",
            data: function(d) {
                d.filterStatus = $('#filterStatus').val();
                d.filterKelas = $('#filterKelas').val();
            }
        },
        columns: [
            {
                data: 'user.nama',
                name: 'user.nama',
                orderable: false,
                render: function(data, type, row) {
                    const isAktif = (row.user.status === 'aktif');
                    const colorClass = isAktif ? 'text-emerald-500 bg-emerald-50' : 'text-rose-500 bg-rose-50';
                    const icon = isAktif ? 'fa-user-check' : 'fa-user-slash';

                    return `
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 flex items-center justify-between shadow-sm active:scale-[0.98] transition-all">
                            <div class="flex items-center gap-4 overflow-hidden">
                                <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-indigo-100 shrink-0">
                                    ${row.user.nama.charAt(0).toUpperCase()}
                                </div>
                                <div class="flex flex-col truncate">
                                    <span class="text-slate-900 font-bold text-[15px] truncate">${row.user.nama}</span>
                                    <span class="text-slate-500 text-xs font-semibold mt-0.5">
                                        <i class="fa-solid fa-graduation-cap text-[10px] opacity-70"></i> ${row.kelas.nama_kelas}
                                    </span>
                                    <span class="text-slate-400 text-[10px] font-mono mt-0.5">NIS: ${row.nis}</span>
                                </div>
                            </div>
                            <button onclick="confirmToggle(${row.id})" class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 ${colorClass}">
                                <i class="fa-solid ${icon} fa-lg"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        dom: 'frtp',
        language: { 
            search: "", 
            searchPlaceholder: "Cari nama siswa...",
            processing: "Memuat...",
            paginate: { previous: "‹", next: "›" }
        }
    });

    $('#filterStatus, #filterKelas').on('change', () => table.ajax.reload());
});

function confirmToggle(id) {
    Swal.fire({
        title: 'Ubah Akses?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal',
        heightAuto: false,
        customClass: { confirmButton: 'rounded-xl font-bold', cancelButton: 'rounded-xl font-bold' }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(`/siswa/toggle/${id}`, () => {
                $('#siswaTable').DataTable().ajax.reload(null, false);
                Swal.fire({ toast: true, position: 'top', icon: 'success', title: 'Berhasil', showConfirmButton: false, timer: 1500 });
            });
        }
    });
}
</script>
@endpush