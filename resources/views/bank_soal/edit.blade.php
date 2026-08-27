@extends('layouts.app')

@section('title', 'Edit Bank Soal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Bank Soal</h1>
        <a href="{{ route('bank-soal.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Bank Soal</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('bank-soal.update', $bank_soal) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Kode Mapel</label>
                        <input type="text" name="kode_mapel" class="form-control @error('kode_mapel') is-invalid @enderror" value="{{ old('kode_mapel', $bank_soal->kode_mapel) }}" required>
                        @error('kode_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" class="form-control @error('nama_mapel') is-invalid @enderror" value="{{ old('nama_mapel', $bank_soal->nama_mapel) }}" required>
                        @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Tingkat</label>
                        <select name="tingkat_id" class="form-control @error('tingkat_id') is-invalid @enderror" required>
                            <option value="">Pilih Tingkat</option>
                            @foreach($tingkats as $t)
                                <option value="{{ $t->id }}" {{ (old('tingkat_id', $bank_soal->tingkat_id) == $t->id) ? 'selected' : '' }}>{{ $t->nama_tingkat }}</option>
                            @endforeach
                        </select>
                        @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Kompetensi Keahlian (Opsional)</label>
                        <select name="kompetensi_keahlian_id" class="form-control @error('kompetensi_keahlian_id') is-invalid @enderror">
                            <option value="">Pilih Kompetensi Keahlian</option>
                            @foreach($kompetensis as $k)
                                <option value="{{ $k->id }}" {{ (old('kompetensi_keahlian_id', $bank_soal->kompetensi_keahlian_id) == $k->id) ? 'selected' : '' }}>{{ $k->nama_kompetensi }}</option>
                            @endforeach
                        </select>
                        @error('kompetensi_keahlian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow">Update</button>
                <a href="{{ route('bank-soal.index') }}" class="btn btn-light font-weight-bold">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection