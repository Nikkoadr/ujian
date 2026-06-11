@extends('layouts.app')
@section('title', 'Handler Peserta Ujian')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
                Handler Peserta Ujian
            </h1>
            <p class="text-muted small mb-0">
                Kelola status dan waktu mulai ujian peserta secara massal.
            </p>
        </div>
    </div>

    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i> Filter Peserta
            </h6>
        </div>

        <div class="card-body">
            <form id="formFilter" class="row">
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold">Kelas</label>
                    <select name="kelas_id" class="form-control">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold">Mata Pelajaran</label>
                    <select name="mapel_id" class="form-control">
                        <option value="">-- Semua Mapel --</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold">Status</label>
                    <select name="status" class="form-control">
                        <option value="">-- Semua Status --</option>
                        <option value="sedang mengerjakan">Sedang Mengerjakan</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <div class="col-md-3 mb-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                        <i class="fas fa-search mr-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users-cog mr-2"></i> Data Peserta
            </h6>

            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-warning font-weight-bold" data-toggle="modal" data-target="#modalWaktu">
                    <i class="fas fa-clock mr-1"></i> Ubah Waktu
                </button>

                <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#modalStatus">
                    <i class="fas fa-sync-alt mr-1"></i> Ubah Status
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="alert alert-info small">
                Centang peserta yang ingin diubah, lalu pilih aksi di kanan atas.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="handlerTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" width="5%">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th>Identitas Siswa</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Pelanggaran</th>
                            <th class="text-center">Mulai Ujian</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalStatus" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold">Ubah Status Massal</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <label class="small font-weight-bold">Status Baru</label>
                <select id="statusBaru" class="form-control">
                    <option value="sedang mengerjakan">Sedang Mengerjakan</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-success" onclick="submitStatus()">
                    Simpan Status
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalWaktu" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title font-weight-bold">Ubah Waktu Mulai Massal</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <label class="small font-weight-bold">Waktu Mulai Ujian Baru</label>
                <input type="datetime-local" id="mulaiUjianBaru" class="form-control">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-warning" onclick="submitWaktu()">
                    Simpan Waktu
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    let table = $('#handlerTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 50,
        lengthMenu: [[25, 50, 100, 200], [25, 50, 100, 200]],
        ordering: true,
        searching: true,
        paging: true,
        info: true,
        order: [[1, 'asc']],
        ajax: {
            url: "{{ route('ujian-handler.index') }}",
            data: function (d) {
                d.kelas_id = $('select[name="kelas_id"]').val();
                d.mapel_id = $('select[name="mapel_id"]').val();
                d.status = $('select[name="status"]').val();
            }
        },
        columns: [
            {
                data: 'id',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (id) {
                    return `<input type="checkbox" class="checkItem" value="${id}">`;
                }
            },
            {
                data: null,
                render: function (data) {
                    return `
                        <div class="font-weight-bold text-gray-800">${data.nama_siswa}</div>
                        <div class="small text-muted">NIS: ${data.nis}</div>
                    `;
                }
            },
            {
                data: 'nama_kelas',
                render: function (data) {
                    return `<span class="badge badge-light border text-primary">${data}</span>`;
                }
            },
            {
                data: 'nama_mapel',
                render: function (data) {
                    return `<div class="font-weight-bold">${data}</div>`;
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function (status) {
                    if (status === 'selesai') {
                        return `<span class="badge badge-success px-3 py-2">SELESAI</span>`;
                    }

                    return `<span class="badge badge-primary px-3 py-2">MENGERJAKAN</span>`;
                }
            },
            {
                data: 'pelanggaran',
                className: 'text-center',
                render: function (data) {
                    return `<span class="badge badge-danger px-3 py-2">${data}</span>`;
                }
            },
            {
                data: 'mulai_ujian',
                className: 'text-center small'
            }
        ],
        language: {
            processing: "Memuat data...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ peserta",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total peserta)",
            zeroRecords: "Tidak ada peserta yang cocok",
            emptyTable: "Tidak ada peserta ditemukan.",
            paginate: {
                first: "Pertama",
                previous: "Sebelumnya",
                next: "Berikutnya",
                last: "Terakhir"
            }
        }
    });

    $('#formFilter').on('submit', function (e) {
        e.preventDefault();
        $('#checkAll').prop('checked', false);
        table.ajax.reload();
    });

    $('#checkAll').on('change', function () {
        $('.checkItem').prop('checked', this.checked);
    });

    $('#handlerTable').on('draw.dt', function () {
        $('#checkAll').prop('checked', false);
    });
});

function getSelectedIds() {
    let ids = [];

    $('.checkItem:checked').each(function () {
        ids.push($(this).val());
    });

    return ids;
}

function submitStatus() {
    let ids = getSelectedIds();

    if (ids.length === 0) {
        Swal.fire('Pilih Peserta', 'Centang minimal satu peserta terlebih dahulu.', 'warning');
        return;
    }

    let status = $('#statusBaru').val();

    Swal.fire({
        title: 'Ubah status?',
        text: ids.length + ' peserta akan diubah statusnya.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, ubah',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#1cc88a'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('ujian-handler.update-status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids,
                    status: status
                },
                success: function (res) {
                    $('#modalStatus').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    });

                    $('#handlerTable').DataTable().ajax.reload(null, false);
                    $('#checkAll').prop('checked', false);
                },
                error: function () {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengubah status.', 'error');
                }
            });
        }
    });
}

function submitWaktu() {
    let ids = getSelectedIds();

    if (ids.length === 0) {
        Swal.fire('Pilih Peserta', 'Centang minimal satu peserta terlebih dahulu.', 'warning');
        return;
    }

    let mulaiUjian = $('#mulaiUjianBaru').val();

    if (!mulaiUjian) {
        Swal.fire('Waktu Kosong', 'Silakan isi waktu mulai ujian baru.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Ubah waktu mulai?',
        text: ids.length + ' peserta akan diubah waktu mulai ujiannya.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, ubah',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#f6c23e'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('ujian-handler.update-waktu') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids,
                    mulai_ujian: mulaiUjian
                },
                success: function (res) {
                    $('#modalWaktu').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    });

                    $('#handlerTable').DataTable().ajax.reload(null, false);
                    $('#checkAll').prop('checked', false);
                },
                error: function () {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengubah waktu mulai ujian.', 'error');
                }
            });
        }
    });
}
</script>
@endpush
@endsection