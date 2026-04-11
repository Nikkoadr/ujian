@extends('layouts.app')
@section('title', 'Edit Data Guru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Profil Guru</h1>
        <a href="{{ route('guru.index') }}" class="btn btn-sm btn-secondary shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-arrow-left fa-sm mr-2"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
                <div class="card-header py-3 bg-white border-0">
                    <h6 class="m-0 font-weight-bold text-primary">Form Perubahan Data Guru</h6>
                </div>
                
                <form action="{{ route('guru.update', $guru->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-dark">NIP Guru</label>
                                <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" 
                                       value="{{ old('nip', $guru->nip) }}" required style="border-radius: 8px;">
                                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-dark">Nama Lengkap & Gelar</label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                       value="{{ old('nama', $guru->user->nama) }}" required style="border-radius: 8px;">
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-dark">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="laki-laki" {{ old('jenis_kelamin', $guru->user->jenis_kelamin) == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="perempuan" {{ old('jenis_kelamin', $guru->user->jenis_kelamin) == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-dark">Email (Username)</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $guru->user->email) }}" required style="border-radius: 8px;">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="bg-light p-3 border" style="border-radius: 12px;">
                            <h6 class="font-weight-bold text-dark mb-2 small"><i class="fas fa-lock mr-2"></i>Ganti Password (Kosongkan jika tidak diubah)</h6>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="small text-muted">Password Baru</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                           placeholder="Min. 8 karakter" style="border-radius: 8px;">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="small text-muted">Konfirmasi Password Baru</label>
                                    <input type="password" name="password_confirmation" class="form-control" 
                                           placeholder="Ulangi password" style="border-radius: 8px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer border-0 bg-white p-4 text-right">
                        <button type="submit" class="btn btn-primary font-weight-bold px-5 shadow" style="border-radius: 10px;">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection