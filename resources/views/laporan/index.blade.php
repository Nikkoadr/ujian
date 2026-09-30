@extends('layouts.app')
@section('title', 'Laporan Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Laporan Hasil Ujian</h1>
            <p class="text-muted small mb-0" id="laporanSubtitle">
                Menampilkan siswa dan mapel yang dikerjakan pada tanggal {{ date('d-m-Y') }}.
            </p>
        </div>

        <button type="button" class="btn btn-sm btn-info shadow-sm mr-2" data-toggle="modal" data-target="#modalRekapPdf">
            <i class="fas fa-file-pdf fa-sm text-white-50 mr-1"></i> Rekap PDF
        </button>
        <button type="button" class="btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalExportExcel">
            <i class="fas fa-file-excel fa-sm text-white-50 mr-1"></i> Download Excel
        </button>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <select id="filterPeriode" class="form-control font-weight-bold" style="border-radius: 10px;">
                        <option value="">Hari Ini ({{ date('d-m-Y') }})</option>
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}">Periode: {{ $p->nama_periode }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 25%;">Identitas Siswa</th>
                            <th style="width: 45%;">Mapel Dikerjakan Hari Ini</th>
                            <th class="text-center" style="width: 15%;">Jumlah Mapel</th>
                            <th class="text-center" style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExportExcel" tabindex="-1" role="dialog" aria-labelledby="modalExportExcelLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('laporan.export') }}" method="GET" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalExportExcelLabel">
                    Download Nilai Excel
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="small text-muted">
                    Pilih periode, mata pelajaran, dan kelas yang ingin diunduh nilainya.
                </p>

                <div class="form-group">
                    <label class="small font-weight-bold">Periode Ujian</label>
                    <select name="periode_ujian_id" id="exportPeriode" class="form-control" required>
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}" {{ $p->is_active ? 'selected' : '' }}>{{ $p->nama_periode }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold">Mata Pelajaran</label>
                    <select name="mapel_id" id="exportMapel" class="form-control" required>
                        <option value="">-- Pilih Periode Dulu --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold">Kelas</label>
                    <select name="kelas_id" id="exportKelas" class="form-control" required>
                        <option value="">-- Pilih Mapel Dulu --</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Batal
                </button>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-download mr-1"></i> Download
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalRekapPdf" tabindex="-1" role="dialog" aria-labelledby="modalRekapPdfLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="GET" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalRekapPdfLabel">
                    Rekap Nilai Silang
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="small text-muted">
                    Baris = siswa sekelas, kolom = tiap mapel berisi nilai. Pilih periode dan kelas.
                </p>

                <div class="form-group">
                    <label class="small font-weight-bold">Periode Ujian</label>
                    <select name="periode_ujian_id" id="rekapPeriode" class="form-control" required>
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}" {{ $p->is_active ? 'selected' : '' }}>{{ $p->nama_periode }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold">Kelas</label>
                    <select name="kelas_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Batal
                </button>

                <button type="submit" class="btn btn-info" formaction="{{ route('laporan.rekap') }}" formtarget="_blank">
                    <i class="fas fa-eye mr-1"></i> Pratinjau
                </button>

                <button type="submit" class="btn btn-danger" formaction="{{ route('laporan.rekap-pdf') }}">
                    <i class="fas fa-download mr-1"></i> Download PDF
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .mapel-box {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 8px 10px;
        margin-bottom: 6px;
        background: #fff;
    }

    .mapel-box:last-child {
        margin-bottom: 0;
    }

    .progress-sm {
        height: 8px;
    }
</style>

@push('scripts')
<script>
$(document).ready(function () {
    function escapeHtml(text) {
        if (text === null || text === undefined) {
            return '';
        }

        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    $('#dataTable').DataTable({
        processing: true,
        serverSide: true,

        paging: true,
        ordering: true,
        searching: true,
        info: true,
        lengthChange: true,

        pageLength: 50,

        lengthMenu: [
            [25, 50, 100, 200],
            [25, 50, 100, 200]
        ],

        order: [[0, 'asc']],

        ajax: {
            url: "{{ route('laporan.index') }}",
            type: "GET",
            data: function (d) {
                d.periode_ujian_id = $('#filterPeriode').val();
            }
        },

        columns: [
            {
                data: null,
                name: 'nama_siswa',
                orderable: true,
                searchable: true,
                render: function (data) {
                    return `
                        <div class="font-weight-bold text-gray-800">
                            ${escapeHtml(data.nama_siswa)}
                        </div>

                        <div class="small text-muted">
                            NIS: ${escapeHtml(data.nis)}
                        </div>

                        <div class="badge badge-light border text-primary">
                            ${escapeHtml(data.nama_kelas)}
                        </div>
                    `;
                }
            },
            {
                data: 'mapel_list',
                name: 'mapel_list',
                orderable: false,
                searchable: false,
                render: function (mapelList) {
                    if (!mapelList || mapelList.length === 0) {
                        return `<span class="text-muted small">Belum ada mapel</span>`;
                    }

                    let html = '';

                    mapelList.forEach(function (m) {
                        let persen = m.total_soal > 0
                            ? Math.round((m.dijawab / m.total_soal) * 100)
                            : 0;

                        html += `
                            <div class="mapel-box">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="font-weight-bold text-gray-800">
                                        ${escapeHtml(m.nama_mapel)}
                                    </div>

                                    <span class="badge badge-${m.status_color}">
                                        ${escapeHtml(m.status_label)}
                                    </span>
                                </div>

                                <div class="small text-muted mb-1">
                                    Nilai: <b>${m.nilai}</b> |
                                    Benar: <b>${m.benar}</b> |
                                    Terisi: <b>${m.dijawab}/${m.total_soal}</b>
                                </div>

                                <div class="progress progress-sm">
                                    <div class="progress-bar ${persen < 50 ? 'bg-danger' : 'bg-info'}" style="width:${persen}%"></div>
                                </div>
                            </div>
                        `;
                    });

                    return html;
                }
            },
            {
                data: 'jumlah_mapel',
                name: 'jumlah_mapel',
                className: 'text-center',
                orderable: true,
                searchable: false,
                render: function (data) {
                    return `
                        <span class="badge badge-primary px-3 py-2">
                            ${data} Mapel
                        </span>
                    `;
                }
            },
            {
                data: null,
                name: 'status_global',
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function (data) {
                    return `
                        <span class="badge badge-${data.status_color} px-3 py-2 text-uppercase shadow-sm" style="font-size:10px;min-width:100px;">
                            <i class="fas fa-info-circle mr-1"></i>
                            ${escapeHtml(data.status_global)}
                        </span>
                    `;
                }
            }
        ],

        language: {
            processing: "Memuat data...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total siswa)",
            zeroRecords: "Tidak ada data yang cocok",
            emptyTable: "Tidak ada siswa yang mengerjakan ujian hari ini.",
            paginate: {
                first: "Pertama",
                previous: "Sebelumnya",
                next: "Berikutnya",
                last: "Terakhir"
            }
        }
    });

    // Dropdown berantai: Periode -> Mapel (yang ada jadwalnya) -> Kelas (yang relevan).
    function loadExportMapel(periodeId) {
        const $mapel = $('#exportMapel');
        const $kelas = $('#exportKelas');
        $mapel.html('<option value="">Memuat mapel...</option>');
        $kelas.html('<option value="">-- Pilih Mapel Dulu --</option>');

        if (!periodeId) {
            $mapel.html('<option value="">-- Pilih Periode Dulu --</option>');
            return;
        }

        $.getJSON("{{ route('laporan.mapel') }}", { periode_ujian_id: periodeId })
            .done(function (list) {
                let html = '<option value="">-- Pilih Mata Pelajaran --</option>';
                list.forEach(function (m) {
                    html += `<option value="${m.id}">${m.nama_mapel}</option>`;
                });
                $mapel.html(html);
            })
            .fail(function () {
                $mapel.html('<option value="">Gagal memuat mapel</option>');
            });
    }

    function loadExportKelas(mapelId) {
        const $kelas = $('#exportKelas');
        $kelas.html('<option value="">Memuat kelas...</option>');

        if (!mapelId) {
            $kelas.html('<option value="">-- Pilih Mapel Dulu --</option>');
            return;
        }

        $.getJSON("{{ route('laporan.kelas') }}", { mapel_id: mapelId })
            .done(function (list) {
                let html = '<option value="">-- Pilih Kelas --</option>';
                list.forEach(function (k) {
                    html += `<option value="${k.id}">${k.nama_kelas}</option>`;
                });
                $kelas.html(html);
            })
            .fail(function () {
                $kelas.html('<option value="">Gagal memuat kelas</option>');
            });
    }

    $('#filterPeriode').on('change', function () {
        const nama = $(this).find('option:selected').text();
        if ($(this).val()) {
            $('#laporanSubtitle').text('Menampilkan riwayat laporan pada ' + nama + '.');
        } else {
            $('#laporanSubtitle').text('Menampilkan siswa dan mapel yang dikerjakan pada tanggal {{ date('d-m-Y') }}.');
        }
        $('#dataTable').DataTable().ajax.reload();
    });

    $('#exportPeriode').on('change', function () {
        loadExportMapel($(this).val());
    });

    $('#exportMapel').on('change', function () {
        loadExportKelas($(this).val());
    });

    // Isi awal mengikuti periode terpilih (default: yang aktif).
    loadExportMapel($('#exportPeriode').val());
});
</script>
@endpush
@endsection