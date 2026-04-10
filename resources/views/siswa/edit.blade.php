@extends('layouts.app')
@section('title', 'Edit Data Siswa')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Siswa</h1>
        <a href="{{ route('siswa.index') }}" class="btn btn-sm btn-secondary shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-arrow-left fa-sm mr-2"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
                <div class="card-header py-3 bg-white border-0">
                    <h6 class="m-0 font-weight-bold text-primary">Formulir Pembaruan Data</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                    value="{{ old('nama', $siswa->user->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold">Email (Username)</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                    value="{{ old('email', $siswa->user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold">NISN</label>
                                <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" 
                                    value="{{ old('nisn', $siswa->nisn) }}" required>
                                @error('nisn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold">NIS</label>
                                <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" 
                                    value="{{ old('nis', $siswa->nis) }}" required>
                                @error('nis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold">Kelas</label>
                                <select name="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror" required>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ old('kelas_id', $siswa->kelas_id) == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                    <option value="L" {{ old('jenis_kelamin', $siswa->user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $siswa->user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="form-group">
                            <label class="small font-weight-bold text-danger">Password Baru (Opsional)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                placeholder="Kosongkan jika tidak ingin mengubah password">
                            <small class="text-muted italic">Min. 8 karakter jika ingin diubah.</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4 text-right">
                            <button type="reset" class="btn btn-light px-4">Reset</button>
                            <button type="submit" class="btn btn-primary px-4 shadow">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow border-0" style="border-radius: 15px;">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-light"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark">{{ $siswa->user->nama }}</h5>
                    <p class="text-muted small">Terdaftar pada: {{ $siswa->created_at->format('d M Y') }}</p>
                    <hr>
                    <div class="text-left small">
                        <p><strong>Status Akun:</strong> 
                            {!! $siswa->user->status == 'aktif' 
                                ? '<span class="badge badge-success">Aktif</span>' 
                                : '<span class="badge badge-danger">Terblokir</span>' 
                            !!}
                        </p>
                        <p class="mb-0 text-muted italic">* Mengubah email akan mempengaruhi login siswa tersebut.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection