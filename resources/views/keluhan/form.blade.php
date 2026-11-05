@extends('layouts.app')
@section('judul', 'Sampaikan Keluhan')
@section('isi')
    <div class="page-heading">
        <div><span class="eyebrow">Layanan pelanggan digital</span><h1>Sampaikan keluhan</h1>
            <p>Pesanan {{ $pesanan->nomor_pesanan }} · Meja {{ $pesanan->meja->nomor_meja }}</p></div>
        <a href="{{ route('pelanggan.status', $pesanan->nomor_pesanan) }}" class="btn btn-outline-secondary">Kembali ke status</a>
    </div>
    <div class="card"><div class="card-body">
        <p class="text-muted">Keluhan tersimpan pada data pesanan dan dapat dilacak menggunakan kode tiket setelah dikirim.</p>
        <form method="POST" action="{{ route('keluhan.store', $pesanan->nomor_pesanan) }}" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label" for="kategori">Topik keluhan</label>
                <select id="kategori" name="kategori" class="form-select" required>
                    @foreach($kategori as $value => $label)
                        <option value="{{ $value }}" @selected(old('kategori') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="nama_pelapor">Nama (opsional)</label>
                <input id="nama_pelapor" name="nama_pelapor" value="{{ old('nama_pelapor', $pesanan->nama_pemesan) }}" class="form-control" maxlength="100">
            </div>
            <div class="col-12">
                <label class="form-label" for="deskripsi">Ceritakan kendala yang dialami</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="5" maxlength="2000" required>{{ old('deskripsi') }}</textarea>
            </div>
            <div class="col-md-7">
                <label class="form-label" for="kontak_pelapor">Nomor telepon atau email untuk dihubungi</label>
                <input id="kontak_pelapor" name="kontak_pelapor" value="{{ old('kontak_pelapor') }}" class="form-control" maxlength="50" required>
            </div>
            <div class="col-12"><button class="btn btn-primary">Kirim keluhan</button></div>
        </form>
    </div></div>
@endsection
