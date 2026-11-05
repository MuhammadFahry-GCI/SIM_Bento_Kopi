@extends('layouts.app')
@section('judul', 'Pesan Meja ' . $meja->nomor_meja)
@section('isi')
<div class="page-heading"><div><span class="eyebrow">Pemesanan meja {{ $meja->nomor_meja }}</span><h1>Pesan menu</h1><p>{{ ucfirst($meja->area) }} · Pilih menu dan jumlah pesanan Anda.</p></div></div>
<p class="small text-muted mb-3">Butuh informasi? <a href="{{ route('pengetahuan.index') }}">Baca panduan dan FAQ pelanggan</a>.</p>
@if(auth()->user()?->peran === 'member')
    <p class="small text-muted mb-3">Pesanan ini akan disimpan ke riwayat akun member Anda.</p>
@endif
<form method="POST" action="{{ route('pelanggan.kirim', $meja->nomor_meja) }}">@csrf
    @include('partials.menu_inputs')
    <div class="card mt-3"><div class="card-body">
        <div class="mb-2"><label class="form-label">Nama (opsional)</label>
            <input name="nama_pemesan" value="{{ old('nama_pemesan') }}" class="form-control" maxlength="100"></div>
        <div class="mb-2"><label class="form-label">Catatan (opsional)</label>
            <textarea name="catatan" class="form-control" rows="2" maxlength="500">{{ old('catatan') }}</textarea></div>
        @include('partials.payment_method', ['isKasir' => false])
        <p class="small text-muted">
            Privasi: kami hanya menyimpan pesanan dan pembayaran untuk memproses transaksi. Nama bersifat opsional,
            data tidak dijual, dan hanya diakses staf sesuai perannya.
        </p>
        <button class="btn btn-dark w-100">Konfirmasi pesanan dan pembayaran</button>
    </div></div>
</form>
@endsection
