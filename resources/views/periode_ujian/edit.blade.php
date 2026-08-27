@extends('layouts.app')

@section('title')
    Edit Periode Ujian
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-soft border-0">
                <div class="card-header bg-white py-4 px-4 border-0 d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle mr-3 d-flex align-items-center justify-content-center"
                         style="width: 42px; height: 42px;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="m-0 font-weight-bold text-dark">Edit Periode Ujian</h5>
                        <small class="text-muted">Ubah data periode ujian</small>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('periode_ujian.update', $periodeUjian->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="nama_periode" class="font-weight-bold">Nama Periode <span class="text-danger">*</span></label>
                            <input type="text" name="nama_periode" id="nama_periode" class="form-control" value="{{ old('nama_periode', $periodeUjian->nama_periode) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_mulai" class="font-weight-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', $periodeUjian->tanggal_mulai ? \Carbon\Carbon::parse($periodeUjian->tanggal_mulai)->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_selesai" class="font-weight-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', $periodeUjian->tanggal_selesai ? \Carbon\Carbon::parse($periodeUjian->tanggal_selesai)->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi" class="font-weight-bold">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control">{{ old('deskripsi', $periodeUjian->deskripsi) }}</textarea>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $periodeUjian->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label font-weight-bold" for="is_active">Aktifkan periode ini</label>
                        </div>

                        <div class="text-right border-top pt-4">
                            <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold shadow rounded-pill">
                                <i class="fas fa-save mr-2"></i> UPDATE
                            </button>
                            <a href="{{ route('periode_ujian.index') }}" class="btn btn-secondary px-4 py-2 rounded-pill">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection