@extends('layouts.master')
@section('title', 'Edit Peserta')
@section('link')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Edit Peserta</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-primary">
                <form action="{{ route('peserta.update', $peserta->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body row">
                        {{-- Nama --}}
                        <div class="form-group col-md-6">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama"
                                class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama', $peserta->user->nama) }}">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $peserta->user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="form-group col-md-6">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin"
                                class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki"
                                    {{ old('jenis_kelamin', $peserta->user->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>
                                <option value="Perempuan"
                                    {{ old('jenis_kelamin', $peserta->user->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan
                                </option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- No Telp --}}
                        <div class="form-group col-md-6">
                            <label>No Telepon</label>
                            <input type="text" name="no_telp"
                                class="form-control @error('no_telp') is-invalid @enderror"
                                value="{{ old('no_telp', $peserta->user->no_telp) }}">
                            @error('no_telp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Password --}}
                        <div class="form-group col-md-6">
                            <label>Password Baru</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Kosongkan jika tidak diubah">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="form-group col-md-6">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                class="form-control"
                                placeholder="Ulangi password baru">
                        </div>
                        {{-- Tempat Lahir --}}
                        <div class="form-group col-md-6">
                            <label>Tempat Lahir</label>
                            <input type="text" name="tempat_lahir"
                                class="form-control @error('tempat_lahir') is-invalid @enderror"
                                value="{{ old('tempat_lahir', $peserta->user->tempat_lahir) }}">
                            @error('tempat_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="form-group col-md-6">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir"
                                class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                value="{{ old('tanggal_lahir', $peserta->user->tanggal_lahir) }}">
                            @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NIS --}}
                        <div class="form-group col-md-6">
                            <label>NIS</label>
                            <input type="text" name="nis"
                                class="form-control @error('nis') is-invalid @enderror"
                                value="{{ old('nis', $peserta->nis) }}">
                            @error('nis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NISN --}}
                        <div class="form-group col-md-6">
                            <label>NISN</label>
                            <input type="text" name="nisn"
                                class="form-control @error('nisn') is-invalid @enderror"
                                value="{{ old('nisn', $peserta->nisn) }}">
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kelas --}}
                        <div class="form-group col-md-6">
                            <label>Kelas</label>
                            <select name="kelas_id"
                                class="form-control @error('kelas_id') is-invalid @enderror">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}"
                                        {{ old('kelas_id', $peserta->kelas_id) == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tahun Ajaran --}}
                        <div class="form-group col-md-6">
                            <label>Tahun Ajaran</label>
                            <select name="tahun_ajaran_id"
                                class="form-control @error('tahun_ajaran_id') is-invalid @enderror">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahun_ajaran as $ta)
                                    <option value="{{ $ta->id }}"
                                        {{ old('tahun_ajaran_id', $peserta->tahun_ajaran_id) == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->nama_tahun_ajaran }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tahun_ajaran_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12">
                            <label for="nama_dudi">Nama DUDI</label>

                            <input type="text"
                                class="form-control @error('dudi_id') is-invalid @enderror"
                                id="nama_dudi"
                                value="{{ $peserta->peserta_pkl->dudi->nama_dudi ?? '' }}">

                            <input type="hidden"
                                name="dudi_id"
                                id="dudi_id"
                                value="{{ $peserta->peserta_pkl->dudi_id ?? '' }}">

                            @error('dudi_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('peserta.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary float-right">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </section>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(function() {
    function setupAutocomplete(selector, hiddenSelector, url) {
        $(selector).autocomplete({
            source: url,
            minLength: 2,
            select: function(event, ui) {
                $(selector).val(ui.item.label);
                $(hiddenSelector).val(ui.item.id);
                return false;
            }
        });
    }

    setupAutocomplete("#nama_peserta", "#peserta_id", "/autocomplete/peserta");
    setupAutocomplete("#nama_dudi", "#dudi_id", "/autocomplete/dudi");
});
</script>
@endsection