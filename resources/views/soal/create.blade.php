@extends('layouts.app')

@section('title', 'Tambah Paket Soal')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Tambah Paket Soal</h1>
        <a href="{{ route('jadwal-ujian.index') }}" class="btn btn-sm btn-secondary shadow-sm" style="border-radius: 10px; padding: 0.4rem 1rem;">
            <i class="fas fa-arrow-left fa-sm mr-2"></i> Kembali
        </a>
    </div>

    <div class="card shadow border-0" style="border-radius: 15px;">
        <div class="card-body">
            <form action="{{ route('soal.store') }}" method="POST">
                @csrf

                {{-- Cek apakah ada parameter jadwal_id dan mapel_id --}}
                @php
                    $hasJadwal = isset($jadwalId) && !empty($jadwalId);
                    $hasMapel  = isset($mapelId) && !empty($mapelId);
                    // Cari data jadwal yang dipilih untuk mengambil periode_id
                    $selectedJadwal = $hasJadwal ? $jadwal->firstWhere('id', $jadwalId) : null;
                    $periodeIdFromJadwal = $selectedJadwal ? $selectedJadwal->periode_ujian_id : null;
                @endphp

                <div class="row">
                    {{-- PERIODE UJIAN --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Periode Ujian</label>
                        @if($hasJadwal)
                            {{-- Jika ada jadwal_id, periode disabled dan diisi otomatis --}}
                            <select class="form-control" disabled>
                                @foreach($periodeUjian as $p)
                                    <option value="{{ $p->id }}" {{ $periodeIdFromJadwal == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_periode }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="periode_ujian_id" value="{{ $periodeIdFromJadwal }}">
                            <small class="text-muted text-success">✓ Periode otomatis sesuai jadwal.</small>
                        @else
                            <select name="periode_ujian_id" id="periode_ujian_id" class="form-control @error('periode_ujian_id') is-invalid @enderror" required>
                                <option value="">Pilih Periode</option>
                                @foreach($periodeUjian as $p)
                                    <option value="{{ $p->id }}" {{ old('periode_ujian_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_periode }}
                                    </option>
                                @endforeach
                            </select>
                            @error('periode_ujian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- JADWAL UJIAN --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Jadwal Ujian</label>
                        @if($hasJadwal)
                            <select class="form-control" disabled>
                                @foreach($jadwal as $j)
                                    <option value="{{ $j->id }}" {{ $jadwalId == $j->id ? 'selected' : '' }}>
                                        {{ $j->tanggal_ujian }} {{ $j->jam_mulai }} - {{ $j->jam_selesai }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="jadwal_id" value="{{ $jadwalId }}">
                            <small class="text-muted text-success">✓ Jadwal sudah terpilih dari halaman sebelumnya.</small>
                        @else
                            <select name="jadwal_id" id="jadwal_id" class="form-control @error('jadwal_id') is-invalid @enderror" required>
                                <option value="">Pilih Jadwal</option>
                                @foreach($jadwal as $j)
                                    <option value="{{ $j->id }}" {{ old('jadwal_id') == $j->id ? 'selected' : '' }} data-periode="{{ $j->periode_ujian_id }}">
                                        {{ $j->tanggal_ujian }} {{ $j->jam_mulai }} - {{ $j->jam_selesai }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jadwal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @endif
                    </div>
                </div>

                {{-- MATA PELAJARAN --}}
                <div class="form-group">
                    <label class="font-weight-bold">Mata Pelajaran</label>
                    @if($hasMapel)
                        <select id="mapel_id" class="form-control" disabled>
                            @foreach($mapel as $m)
                                <option value="{{ $m->id }}" {{ $mapelId == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="mapel_id" id="hidden_mapel_id" value="{{ $mapelId }}">
                        <small class="text-muted text-success">✓ Mapel sudah terpilih dari halaman sebelumnya.</small>
                    @else
                        <select name="mapel_id" id="mapel_id" class="form-control @error('mapel_id') is-invalid @enderror" required>
                            <option value="">Pilih Mapel</option>
                            @foreach($mapel as $m)
                                <option value="{{ $m->id }}" {{ old('mapel_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                        @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @endif
                </div>

                {{-- DAFTAR PERTANYAAN (DataTable AJAX) --}}
                <div class="form-group">
                    <label class="font-weight-bold">Pilih Pertanyaan (centang yang diinginkan)</label>
                    <div class="table-responsive">
                        <table id="tabelPertanyaan" class="table table-bordered table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th width="50"><input type="checkbox" id="checkAll"></th>
                                    <th>Pertanyaan</th>
                                    <th>Jenis</th>
                                    <th>Bobot</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data diisi via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="selectedCount" class="mt-2 text-muted">Belum ada pertanyaan dipilih</div>
                    @error('pertanyaan_ids') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 shadow" style="border-radius: 10px;">
                        <i class="fas fa-save mr-2"></i> Simpan Paket Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table;

        // Fungsi update periode saat jadwal berubah (hanya jika tidak disabled)
        function updatePeriode() {
            var selected = $('#jadwal_id').val();
            if (selected) {
                var periodeId = $('#jadwal_id option:selected').data('periode');
                if (periodeId) {
                    $('#periode_ujian_id').val(periodeId);
                }
            } else {
                $('#periode_ujian_id').val('');
            }
        }

        // Fungsi load pertanyaan via AJAX DataTable
        function loadPertanyaan(mapelId) {
            if ($.fn.DataTable.isDataTable('#tabelPertanyaan')) {
                table.destroy();
                $('#tabelPertanyaan tbody').empty();
            }

            if (!mapelId) {
                $('#tabelPertanyaan tbody').html('<tr><td colspan="4" class="text-center">Silakan pilih mata pelajaran terlebih dahulu.</td></tr>');
                $('#selectedCount').text('Belum ada pertanyaan dipilih');
                return;
            }

            table = $('#tabelPertanyaan').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ url("get-pertanyaan") }}/' + mapelId,
                    dataSrc: function(data) {
                        return data;
                    },
                    error: function() {
                        $('#tabelPertanyaan tbody').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data.</td></tr>');
                    }
                },
                columns: [
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="pertanyaan-checkbox" name="pertanyaan_ids[]" value="' + row.id + '">';
                        },
                        orderable: false,
                        searchable: false
                    },
                    { data: 'pertanyaan' },
                    { data: 'jenis_soal' },
                    { data: 'bobot_nilai' }
                ],
                language: {
                    processing: "<div class='spinner-border text-primary'><span class='sr-only'>Loading...</span></div>",
                    emptyTable: "Tidak ada pertanyaan untuk mapel ini.",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pertanyaan"
                },
                drawCallback: function() {
                    updateSelectedCount();
                    $('#checkAll').prop('checked', false);
                }
            });
        }

        function updateSelectedCount() {
            var count = $('#tabelPertanyaan .pertanyaan-checkbox:checked').length;
            if (count > 0) {
                $('#selectedCount').html('<span class="text-success"><i class="fas fa-check-circle"></i> ' + count + ' pertanyaan dipilih</span>');
            } else {
                $('#selectedCount').text('Belum ada pertanyaan dipilih');
            }
        }

        // Event: saat jadwal berubah (hanya jika tidak disabled)
        $('#jadwal_id').on('change', function() {
            if (!$(this).prop('disabled')) {
                updatePeriode();
            }
        });

        // Event: saat mapel berubah
        $('#mapel_id').on('change', function() {
            var mapelId = $(this).val();
            if ($(this).prop('disabled')) {
                mapelId = $('#hidden_mapel_id').val();
            }
            loadPertanyaan(mapelId);
        });

        // Inisialisasi awal
        var initialMapelId = '{{ $mapelId }}';
        if (initialMapelId) {
            if (!$('#mapel_id').prop('disabled')) {
                $('#mapel_id').val(initialMapelId);
            }
            loadPertanyaan(initialMapelId);
        } else {
            $('#tabelPertanyaan tbody').html('<tr><td colspan="4" class="text-center">Silakan pilih mata pelajaran terlebih dahulu.</td></tr>');
        }

        // Jika jadwal tidak disabled dan ada value, update periode
        if (!$('#jadwal_id').prop('disabled') && $('#jadwal_id').val()) {
            updatePeriode();
        }

        // Check All
        $('#checkAll').on('change', function() {
            $('.pertanyaan-checkbox').prop('checked', $(this).prop('checked'));
            updateSelectedCount();
        });

        // Update count saat checkbox berubah
        $(document).on('change', '.pertanyaan-checkbox', function() {
            updateSelectedCount();
            var total = $('.pertanyaan-checkbox').length;
            var checked = $('.pertanyaan-checkbox:checked').length;
            $('#checkAll').prop('checked', total > 0 && total === checked);
        });
    });
</script>
@endpush