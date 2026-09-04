@extends('layouts.app')

@section('title', 'Kelola Paket Soal')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Kelola Paket Soal</h1>
        <a href="{{ route('jadwal-ujian.index') }}" class="btn btn-sm btn-secondary shadow-sm" style="border-radius: 10px; padding: 0.4rem 1rem;">
            <i class="fas fa-arrow-left fa-sm mr-2"></i> Kembali
        </a>
    </div>

    <div class="card shadow border-0" style="border-radius: 15px;">
        <div class="card-body">
            <form action="{{ route('soal.sync', $soal) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Informasi Paket Soal (disabled) --}}
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Periode Ujian</label>
                        <input type="text" class="form-control" value="{{ $soal->PeriodeUjian->nama_periode ?? '-' }}" disabled>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Jadwal Ujian</label>
                        <input type="text" class="form-control" value="{{ $soal->jadwal->tanggal_ujian ?? '-' }} ({{ $soal->jadwal->jam_mulai ?? '' }} - {{ $soal->jadwal->jam_selesai ?? '' }})" disabled>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Mata Pelajaran</label>
                        <input type="text" class="form-control" value="{{ $soal->mapel->nama_mapel ?? '-' }}" disabled>
                    </div>
                </div>

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
                                @forelse($bankPertanyaan as $bp)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="pertanyaan-checkbox" name="pertanyaan_ids[]" value="{{ $bp->id }}" {{ in_array($bp->id, $selectedPertanyaan) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $bp->pertanyaan }}</td>
                                    <td>{{ $bp->jenis_soal }}</td>
                                    <td>{{ $bp->bobot_nilai }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada pertanyaan untuk mapel ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div id="selectedCount" class="mt-2 text-muted">
                        @php
                            $count = count($selectedPertanyaan);
                        @endphp
                        @if($count > 0)
                            <span class="text-success"><i class="fas fa-check-circle"></i> {{ $count }} pertanyaan dipilih</span>
                        @else
                            Belum ada pertanyaan dipilih
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 shadow" style="border-radius: 10px;">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
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
        // Fungsi update jumlah terpilih
        function updateSelectedCount() {
            var count = $('.pertanyaan-checkbox:checked').length;
            if (count > 0) {
                $('#selectedCount').html('<span class="text-success"><i class="fas fa-check-circle"></i> ' + count + ' pertanyaan dipilih</span>');
            } else {
                $('#selectedCount').text('Belum ada pertanyaan dipilih');
            }
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

        // Inisialisasi count
        updateSelectedCount();
    });
</script>
@endpush