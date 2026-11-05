@extends('layouts.app')
@section('judul', 'Ubah Artikel Pengetahuan')
@section('isi')
    <div class="page-heading">
        <div><span class="eyebrow">Basis pengetahuan</span><h1>Ubah artikel</h1><p>Perubahan dicatat bersama akun yang memperbarui artikel.</p></div>
        <a href="{{ route('pengetahuan.manage') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('pengetahuan.update', $artikel->id_artikel) }}" class="row g-3">
            @csrf @method('PUT')
            <div class="col-md-7">
                <label class="form-label" for="judul">Judul</label>
                <input id="judul" name="judul" value="{{ old('judul', $artikel->judul) }}" class="form-control" maxlength="150" required>
            </div>
            <div class="col-md-5">
                <label class="form-label" for="kategori">Kelompok</label>
                <select id="kategori" name="kategori" class="form-select" required>
                    @foreach($kategori as $pilihan)
                        <option value="{{ $pilihan }}" @selected(old('kategori', $artikel->kategori) === $pilihan)>{{ $pilihan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label" for="akses">Akses konten</label>
                <select id="akses" name="akses" class="form-select" required>
                    <option value="publik" @selected(old('akses', $artikel->akses) === 'publik')>Publik (pelanggan dan staf)</option>
                    <option value="staf" @selected(old('akses', $artikel->akses) === 'staf')>Internal staf</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label" for="isi">Isi</label>
                <textarea id="isi" name="isi" class="form-control" rows="8" maxlength="20000" required>{{ old('isi', $artikel->isi) }}</textarea>
            </div>
            <div class="col-12"><button class="btn btn-primary">Simpan perubahan</button></div>
        </form>
    </div></div>
@endsection
