@extends('layouts.app')
@section('title', 'Edit Jadwal Mata Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Jadwal Ujian</h1>
        <a href="{{ route('mapel.index') }}" class="btn btn-sm btn-secondary shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-arrow-left fa-sm mr-2"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
                <div class="card-header py-3 bg-white border-0">
                    <h6 class="m-0 font-weight-bold text-primary">Form Perubahan Jadwal: {{ $mapel->nama_mapel }}</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('mapel.update', $mapel->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold">Kode Mapel</label>
                                <input type="text" name="kode_mapel" 
                                       class="form-control @error('kode_mapel') is-invalid @enderror" 
                                       value="{{ old('kode_mapel', $mapel->kode_mapel) }}" required>
                                @error('kode_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold">Nama Mata Pelajaran</label>
                                <input type="text" name="nama_mapel" 
                                       class="form-control @error('nama_mapel') is-invalid @enderror" 
                                       value="{{ old('nama_mapel', $mapel->nama_mapel) }}" required>
                                @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold">Tanggal Ujian</label>
                                <input type="date" name="tanggal_ujian" 
                                       class="form-control @error('tanggal_ujian') is-invalid @enderror" 
                                       value="{{ old('tanggal_ujian', $mapel->tanggal_ujian) }}" required>
                                @error('tanggal_ujian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold">Jam Mulai</label>
                                <input type="time" name="jam_mulai" 
                                       class="form-control @error('jam_mulai') is-invalid @enderror" 
                                       value="{{ old('jam_mulai', $mapel->jam_mulai) }}" required>
                                @error('jam_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold">Jam Selesai</label>
                                <input type="time" name="jam_selesai" 
                                       class="form-control @error('jam_selesai') is-invalid @enderror" 
                                       value="{{ old('jam_selesai', $mapel->jam_selesai) }}" required>
                                @error('jam_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold">Durasi (HH:mm:ss)</label>
                            <input type="time" name="durasi" step="1" 
                                   class="form-control @error('durasi') is-invalid @enderror" 
                                   value="{{ old('durasi', $mapel->durasi) }}" required>
                            @error('durasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold">Tingkat</label>
                                <select name="tingkat_id" class="form-control @error('tingkat_id') is-invalid @enderror" required>
                                    <option value="">Pilih Tingkat</option>
                                    @foreach($tingkat as $level)
                                        <option value="{{ $level->id }}" {{ old('tingkat_id', $mapel->tingkat_id) == $level->id ? 'selected' : '' }}>
                                            {{ $level->nama_tingkat }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold">Kompetensi Keahlian</label>
                                <select name="kompetensi_keahlian_id" class="form-control @error('kompetensi_keahlian_id') is-invalid @enderror">
                                    <option value="">Pilih Kompetensi Keahlian</option>
                                    @foreach($kompetensi_keahlian as $keahlian)
                                        <option value="{{ $keahlian->id }}" {{ old('kompetensi_keahlian_id', $mapel->kompetensi_keahlian_id) == $keahlian->id ? 'selected' : '' }}>
                                            {{ $keahlian->nama_kompetensi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kompetensi_keahlian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Status Mata Pelajaran</label>
                            <div class="d-flex mt-2">
                                <div class="custom-control custom-radio mr-4">
                                    <input type="radio" id="statusAktif" name="status" value="aktif" 
                                        class="custom-control-input @error('status') is-invalid @enderror" 
                                        {{ old('status', $mapel->status) == 'aktif' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="statusAktif">Aktif</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="statusNonaktif" name="status" value="nonaktif" 
                                        class="custom-control-input @error('status') is-invalid @enderror" 
                                        {{ old('status', $mapel->status) == 'nonaktif' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="statusNonaktif">Nonaktif</label>
                                </div>
                            </div>
                            @error('status')
                                <span class="text-danger small font-weight-bold mt-1 d-block">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <hr>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary font-weight-bold px-5 shadow" style="border-radius: 10px;">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    @if($errors->any())
        Toast.fire({
            icon: 'error',
            title: 'Ada kesalahan pada inputan Anda'
        });
    @endif
</script>
@endpush