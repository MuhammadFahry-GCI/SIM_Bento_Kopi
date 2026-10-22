@extends('layouts.app')
@section('judul', 'Pesan Meja ' . $meja->nomor_meja)
@section('isi')
<h4>Pesan dari Meja {{ $meja->nomor_meja }} <small class="text-muted fs-6">({{ $meja->area }})</small></h4>
<form method="POST" action="{{ route('pelanggan.kirim', $meja->nomor_meja) }}">@csrf
    @include('partials.menu_inputs')
    <div class="card mt-3"><div class="card-body">
        <div class="mb-2"><label class="form-label">Nama (opsional)</label>
            <input name="nama_pemesan" value="{{ old('nama_pemesan') }}" class="form-control" maxlength="100"></div>
        <div class="mb-2"><label class="form-label">Catatan (opsional)</label>
            <textarea name="catatan" class="form-control" rows="2" maxlength="500">{{ old('catatan') }}</textarea></div>
        <div class="mb-2"><label class="form-label">Metode pembayaran</label>
            <select name="metode" class="form-select">
                <option value="qris" @selected(old('metode') === 'qris')>QRIS (simulasi prototipe)</option>
                <option value="tunai" @selected(old('metode') === 'tunai')>Tunai di kasir</option>
            </select></div>
        <p class="small text-muted">
            Privasi: kami hanya menyimpan pesanan dan pembayaran untuk memproses transaksi. Nama bersifat opsional,
            data tidak dijual, dan hanya diakses staf sesuai perannya.
        </p>
        <button class="btn btn-dark w-100">Kirim Pesanan</button>
    </div></div>
</form>
@endsection
