@extends('layouts.app')

@section('title', 'Edit Jadwal Ujian')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-edit text-primary mr-2"></i> Edit Jadwal Ujian
        </h1>
        <a href="{{ route('jadwal-ujian.index') }}" class="btn btn-secondary btn-sm shadow-sm" style="border-radius: 10px; padding: 0.4rem 1rem;">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-edit mr-2"></i> Form Edit Jadwal Ujian
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('jadwal-ujian.update', $jadwal->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Periode Ujian --}}
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold text-dark">Periode Ujian <span class="text-danger">*</span></label>
                        <select name="periode_ujian_id" class="form-control @error('periode_ujian_id') is-invalid @enderror" required>
                            <option value="">Pilih Periode</option>
                            @foreach($periodeUjian as $periode)
                                <option value="{{ $periode->id }}" 
                                    {{ old('periode_ujian_id', $jadwal->periode_ujian_id) == $periode->id ? 'selected' : '' }}>
                                    {{ $periode->nama_periode }} 
                                    ({{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d-m-Y') }} - 
                                    {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d-m-Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('periode_ujian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Mata Pelajaran --}}
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold text-dark">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mapel_id" class="form-control @error('mapel_id') is-invalid @enderror" required>
                            <option value="">Pilih Mapel</option>
                            @foreach($semuaMapel as $m)
                                <option value="{{ $m->id }}" 
                                    {{ old('mapel_id', $jadwal->mapel_id) == $m->id ? 'selected' : '' }}>
                                    {{ $m->kode_mapel }} - {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                        @error('mapel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Tanggal Ujian --}}
                    <div class="col-md-4 form-group">
                        <label class="small font-weight-bold text-dark">Tanggal Ujian <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_ujian" 
                               class="form-control @error('tanggal_ujian') is-invalid @enderror" 
                               value="{{ old('tanggal_ujian', \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('Y-m-d')) }}" 
                               required>
                        @error('tanggal_ujian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Jam Mulai --}}
                    <div class="col-md-4 form-group">
                        <label class="small font-weight-bold text-dark">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" 
                               class="form-control @error('jam_mulai') is-invalid @enderror" 
                               value="{{ old('jam_mulai', \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i')) }}" 
                               required>
                        @error('jam_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Jam Selesai --}}
                    <div class="col-md-4 form-group">
                        <label class="small font-weight-bold text-dark">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_selesai" 
                               class="form-control @error('jam_selesai') is-invalid @enderror" 
                               value="{{ old('jam_selesai', \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i')) }}" 
                               required>
                        @error('jam_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Durasi --}}
                    <div class="col-md-4 form-group">
                        <label class="small font-weight-bold text-dark">Durasi (HH:mm:ss) <span class="text-danger">*</span></label>
                        <input type="time" name="durasi" step="1" 
                               class="form-control @error('durasi') is-invalid @enderror" 
                               value="{{ old('durasi', \Carbon\Carbon::parse($jadwal->durasi)->format('H:i:s')) }}" 
                               required>
                        @error('durasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4 form-group">
                        <label class="small font-weight-bold text-dark">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="aktif" {{ old('status', $jadwal->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $jadwal->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Token (hanya tampil, tidak bisa diubah) --}}
                    <div class="col-md-4 form-group">
                        <label class="small font-weight-bold text-dark">Token Ujian</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-light" value="{{ $jadwal->token }}" readonly disabled>
                            <div class="input-group-append">
                                <span class="input-group-text bg-white text-muted">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                        </div>
                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Token tidak dapat diubah.</small>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="form-group mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm" style="border-radius: 10px; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-save mr-2"></i> Update Jadwal
                    </button>
                    <a href="{{ route('jadwal-ujian.index') }}" class="btn btn-light font-weight-bold px-4" style="border-radius: 10px; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection