@extends('layouts.app')
@section('title', 'Edit Data Kelas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Data Kelas</h1>
        <a href="{{ route('kelas.index') }}" class="btn btn-sm btn-secondary shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-arrow-left fa-sm mr-2"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
                <div class="card-header py-3 bg-white border-0">
                    <h6 class="m-0 font-weight-bold text-primary">Form Perubahan Kelas: {{ $kelas->nama_kelas }}</h6>
                </div>
                
                <form action="{{ route('kelas.update', $kelas->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-dark">Tingkat</label>
                                <select name="tingkat_id" class="form-control @error('tingkat_id') is-invalid @enderror" required>
                                    @foreach($data_tingkat as $t)
                                        <option value="{{ $t->id }}" {{ (old('tingkat_id', $kelas->tingkat_id) == $t->id) ? 'selected' : '' }}>
                                            {{ $t->nama_tingkat }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tingkat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-dark">Kompetensi Keahlian</label>
                                <select name="kompetensi_keahlian_id" class="form-control @error('kompetensi_keahlian_id') is-invalid @enderror" required>
                                    @foreach($data_keahlian as $k)
                                        <option value="{{ $k->id }}" {{ (old('kompetensi_keahlian_id', $kelas->kompetensi_keahlian_id) == $k->id) ? 'selected' : '' }}>
                                            {{ $k->nama_kompetensi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kompetensi_keahlian_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label class="small font-weight-bold text-dark">Nama Kelas</label>
                            <input type="text" name="nama_kelas" class="form-control @error('nama_kelas') is-invalid @enderror" 
                                   value="{{ old('nama_kelas', $kelas->nama_kelas) }}" placeholder="E.g. XII-RPL-1" maxlength="10" required>
                            @error('nama_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted mt-1 d-block font-italic">Contoh penulisan: X-TKR-1 atau XI-RPL-2</small>
                        </div>
                    </div>

                    <div class="card-footer border-0 bg-light p-4 text-right" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                        <button type="reset" class="btn btn-light font-weight-bold px-4 mr-2" style="border-radius: 10px;">Reset</button>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // Menampilkan pesan error jika validasi gagal
        @if(session('error') || $errors->any())
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') ?? 'Gagal memperbarui, periksa kembali inputan Anda.' }}"
            });
        @endif
    });
</script>
@endpush