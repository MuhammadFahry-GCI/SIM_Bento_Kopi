@extends('layouts.app')
@section('judul', 'Pesanan Tunai')
@section('isi')
<div class="page-heading"><div><span class="eyebrow">Operasional kasir</span><h1>Pesanan baru</h1><p>Pilih menu, metode pembayaran, dan status pembayarannya.</p></div></div>
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('kasir.simpan') }}">@csrf
    <div class="mb-2"><label class="form-label">Meja</label>
        <select name="id_meja" class="form-select" required>
            @foreach($meja as $m)<option value="{{ $m->id_meja }}" @selected(old('id_meja') == $m->id_meja)>{{ $m->nomor_meja }} ({{ $m->area }} · {{ $m->status_meja === 'terisi' ? 'Sedang terisi' : 'Tersedia' }})</option>@endforeach
        </select></div>
    <p class="small text-muted">Pesanan baru menandai meja terisi. Jika meja sudah digunakan, pastikan pesanan tambahan memang untuk pelanggan di meja tersebut.</p>
    @include('partials.menu_inputs')
    <div class="mt-3 mb-2"><label class="form-label">Nama pemesan (opsional)</label>
        <input name="nama_pemesan" value="{{ old('nama_pemesan') }}" class="form-control" maxlength="100"></div>
    <div class="mb-3"><label class="form-label">Catatan (opsional)</label>
        <textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea></div>
    @include('partials.payment_method', ['isKasir' => true])
    <fieldset class="mb-3">
        <legend class="form-label mb-2">Status pembayaran</legend>
        <div class="payment-methods">
            <label class="payment-option" for="status_menunggu">
                <input class="form-check-input" type="radio" name="status_bayar" id="status_menunggu" value="menunggu" @checked(old('status_bayar', 'menunggu') === 'menunggu') required>
                <span class="payment-option-copy"><strong>Belum dibayar</strong><small>Pesanan belum masuk dapur sampai kasir mencatat pembayaran diterima.</small></span>
            </label>
            <label class="payment-option" for="status_berhasil">
                <input class="form-check-input" type="radio" name="status_bayar" id="status_berhasil" value="berhasil" @checked(old('status_bayar') === 'berhasil') required>
                <span class="payment-option-copy"><strong>Sudah lunas</strong><small>Pilih setelah uang tunai diterima atau pembayaran QRIS sudah diverifikasi.</small></span>
            </label>
        </div>
    </fieldset>
    <button class="btn btn-dark">Simpan pesanan</button>
</form>
</div></div>
@endsection
